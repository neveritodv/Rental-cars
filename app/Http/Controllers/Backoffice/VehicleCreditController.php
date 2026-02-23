<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleCredit;
use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class VehicleCreditController extends Controller
{
    /**
     * Display a listing of vehicle credits.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $agency = $user->agency;
        
        // Charger uniquement les relations qui existent
        $query = VehicleCredit::with(['vehicle', 'agency']);
        
        // Filter by agency for managers
        if ($user->hasRole('manager') && $agency) {
            $query->where('agency_id', $agency->id);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('credit_number', 'like', "%{$search}%")
                  ->orWhere('creditor_name', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function($sub) use ($search) {
                      $sub->where('registration_number', 'like', "%{$search}%");
                  });
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Creditor filter
        if ($request->filled('creditor')) {
            $query->where('creditor_name', 'like', "%{$request->creditor}%");
        }
        
        // Amount range filter
        if ($request->filled('amount_min')) {
            $query->where('total_amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('total_amount', '<=', $request->amount_max);
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }
        
        // Sorting
        switch ($request->get('sort')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'amount_asc':
                $query->orderBy('total_amount', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'remaining_asc':
                $query->orderBy('remaining_amount', 'asc');
                break;
            case 'remaining_desc':
                $query->orderBy('remaining_amount', 'desc');
                break;
            default:
                $query->latest();
                break;
        }
        
        $credits = $query->paginate(15)->withQueryString();
        
        // Get available creditors for filter dropdown
        $availableCreditors = VehicleCredit::where('agency_id', $agency->id)
            ->distinct()
            ->pluck('creditor_name')
            ->filter()
            ->values()
            ->toArray();
        
        $vehicles = Vehicle::where('agency_id', $agency->id)->get();
        
        return view('Backoffice.vehicle-credits.index', compact('credits', 'availableCreditors', 'vehicles'));
    }

    /**
     * Show the form for creating a new credit - Returns the modal view
     */
    public function create(): View
    {
        $user = auth()->user();
        $agency = $user->agency;
        
        $vehicles = Vehicle::where('agency_id', $agency->id)
            ->orderBy('registration_number')
            ->get();
        
        return view('Backoffice.vehicle-credits.partials._modal_create', compact('vehicles'));
    }

    /**
     * Store a newly created credit in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $agency = $user->agency;
        
        // Nettoyer les entrées avant validation
        $input = $request->all();
        
        // Supprimer les zéros non significatifs et convertir en nombres
        $input['down_payment'] = !empty($input['down_payment']) ? (float)ltrim($input['down_payment'], '0') : 0;
        $input['interest_rate'] = !empty($input['interest_rate']) ? (float)ltrim($input['interest_rate'], '0') : 0;
        $input['duration_months'] = (int)$input['duration_months'];
        $input['total_amount'] = (float)$input['total_amount'];
        $input['monthly_payment'] = (float)$input['monthly_payment'];
        
        $request->merge($input);
        
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'creditor_name' => 'required|string|max:150',
            'total_amount' => 'required|numeric|min:0',
            'down_payment' => 'nullable|numeric|min:0',
            'monthly_payment' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1|max:120',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Set default values
        $validated['down_payment'] = $validated['down_payment'] ?? 0;
        $validated['interest_rate'] = $validated['interest_rate'] ?? 0;
        $validated['agency_id'] = $agency->id;
        
        // Generate unique credit number with retry logic
        $creditNumber = $this->generateUniqueCreditNumber();
        $validated['credit_number'] = $creditNumber;
        
        // Handle file upload
        if ($request->hasFile('contract_file')) {
            $path = $request->file('contract_file')->store('credit-contracts', 'public');
            $validated['contract_file'] = $path;
        }
        
        $credit = VehicleCredit::create($validated);
        
        // Generate payment schedule
        $payments = $credit->generatePaymentSchedule();
        foreach ($payments as $paymentData) {
            $credit->payments()->create($paymentData);
        }
        
        return redirect()->route('backoffice.vehicle-credits.index')
            ->with('success', 'Crédit créé avec succès');
    }

    /**
     * Generate a unique credit number
     */
    private function generateUniqueCreditNumber()
    {
        $year = date('Y');
        $maxAttempts = 100;
        $attempt = 0;
        
        do {
            // Utiliser une transaction pour éviter les conditions de course
            try {
                DB::beginTransaction();
                
                // Obtenir le dernier numéro de séquence pour l'année
                $lastCredit = VehicleCredit::withTrashed()
                    ->whereYear('created_at', $year)
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();
                
                if ($lastCredit && preg_match('/CRD-' . $year . '-(\d{4})/', $lastCredit->credit_number, $matches)) {
                    $lastSequence = (int)$matches[1];
                    $sequence = $lastSequence + 1;
                } else {
                    // Compter le nombre total de crédits pour l'année
                    $count = VehicleCredit::withTrashed()
                        ->whereYear('created_at', $year)
                        ->count();
                    $sequence = $count + 1;
                }
                
                $creditNumber = 'CRD-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                
                // Vérifier si ce numéro existe déjà (sécurité supplémentaire)
                $exists = VehicleCredit::withTrashed()
                    ->where('credit_number', $creditNumber)
                    ->exists();
                
                DB::commit();
                
                if (!$exists) {
                    return $creditNumber;
                }
                
            } catch (\Exception $e) {
                DB::rollBack();
            }
            
            $attempt++;
            
            if ($attempt >= $maxAttempts) {
                // Fallback: utiliser timestamp + random
                $creditNumber = 'CRD-' . $year . '-' . date('His') . '-' . rand(1000, 9999);
                
                // Vérifier une dernière fois
                $exists = VehicleCredit::withTrashed()
                    ->where('credit_number', $creditNumber)
                    ->exists();
                    
                if (!$exists) {
                    return $creditNumber;
                }
            }
            
        } while ($attempt < $maxAttempts);
        
        // Dernier recours: utiliser uniqid
        return 'CRD-' . $year . '-' . uniqid();
    }

    /**
     * Display the specified credit.
     */
    public function show(VehicleCredit $vehicleCredit): View
    {
        // Charger uniquement les relations qui existent
        $vehicleCredit->load(['vehicle', 'payments' => function($q) {
            $q->orderBy('payment_number');
        }]);
        
        return view('Backoffice.vehicle-credits.show', [
            'credit' => $vehicleCredit
        ]);
    }

    /**
     * Show the form for editing the specified credit - Returns the modal view
     */
    public function edit(VehicleCredit $vehicleCredit): View
    {
        $user = auth()->user();
        $agency = $user->agency;
        
        $vehicles = Vehicle::where('agency_id', $agency->id)
            ->orderBy('registration_number')
            ->get();
        
        return view('Backoffice.vehicle-credits.partials._modal_edit', [
            'credit' => $vehicleCredit,
            'vehicles' => $vehicles
        ]);
    }

    /**
     * Update the specified credit in storage.
     */
    public function update(Request $request, VehicleCredit $vehicleCredit)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'creditor_name' => 'required|string|max:150',
            'status' => 'required|in:active,completed,defaulted,pending',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('contract_file')) {
            // Delete old file if exists
            if ($vehicleCredit->contract_file) {
                Storage::disk('public')->delete($vehicleCredit->contract_file);
            }
            $path = $request->file('contract_file')->store('credit-contracts', 'public');
            $validated['contract_file'] = $path;
        }
        
        $vehicleCredit->update($validated);
        
        return redirect()->route('backoffice.vehicle-credits.show', $vehicleCredit)
            ->with('success', 'Crédit mis à jour avec succès');
    }

    /**
     * Remove the specified credit from storage.
     */
    public function destroy(VehicleCredit $vehicleCredit)
    {
        // Delete contract file if exists
        if ($vehicleCredit->contract_file) {
            Storage::disk('public')->delete($vehicleCredit->contract_file);
        }
        
        $vehicleCredit->delete();
        
        return redirect()->route('backoffice.vehicle-credits.index')
            ->with('success', 'Crédit déplacé vers la corbeille');
    }

    /**
     * Record a payment for a credit.
     */
    public function recordPayment(Request $request, VehicleCredit $vehicleCredit)
    {
        $validated = $request->validate([
            'payment_number' => 'required|integer',
            'paid_date' => 'required|date',
            'penalty' => 'nullable|numeric|min:0',
        ]);

        $payment = $vehicleCredit->payments()
            ->where('payment_number', $validated['payment_number'])
            ->where('status', 'pending')
            ->firstOrFail();
        
        $payment->update([
            'status' => 'paid',
            'paid_date' => $validated['paid_date'],
            'penalty' => $validated['penalty'] ?? 0,
        ]);
        
        // Update credit remaining amount
        $vehicleCredit->updateRemainingAmount();
        
        return redirect()->back()->with('success', 'Paiement enregistré avec succès');
    }

    /**
     * Get payment schedule for a credit (API).
     */
    public function getPaymentSchedule(VehicleCredit $vehicleCredit)
    {
        $payments = $vehicleCredit->payments()
            ->orderBy('payment_number')
            ->get()
            ->map(function($payment) {
                return [
                    'number' => $payment->payment_number,
                    'due_date' => $payment->due_date->format('d/m/Y'),
                    'amount' => number_format($payment->amount, 2) . ' DH',
                    'principal' => number_format($payment->principal, 2) . ' DH',
                    'interest' => number_format($payment->interest, 2) . ' DH',
                    'status' => $payment->status,
                    'status_text' => $payment->status === 'paid' ? 'Payé' : ($payment->status === 'late' ? 'En retard' : 'En attente'),
                    'paid_date' => $payment->paid_date ? $payment->paid_date->format('d/m/Y') : '-',
                ];
            });
        
        return response()->json($payments);
    }

    /**
     * Dashboard with credit statistics.
     */
    public function dashboard(): View
    {
        $user = auth()->user();
        $agency = $user->agency;
        
        $stats = [
            'total_credits' => VehicleCredit::where('agency_id', $agency->id)->count(),
            'active_credits' => VehicleCredit::where('agency_id', $agency->id)->where('status', 'active')->count(),
            'completed_credits' => VehicleCredit::where('agency_id', $agency->id)->where('status', 'completed')->count(),
            'defaulted_credits' => VehicleCredit::where('agency_id', $agency->id)->where('status', 'defaulted')->count(),
            'total_amount' => VehicleCredit::where('agency_id', $agency->id)->sum('total_amount'),
            'total_remaining' => VehicleCredit::where('agency_id', $agency->id)->sum('remaining_amount'),
            'total_paid' => CreditPayment::whereHas('credit', fn($q) => $q->where('agency_id', $agency->id))
                ->where('status', 'paid')
                ->sum('amount'),
            'late_payments' => CreditPayment::whereHas('credit', fn($q) => $q->where('agency_id', $agency->id)->where('status', 'active'))
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->count(),
        ];
        
        $recentPayments = CreditPayment::with(['credit.vehicle'])
            ->whereHas('credit', fn($q) => $q->where('agency_id', $agency->id))
            ->where('status', 'paid')
            ->latest('paid_date')
            ->limit(10)
            ->get();
        
        $expiringSoon = VehicleCredit::with('vehicle')
            ->where('agency_id', $agency->id)
            ->where('status', 'active')
            ->whereHas('payments', function($q) {
                $q->where('status', 'pending')
                  ->where('due_date', '>=', now())
                  ->where('due_date', '<=', now()->addDays(15));
            })
            ->limit(5)
            ->get();
        
        return view('Backoffice.vehicle-credits.dashboard', compact('stats', 'recentPayments', 'expiringSoon'));
    }

    /**
     * Display trashed credits.
     */
    public function trashed(Request $request): View
    {
        $user = auth()->user();
        $agency = $user->agency;
        
        $query = VehicleCredit::onlyTrashed()->with(['vehicle', 'agency']);
        
        if ($user->hasRole('manager') && $agency) {
            $query->where('agency_id', $agency->id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('credit_number', 'like', "%{$search}%")
                  ->orWhere('creditor_name', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function($sub) use ($search) {
                      $sub->where('registration_number', 'like', "%{$search}%");
                  });
            });
        }
        
        $credits = $query->paginate(15)->withQueryString();
        
        return view('Backoffice.vehicle-credits.trashed', compact('credits'));
    }

    /**
     * Restore a soft deleted credit.
     */
    public function restore($id)
    {
        $credit = VehicleCredit::onlyTrashed()->findOrFail($id);
        $credit->restore();
        
        return redirect()->route('backoffice.vehicle-credits.trashed')
            ->with('success', 'Crédit restauré avec succès');
    }

    /**
     * Permanently delete a credit.
     */
    public function forceDelete($id)
    {
        $credit = VehicleCredit::onlyTrashed()->findOrFail($id);
        
        // Delete contract file if exists
        if ($credit->contract_file) {
            Storage::disk('public')->delete($credit->contract_file);
        }
        
        // Delete related payments
        $credit->payments()->forceDelete();
        
        // Force delete the credit
        $credit->forceDelete();
        
        return redirect()->route('backoffice.vehicle-credits.trashed')
            ->with('success', 'Crédit supprimé définitivement');
    }
}