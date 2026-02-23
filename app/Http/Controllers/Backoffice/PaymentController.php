<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\RentalContract;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Http\Requests\Backoffice\Payment\PaymentStoreRequest;
use App\Http\Requests\Backoffice\Payment\PaymentUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = Payment::where('agency_id', $agencyId)
            ->with(['invoice', 'rentalContract', 'financialAccount']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function ($sub) use ($search) {
                      $sub->where('invoice_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('rentalContract', function ($sub) use ($search) {
                      $sub->where('contract_number', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by account
        if ($request->filled('financial_account_id')) {
            $query->where('financial_account_id', $request->financial_account_id);
        }

        // Filter by invoice
        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'date_asc') {
            $query->orderBy('payment_date', 'asc');
        } elseif ($sort === 'date_desc') {
            $query->orderBy('payment_date', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('amount', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $payments = $query->paginate(15)->withQueryString();

        // Get accounts for filter
        $accounts = FinancialAccount::where('agency_id', $agencyId)->orderBy('name')->get();
        $invoices = Invoice::where('agency_id', $agencyId)->orderBy('invoice_number')->get();

        return view('backoffice.payments.index', compact('payments', 'accounts', 'invoices'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $accounts = FinancialAccount::where('agency_id', $agencyId)->orderBy('name')->get();
        $invoices = Invoice::where('agency_id', $agencyId)->orderBy('invoice_number')->get();
        $contracts = RentalContract::where('agency_id', $agencyId)->orderBy('contract_number')->get();

        // Pre-select invoice if provided
        $selectedInvoice = null;
        if ($request->has('invoice_id')) {
            $selectedInvoice = Invoice::find($request->invoice_id);
        }

        return view('backoffice.payments.partials._modal_create', compact('accounts', 'invoices', 'contracts', 'selectedInvoice'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(PaymentStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;

            // Create payment
            $payment = Payment::create($data);

            // Create financial transaction if needed
            if ($payment->status === 'confirmed') {
                $this->createFinancialTransaction($payment);
            }

            // Update invoice status if payment is for an invoice
            if ($payment->invoice_id && $payment->status === 'confirmed') {
                $this->updateInvoiceStatus($payment->invoice);
            }
            
            // FIXED: Use correct module name 'payment' and the actual payment object
            $this->createNotification('store', 'payment', $payment);

            DB::commit();

            return redirect()
                ->route('backoffice.payments.show', $payment)
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Paiement créé avec succès.',
                    'dot' => '#198754',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la création: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        //$this->authorize('view', $payment);

        $payment->load(['invoice', 'rentalContract', 'financialAccount', 'financialTransaction']);

        return view('backoffice.payments.partials.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        //$this->authorize('update', $payment);

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $accounts = FinancialAccount::where('agency_id', $agencyId)->orderBy('name')->get();
        $invoices = Invoice::where('agency_id', $agencyId)->orderBy('invoice_number')->get();
        $contracts = RentalContract::where('agency_id', $agencyId)->orderBy('contract_number')->get();

        return view('backoffice.payments.partials._modal_edit', compact('payment', 'accounts', 'invoices', 'contracts'));
    }

    /**
     * Update the specified payment.
     */
    public function update(PaymentUpdateRequest $request, Payment $payment)
    {
        //$this->authorize('update', $payment);

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Delete old financial transaction if exists
            if ($payment->financialTransaction) {
                $payment->financialTransaction->delete();
            }

            $payment->update($data);

            // Create new financial transaction if status is confirmed
            if ($payment->status === 'confirmed') {
                $this->createFinancialTransaction($payment);
            }

            // Update invoice status
            if ($payment->invoice_id) {
                $this->updateInvoiceStatus($payment->invoice);
            }
            
            // ADDED: Create notification for update
            $this->createNotification('update', 'payment', $payment);

            DB::commit();

            return redirect()
                ->route('backoffice.payments.show', $payment)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Paiement mis à jour avec succès.',
                    'dot' => '#0d6efd',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Payment $payment)
    {
        //$this->authorize('delete', $payment);

        try {
            DB::beginTransaction();

            // Store payment data for notification before delete
            $paymentData = clone $payment;

            // Delete financial transaction if exists
            if ($payment->financialTransaction) {
                $payment->financialTransaction->delete();
            }
 $item->delete();
            $payment->delete();

            // Update invoice status if needed
            if ($payment->invoice_id) {
                $this->updateInvoiceStatus($payment->invoice);
            }
            
            // ADDED: Create notification for delete
            $this->createNotification('destroy', 'payment', $paymentData);

            DB::commit();

            return redirect()
                ->route('backoffice.payments.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Paiement supprimé avec succès.',
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Create financial transaction for payment.
     */
    private function createFinancialTransaction(Payment $payment): void
    {
        $transaction = FinancialTransaction::create([
            'agency_id' => $payment->agency_id,
            'financial_account_id' => $payment->financial_account_id,
            'date' => $payment->payment_date,
            'amount' => $payment->amount,
            'type' => 'income',
            'description' => 'Paiement ' . ($payment->invoice ? 'facture ' . $payment->invoice->invoice_number : ''),
            'reference' => $payment->reference,
            'currency' => $payment->currency,
            'created_by' => Auth::guard('backoffice')->id(),
        ]);

        $payment->update(['financial_transaction_id' => $transaction->id]);
    }

    /**
     * Update invoice status based on payments.
     */
    private function updateInvoiceStatus(?Invoice $invoice): void
    {
        if (!$invoice) return;

        $totalPaid = $invoice->payments()
            ->where('status', 'confirmed')
            ->sum('amount');

        if ($totalPaid >= $invoice->total_ttc) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partially_paid']);
        } else {
            $invoice->update(['status' => 'sent']);
        }
    }
}