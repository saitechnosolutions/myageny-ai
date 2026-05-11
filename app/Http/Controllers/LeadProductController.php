<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadProduct;
use App\Models\Payment;
use App\Models\Product;
use App\Services\DataVisibilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadProductController extends Controller
{
    public function __construct(private readonly DataVisibilityService $visibility) {}

    // ══════════════════════════════════════════════════════════════════
    //  PRODUCTS
    // ══════════════════════════════════════════════════════════════════

    /**
     * GET /api/products
     * Returns all active products for the multi-select dropdown.
     */
    public function productList(): JsonResponse
    {
        $query = Product::with('category')
            ->where('status', 'active')
            ->orderBy('sort_order');

        $this->visibility->applyProductVisibility($query);

        $products = $query
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'name'        => $p->package_name,
                'category'    => $p->category?->name,
                'description' => $p->description,
                'price'       => (float) $p->final_price,
                'base_price'  => (float) $p->base_price,
                'discount_type' => $p->discount_type,
                'discount_value' => (float) $p->discount_value,
                'discount_percent' => $p->discount_type === 'percentage'
                    ? (float) $p->discount_value
                    : ((float) $p->base_price > 0
                        ? round(((float) $p->discount_value / (float) $p->base_price) * 100, 2)
                        : 0),
            ]);

        return response()->json(['data' => $products]);
    }

    /**
     * GET /api/products/{id}
     * Returns single product details.
     */
    public function productDetail(Product $product): JsonResponse
    {
        abort_unless($this->visibility->canAccessProduct($product), 403);

        return response()->json([
            'data' => [
                'id'          => $product->id,
                'name'        => $product->package_name,
                'category'    => $product->category?->name,
                'description' => $product->description,
                'price'       => (float) $product->final_price,
                'base_price'  => (float) $product->base_price,
                'discount_type' => $product->discount_type,
                'discount_value' => (float) $product->discount_value,
                'discount_percent' => $product->discount_type === 'percentage'
                    ? (float) $product->discount_value
                    : ((float) $product->base_price > 0
                        ? round(((float) $product->discount_value / (float) $product->base_price) * 100, 2)
                        : 0),
                'attributes'  => $product->attributeValues->map(fn($av) => [
                    'name'  => $av->attribute->name,
                    'value' => $av->value,
                    'unit'  => $av->attribute->unit,
                ])->toArray(),
            ],
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    //  LEAD PRODUCTS (Deals)
    // ══════════════════════════════════════════════════════════════════

    /**
     * GET /api/lead-products/{lead_id}
     * Returns all deals grouped by deal_name for the accordion UI.
     */
    public function index(int $leadId): JsonResponse
    {


        $lead = Lead::findOrFail($leadId);
        abort_unless($this->visibility->canAccessLead($lead), 403);

        $products = LeadProduct::with(['payments.recordedBy'])
            ->where('lead_id', $leadId)
            ->latest()
            ->get();

        // Group into deals (accordion)
        $deals = $products->groupBy('deal_name')->map(function ($items, $dealName) {
            $totalValue   = $items->sum('total_price');
            $totalPaid    = $items->sum('amount_paid');
            $totalPending = $totalValue - $totalPaid;

            return [
                'deal_name'     => $dealName,
                'status'        => $items->first()->product_status,
                'total_value'   => round($totalValue, 2),
                'total_paid'    => round($totalPaid, 2),
                'total_pending' => round($totalPending, 2),
                'products'      => $items->map(fn($p) => $p->toJsPayload())->values(),
            ];
        })->values();

        // Overall summary
        $summary = [
            'total_value'   => round($products->sum('total_price'), 2),
            'total_paid'    => round($products->sum('total_paid'), 2),
            'total_pending' => round($products->sum(fn($p) => $p->amount_pending), 2),
            'product_count' => $products->count(),
            'converted'     => $products->where('product_status', 'converted')->count(),
        ];

        return response()->json([
            'deals'   => $deals,
            'summary' => $summary,
        ]);
    }

    /**
     * POST /api/lead-products
     * Creates a new deal with multiple products.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $v = Validator::make($request->all(), [
            'lead_id'            => ['required', 'exists:leads,id'],
            'deal_name'          => ['required', 'string', 'max:255'],
            'products'           => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.remarks'    => ['nullable', 'string', 'max:1000'],
            'products.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'products.*.quantity'   => ['nullable', 'integer', 'min:1'],
            'products.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $lead = Lead::findOrFail($request->lead_id);
        abort_unless($this->visibility->canAccessLead($lead), 403);

        if (!$this->isAdmin($user)) {
            foreach ($request->products as $row) {
                $product = Product::findOrFail($row['product_id']);
                abort_unless($this->visibility->canAccessProduct($product), 403);
                $requestedPrice = round((float) ($row['unit_price'] ?? $product->final_price), 2);
                $defaultPrice = round((float) $product->final_price, 2);

                if ($requestedPrice !== $defaultPrice) {
                    return response()->json([
                        'message' => 'Price was changed. Please send a price change request for admin approval.',
                    ], 422);
                }
            }
        }

        $created = DB::transaction(function () use ($request) {
            $rows = [];
            foreach ($request->products as $row) {
                $product  = Product::findOrFail($row['product_id']);
                abort_unless($this->visibility->canAccessProduct($product), 403);
                $unitPrice = $row['unit_price'] ?? $product->final_price;
                $qty       = $row['quantity'] ?? 1;
                $disc      = $row['discount_percent'] ?? 0;

                $rows[] = LeadProduct::create([
                    'lead_id'          => $request->lead_id,
                    'product_id'       => $product->id,
                    'deal_name'        => $request->deal_name,
                    'product_name'     => $product->package_name,
                    'description'      => $product->description,
                    'unit_price'       => $unitPrice,
                    'quantity'         => $qty,
                    'discount_percent' => $disc,
                    'remarks'          => $row['remarks'] ?? null,
                    'product_status'   => 'new',
                    'created_by'       =>  auth()->id(),
                ]);
            }
            return $rows;
        });

        return response()->json([
            'message'  => 'Deal created successfully.',
            'products' => array_map(fn($p) => $p->toJsPayload(), $created),
        ], 201);
    }

    /**
     * PUT /api/lead-products/status
     * Updates status for all products in a deal.
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'lead_id'         => ['required', 'exists:leads,id'],
            'deal_name'       => ['required', 'string'],
            'product_status'  => ['required', 'in:new,hot,warm,cold,converted'],
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $lead = Lead::findOrFail($request->lead_id);
        abort_unless($this->visibility->canAccessLead($lead), 403);

        LeadProduct::where('lead_id', $request->lead_id)
            ->where('deal_name', $request->deal_name)
            ->update(['product_status' => $request->product_status]);

        return response()->json(['message' => 'Status updated.']);
    }

    /**
     * DELETE /api/lead-products/{id}
     * Soft-deletes a single lead product.
     */
    public function destroy(int $id): JsonResponse
    {
        $lp = LeadProduct::with('lead')->findOrFail($id);
        abort_unless($lp->lead && $this->visibility->canAccessLead($lp->lead), 403);
        $lp->delete();
        return response()->json(['message' => 'Product removed from deal.']);
    }

    // ══════════════════════════════════════════════════════════════════
    //  PAYMENTS
    // ══════════════════════════════════════════════════════════════════

    /**
     * GET /api/payments/{lead_product_id}
     * Returns payment history for one lead product + overall for lead.
     */
    public function paymentHistory(int $leadProductId): JsonResponse
    {
        $lp = LeadProduct::with(['lead', 'payments.recordedBy'])->findOrFail($leadProductId);
        abort_unless($lp->lead && $this->visibility->canAccessLead($lp->lead), 403);

        // Overall payments for the lead
        $overall = Payment::with('leadProduct')
            ->where('lead_id', $lp->lead_id)
            ->latest('payment_date')
            ->get()
            ->map(fn($p) => $p->toJsPayload());

        return response()->json([
            'product'  => $lp->toJsPayload(),
            'overall'  => $overall,
        ]);
    }

    /**
     * POST /api/payments
     * Stores a new payment and recalculates lead product's total_paid.
     */
    public function storePayment(Request $request): JsonResponse
    {

        $lp = LeadProduct::with('lead')->findOrFail($request->lead_product_id);
        abort_unless($lp->lead && $this->visibility->canAccessLead($lp->lead), 403);

        $balancePayment = $lp->total_price - $lp->amount_paid;

        if($balancePayment < $request->amount)
            {
        return response()->json([
            'status'  => 'Error',
            'message'  => 'Please Check you payment',
        ], 500);
            }

        $v = Validator::make($request->all(), [
            'lead_product_id' => ['required', 'exists:lead_products,id'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'payment_mode'    => ['required', 'in:cash,bank_transfer,cheque,upi,card'],
            'payment_date'    => ['required', 'date'],
            'reference_number'=> ['nullable', 'string', 'max:100'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $lp = LeadProduct::with('lead')->findOrFail($request->lead_product_id);
        abort_unless($lp->lead && $this->visibility->canAccessLead($lp->lead), 403);

        $payment = DB::transaction(function () use ($request, $lp) {
            $p = Payment::create([
                'lead_product_id' => $lp->id,
                'lead_id'         => $lp->lead_id,
                'amount'          => $request->amount,
                'payment_mode'    => $request->payment_mode,
                'payment_date'    => $request->payment_date,
                'reference_number'=> $request->reference_number,
                'notes'           => $request->notes,
                'recorded_by'     =>  auth()->id()
            ]);
            $lp->recalcPaid();
            return $p;
        });

        $payment->load('recordedBy');

        return response()->json([
            'message'  => 'Payment recorded.',
            'payment'  => $payment->toJsPayload(),
            'product'  => $lp->fresh()->toJsPayload(),
        ], 201);
    }

    /**
     * DELETE /api/payments/{id}
     * Removes a payment and recalculates totals.
     */
    public function destroyPayment(int $id): JsonResponse
    {
        $payment = Payment::findOrFail($id);
        $lp      = $payment->leadProduct()->with('lead')->first();
        abort_unless($lp && $lp->lead && $this->visibility->canAccessLead($lp->lead), 403);

        DB::transaction(function () use ($payment, $lp) {
            $payment->delete();
            $lp->recalcPaid();
        });

        return response()->json([
            'message' => 'Payment removed.',
            'product' => $lp->fresh()->toJsPayload(),
        ]);
    }

    protected function isAdmin($user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'Super Admin', 'admin']);
    }
}
