<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Http\Requests\Backoffice\InvoiceItem\InvoiceItemStoreRequest;
use App\Http\Requests\Backoffice\InvoiceItem\InvoiceItemUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceItemController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of invoice items (standalone).
     */
    public function index(Request $request)
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = InvoiceItem::with(['invoice.client'])
            ->whereHas('invoice', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function ($sub) use ($search) {
                      $sub->where('invoice_number', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by invoice
        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'description_asc') {
            $query->orderBy('description', 'asc');
        } elseif ($sort === 'description_desc') {
            $query->orderBy('description', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('total_ttc', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('total_ttc', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $items = $query->paginate(15)->withQueryString();

        // Get invoices for filter
        $invoices = Invoice::where('agency_id', $agencyId)->orderBy('invoice_number')->get();

        return view('backoffice.invoice-items.index', compact('items', 'invoices'));
    }

    /**
     * Show the form for creating a new invoice item.
     */
    public function create()
    {
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        $invoices = Invoice::where('agency_id', $agencyId)->orderBy('invoice_number')->get();
        
        return view('backoffice.invoice-items.partials._modal_create', compact('invoices'));
    }

    /**
     * Store a newly created invoice item.
     */
    public function store(InvoiceItemStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Recalculate totals to ensure they're correct
            $qty = floatval($data['quantity']);
            $price = floatval($data['unit_price_ttc'] ?? 0);
            $vat = floatval($data['vat_rate'] ?? 0);
            
            $ttc = $qty * $price;
            $vatMultiplier = 1 + ($vat / 100);
            $ht = $vat > 0 ? $ttc / $vatMultiplier : $ttc;
            
            // Override the submitted values with calculated ones
            $data['total_ttc'] = round($ttc, 2);
            $data['total_ht'] = round($ht, 2);

            // Ensure invoice_id is present
            if (!isset($data['invoice_id'])) {
                throw new \Exception('L\'ID de la facture est requis.');
            }

            // Create the invoice item
            $invoiceItem = InvoiceItem::create([
                'invoice_id' => $data['invoice_id'],
                'description' => $data['description'],
                'days_count' => $data['days_count'] ?? null,
                'unit_price_ttc' => $data['unit_price_ttc'] ?? null,
                'quantity' => $data['quantity'],
                'total_ttc' => $data['total_ttc'],
                'total_ht' => $data['total_ht'],
                'vat_rate' => $data['vat_rate'] ?? null,
            ]);

            // Get fresh invoice with items loaded
            $invoice = Invoice::with('items')->find($data['invoice_id']);
            
            // Update invoice totals
            $this->updateInvoiceTotals($invoice);
            
            // FIXED: Use correct module name 'invoice-item' and the actual invoiceItem object
            $this->createNotification('store', 'invoice-item', $invoiceItem);

            DB::commit();

            return redirect()
                ->route('backoffice.invoice-items.index')
                ->with('toast', [
                    'title' => 'Ajouté',
                    'message' => 'Ligne de facture ajoutée avec succès.',
                    'dot' => '#198754',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Display the specified invoice item.
     */
    public function show(InvoiceItem $invoiceItem)
    {
        // Check if the item belongs to the user's agency
        if ($invoiceItem->invoice->agency_id !== Auth::guard('backoffice')->user()->agency_id) {
            abort(403);
        }

        $invoiceItem->load(['invoice.client']);

        return view('backoffice.invoice-items.show', compact('invoiceItem'));
    }

    /**
     * Show the form for editing the specified invoice item.
     */
    public function edit(InvoiceItem $invoiceItem)
    {
        if ($invoiceItem->invoice->agency_id !== Auth::guard('backoffice')->user()->agency_id) {
            abort(403);
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        $invoices = Invoice::where('agency_id', $agencyId)->orderBy('invoice_number')->get();

        return view('backoffice.invoice-items.partials._modal_edit', compact('invoiceItem', 'invoices'));
    }

    /**
     * Update the specified invoice item.
     */
    public function update(InvoiceItemUpdateRequest $request, InvoiceItem $invoiceItem)
    {
        if ($invoiceItem->invoice->agency_id !== Auth::guard('backoffice')->user()->agency_id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            $oldInvoiceId = $invoiceItem->invoice_id;

            // Recalculate totals
            $qty = floatval($data['quantity']);
            $price = floatval($data['unit_price_ttc'] ?? 0);
            $vat = floatval($data['vat_rate'] ?? 0);
            
            $ttc = $qty * $price;
            $vatMultiplier = 1 + ($vat / 100);
            $ht = $vat > 0 ? $ttc / $vatMultiplier : $ttc;
            
            $data['total_ttc'] = round($ttc, 2);
            $data['total_ht'] = round($ht, 2);

            // Update the invoice item
            $invoiceItem->update([
                'invoice_id' => $data['invoice_id'],
                'description' => $data['description'],
                'days_count' => $data['days_count'] ?? null,
                'unit_price_ttc' => $data['unit_price_ttc'] ?? null,
                'quantity' => $data['quantity'],
                'total_ttc' => $data['total_ttc'],
                'total_ht' => $data['total_ht'],
                'vat_rate' => $data['vat_rate'] ?? null,
            ]);

            // Update totals for old invoice
            $oldInvoice = Invoice::with('items')->find($oldInvoiceId);
            $this->updateInvoiceTotals($oldInvoice);
            
            // Update totals for new invoice if different
            if ($oldInvoiceId != $data['invoice_id']) {
                $newInvoice = Invoice::with('items')->find($data['invoice_id']);
                $this->updateInvoiceTotals($newInvoice);
            }
            
            // ADDED: Create notification for update
            $this->createNotification('update', 'invoice-item', $invoiceItem);

            DB::commit();

            return redirect()
                ->route('backoffice.invoice-items.index')
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Ligne de facture mise à jour avec succès.',
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
     * Remove the specified invoice item.
     */
    public function destroy(InvoiceItem $invoiceItem)
    {
        if ($invoiceItem->invoice->agency_id !== Auth::guard('backoffice')->user()->agency_id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $invoiceId = $invoiceItem->invoice_id;
            
            // Store invoice item data for notification before delete
            $invoiceItemData = clone $invoiceItem;
            $invoiceItem->delete();
 $item->delete();
            // Get fresh invoice with items loaded
            $invoice = Invoice::with('items')->find($invoiceId);
            
            // Update invoice totals
            $this->updateInvoiceTotals($invoice);
            
            // ADDED: Create notification for delete
            $this->createNotification('destroy', 'invoice-item', $invoiceItemData);

            DB::commit();

            return redirect()
                ->route('backoffice.invoice-items.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Ligne de facture supprimée avec succès.',
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
     * Update invoice totals based on items.
     */
    private function updateInvoiceTotals(Invoice $invoice): void
    {
        if (!$invoice) return;

        // Make sure we have the items loaded
        if (!$invoice->relationLoaded('items')) {
            $invoice->load('items');
        }
        
        $items = $invoice->items;

        $totalHt = $items->sum('total_ht');
        $totalVat = $items->sum(function ($item) {
            return $item->total_ttc - $item->total_ht;
        });
        $totalTtc = $items->sum('total_ttc');

        $invoice->update([
            'total_ht' => $totalHt,
            'total_vat' => $totalVat,
            'total_ttc' => $totalTtc,
        ]);
    }
}