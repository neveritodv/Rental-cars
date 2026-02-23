<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\RentalContract;
use App\Http\Requests\Backoffice\Invoice\InvoiceStoreRequest;
use App\Http\Requests\Backoffice\Invoice\InvoiceUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = Invoice::where('agency_id', $agencyId)
            ->with(['client', 'rentalContract']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($sub) use ($search) {
                      $sub->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'date_asc') {
            $query->orderBy('invoice_date', 'asc');
        } elseif ($sort === 'date_desc') {
            $query->orderBy('invoice_date', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('total_ttc', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('total_ttc', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Get clients for filter
        $clients = Client::where('agency_id', $agencyId)->orderBy('first_name')->get();

        return view('backoffice.invoices.index', compact('invoices', 'clients'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $clients = Client::where('agency_id', $agencyId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $contracts = RentalContract::where('agency_id', $agencyId)
            ->orderBy('contract_number')
            ->get();

        $invoiceNumber = Invoice::generateInvoiceNumber();

        return view('backoffice.invoices.partials._modal_create', compact('clients', 'contracts', 'invoiceNumber'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(InvoiceStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;
            $data['invoice_number'] = Invoice::generateInvoiceNumber();

            // Calculate VAT and TTC
            $data['total_vat'] = $data['total_ht'] * ($data['vat_rate'] / 100);
            $data['total_ttc'] = $data['total_ht'] + $data['total_vat'];

            $invoice = Invoice::create($data);
            
            // FIXED: Use correct module name 'invoice' and the actual invoice object
            $this->createNotification('store', 'invoice', $invoice);
            
            DB::commit();

            return redirect()
                ->route('backoffice.invoices.index')
                ->with('toast', [
                    'title' => 'Créée',
                    'message' => 'Facture créée avec succès.',
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
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        //$this->authorize('view', $invoice);

        $invoice->load(['client', 'rentalContract', 'agency']);

        return view('backoffice.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $clients = Client::where('agency_id', $agencyId)->orderBy('first_name')->get();
        $contracts = RentalContract::where('agency_id', $agencyId)->orderBy('contract_number')->get();

        return view('backoffice.invoices.partials._modal_edit', compact('invoice', 'clients', 'contracts'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(InvoiceUpdateRequest $request, Invoice $invoice)
    {
        //$this->authorize('update', $invoice);

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Recalculate VAT and TTC
            $data['total_vat'] = $data['total_ht'] * ($data['vat_rate'] / 100);
            $data['total_ttc'] = $data['total_ht'] + $data['total_vat'];

            $invoice->update($data);
            
            // ADDED: Create notification for update
            $this->createNotification('update', 'invoice', $invoice);

            DB::commit();

            return redirect()
                ->route('backoffice.invoices.show', $invoice)
                ->with('toast', [
                    'title' => 'Mise à jour',
                    'message' => 'Facture mise à jour avec succès.',
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
     * Remove the specified invoice.
     */
    public function destroy(Invoice $invoice)
    {
        //$this->authorize('delete', $invoice);

        try {
            DB::beginTransaction();
             $item->delete();
            // Store invoice data for notification before delete
            $invoiceData = clone $invoice;
            $invoice->delete();
            
            // ADDED: Create notification for delete
            $this->createNotification('destroy', 'invoice', $invoiceData);
            
            DB::commit();

            return redirect()
                ->route('backoffice.invoices.index')
                ->with('toast', [
                    'title' => 'Supprimée',
                    'message' => 'Facture supprimée avec succès.',
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
     * Update invoice status.
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        //$this->authorize('update', $invoice);

        $request->validate([
            'status' => ['required', 'in:draft,sent,paid,partially_paid,cancelled'],
        ]);

        try {
            $oldStatus = $invoice->status;
            $invoice->update(['status' => $request->status]);
            
            // ADDED: Create notification for status change
            $this->createNotification('status', 'invoice', $invoice);

            return redirect()
                ->route('backoffice.invoices.show', $invoice)
                ->with('toast', [
                    'title' => 'Statut mis à jour',
                    'message' => 'Le statut de la facture a été mis à jour.',
                    'dot' => '#0d6efd',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la mise à jour du statut: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }
}