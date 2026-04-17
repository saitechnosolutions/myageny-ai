<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuotationCollection;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuotationController extends Controller
{
    // ── List ──────────────────────────────────────────────────────────────────

    /**
     * List all quotations (optionally filtered by lead).
     */
    public function index(Request $request): View
    {
        $query = Quotation::with(['lead', 'approver'])
            ->latest();

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->lead_id);
        }

        $quotations = $query->paginate(15)->withQueryString();

        return view('pages.quotations.index', compact('quotations'));
    }

    // ── Create ────────────────────────────────────────────────────────────────

    /**
     * Show create form, optionally pre-selecting a lead.
     */
    public function create($leadId): View
    {

        $leads    = Lead::orderBy('contact_name')->get(['id', 'contact_name', 'company_name']);
        $products = Product::orderBy('product_name')->get(['id', 'product_name', 'description', 'final_price', 'base_price']);

        $selectedLeadId = $leadId;

        $defaults = [
            'quotation_no'   => Quotation::generateQuotationNo(),
            'quotation_date' => now()->toDateString(),
            'valid_until'    => now()->addDays(7)->toDateString(),
            'tax'            => 0,
        ];

        return view('pages.quotations.create', compact('leads', 'products', 'selectedLeadId', 'defaults'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    /**
     * Persist quotation + items inside a DB transaction.
     */
    public function store(Request $request): RedirectResponse
    {

        $validated = $request->validate([
            'lead_id'        => ['required'],
            'quotation_date' => ['required', 'date'],
            'valid_until'    => ['required', 'date', 'after_or_equal:quotation_date'],

            'notes'          => ['nullable', 'string'],

            // Line items
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'exists:products,id'],
            'items.*.description'    => ['nullable', 'string'],
            'items.*.qty'            => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'     => ['required', 'numeric', 'min:0'],
            'items.*.discount'       => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 1. Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $rowTotal  = ($item['qty'] * $item['unit_price']) - $item['discount'];
                $subtotal += max($rowTotal, 0);
            }

            $taxAmount   = round($subtotal * (18 / 100), 2);
            $totalAmount = round($subtotal + $taxAmount, 2);

            // 2. Create quotation
            $quotation = Quotation::create([
                'quotation_no'   => Quotation::generateQuotationNo(),
                'quotation_date' => $validated['quotation_date'],
                'valid_until'    => $validated['valid_until'],
                'tax'            => 18,
                'subtotal'       => $subtotal,
                'tax_amount'     => $taxAmount,
                'total_amount'   => $totalAmount,
                'lead_id'        => $validated['lead_id'],
                'notes'          => $validated['notes'] ?? null,
                'is_approved'    => false,
                'created_by'    => Auth::user()->id,
                'bill_to_address'    => $request->bill_to_address,
                'ship_to_address'    => $request->ship_to_address,
            ]);

            // 3. Save line items
            foreach ($validated['items'] as $item) {
                $rowTotal = max(($item['qty'] * $item['unit_price']) - $item['discount'], 0);

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id'   => $item['product_id'],
                    'description'  => $item['description'] ?? null,
                    'qty'          => $item['qty'],
                    'unit_price'   => $item['unit_price'],
                    'discount'     => $item['discount'],
                    'total'        => $rowTotal,
                ]);
            }
        });

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation created successfully.');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Quotation $quotation): View
    {
        $quotation->load(['lead', 'items.product', 'approver']);

        return view('pages.quotations.show', compact('quotation'));
    }

    // ── Approve ───────────────────────────────────────────────────────────────

    public function approve(Quotation $quotation): RedirectResponse
    {
        $quotation->update([
            'is_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Quotation approved successfully.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $quotation->delete(); // items cascade via FK

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted.');
    }

    // ── API: Products for Select2 ─────────────────────────────────────────────

    /**
     * Return products as JSON (used by Select2 AJAX if preferred).
     */
    public function productsApi(Request $request): JsonResponse
    {
        $products = Product::when($request->filled('q'), function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->q . '%');
        })
            ->limit(30)
            ->get(['id', 'name', 'description', 'price']);

        return response()->json($products);
    }

    // ── API: Quotation list for mobile ────────────────────────────────────────

    public function apiIndex(Request $request): JsonResponse
    {
        $quotations = Quotation::with(['lead:id,name', 'approver:id,name'])
            ->when($request->filled('lead_id'), fn($q) => $q->where('lead_id', $request->lead_id))
            ->latest()
            ->paginate(20);

        return response()->json($quotations);
    }

    public function apiStore(Request $request): JsonResponse
    {
        // Reuse the same store logic via a shared private method
        // For brevity, delegate to store() after faking redirect
        $request->validate([
            'lead_id'        => ['required', 'exists:leads,id'],
            'quotation_date' => ['required', 'date'],
            'valid_until'    => ['required', 'date'],
            'tax'            => ['required', 'numeric'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'exists:products,id'],
            'items.*.qty'         => ['required', 'numeric'],
            'items.*.unit_price'  => ['required', 'numeric'],
            'items.*.discount'    => ['required', 'numeric'],
        ]);

        $quotation = null;

        DB::transaction(function () use ($request, &$quotation) {
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += max(($item['qty'] * $item['unit_price']) - $item['discount'], 0);
            }

            $taxAmount   = round($subtotal * ($request->tax / 100), 2);
            $totalAmount = round($subtotal + $taxAmount, 2);

            $quotation = Quotation::create([
                'quotation_no'   => Quotation::generateQuotationNo(),
                'quotation_date' => $request->quotation_date,
                'valid_until'    => $request->valid_until,
                'tax'            => $request->tax,
                'subtotal'       => $subtotal,
                'tax_amount'     => $taxAmount,
                'total_amount'   => $totalAmount,
                'lead_id'        => $request->lead_id,
                'notes'          => $request->notes,
                'is_approved'    => false,
            ]);

            foreach ($request->items as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id'   => $item['product_id'],
                    'description'  => $item['description'] ?? null,
                    'qty'          => $item['qty'],
                    'unit_price'   => $item['unit_price'],
                    'discount'     => $item['discount'],
                    'total'        => max(($item['qty'] * $item['unit_price']) - $item['discount'], 0),
                ]);
            }
        });

        return response()->json([
            'message'   => 'Quotation created.',
            'quotation' => $quotation->load('items'),
        ], 201);
    }

    public function downloadPdf($id)
    {
        $quotation = Quotation::with('items', 'createdBy')->findOrFail($id);

        $pdf = Pdf::loadView('pages.quotations.quotation_format_1', compact('quotation'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'defaultFont'  => 'DejaVu Sans',
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled'      => true,
                  ]);

        $filename = 'QT-' . str_pad($quotation->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->stream($filename);
    }

    public function getLeadQuotations($leadId)
    {
        $getQuotations = Quotation::with('items')->where('lead_id', $leadId)->get();

        return new QuotationCollection($getQuotations);
    }

    public function getAllQuotations()
    {
        $getQuotations = Quotation::with('items')->get();

        return new QuotationCollection($getQuotations);
    }
}
