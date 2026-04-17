<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $service) {}

    // ── Web Routes ────────────────────────────────────────────────────

    /**
     * GET /products
     */
    public function index(Request $request): View
    {
        $query = Product::with('category')
            ->when($request->search, fn($q) =>
                $q->where('package_name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
            )
            ->when($request->category, fn($q) =>
                $q->where('product_category_id', $request->category)
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->orderBy('sort_order')
            ->orderByDesc('created_at');

        $products   = $query->paginate(15)->withQueryString();
        $categories = ProductCategory::active()->orderBy('name')->get();

        return view('pages.products.index', compact('products', 'categories'));
    }

    /**
     * GET /products/create
     */
    public function create(): View
    {
        $categories = ProductCategory::active()->orderBy('name')->get();
        return view('pages.products.create', compact('categories'));
    }

    /**
     * POST /products
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->service->store($request->validated());

        return redirect()
            ->route('products.show', $product)
            ->with('success', "Product {$product->package_name} created successfully.");
    }

    /**
     * GET /products/{product}
     */
    public function show(Product $product): View
    {
        $product->load('category', 'attributeValues.attribute');
        return view('pages.products.show', compact('product'));
    }

    /**
     * GET /products/{product}/edit
     */
    public function edit(Product $product): View
    {
        $product->load('category', 'attributeValues.attribute');
        $categories = ProductCategory::active()->orderBy('name')->get();

        // Build a keyed map for pre-filling attribute values
        $existingValues = $product->attributeValues
            ->keyBy('attribute_id')
            ->map(fn($pav) => $pav->value);

        return view('pages.products.edit', compact('product', 'categories', 'existingValues'));
    }

    /**
     * PUT /products/{product}
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product = $this->service->update($product, $request->validated());

        return redirect()
            ->route('products.show', $product)
            ->with('success', "Product {$product->package_name} updated successfully.");
    }

    /**
     * DELETE /products/{product}
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->package_name;
        $this->service->delete($product);

        return redirect()
            ->route('products.index')
            ->with('success', "Product {$name} deleted successfully.");
    }

    // ── AJAX / API Helpers ────────────────────────────────────────────

    /**
     * GET /products/attributes-by-category/{category}
     * Returns attributes for the selected category (used by AJAX).
     */
    public function attributesByCategory(ProductCategory $category): JsonResponse
    {
        $attributes = Attribute::with('presetValues')
            ->where('product_category_id', $category->id)
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn($attr) => [
                'id'           => $attr->id,
                'name'         => $attr->name,
                'key'          => $attr->key,
                'field_type'   => $attr->field_type,
                'unit'         => $attr->unit,
                'placeholder'  => $attr->placeholder,
                'is_required'  => $attr->is_required,
                'options'      => $attr->options ?? [],
                'preset_values'=> $attr->presetValues->pluck('value'),
            ]);

        return response()->json(['attributes' => $attributes]);
    }

    /**
     * POST /products/preview-price
     * Returns computed final price without persisting.
     */
    public function previewPrice(Request $request): JsonResponse
    {
        $request->validate([
            'base_price'     => 'required|numeric|min:0',
            'tax_type'       => 'required|in:percentage,fixed',
            'tax_value'      => 'required|numeric|min:0',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        $finalPrice = $this->service->previewPrice($request->only(
            'base_price', 'tax_type', 'tax_value', 'discount_type', 'discount_value'
        ));

        return response()->json(['final_price' => $finalPrice]);
    }

    // ── API (JSON) endpoints for mobile ──────────────────────────────

    public function apiIndex(Request $request): JsonResponse
    {
        $products = Product::with('category', 'attributeValues.attribute')
            ->active()
            ->when($request->category_id, fn($q) => $q->byCategory($request->category_id))
            ->paginate($request->per_page ?? 20);

        return response()->json($products);
    }

    public function apiShow(Product $product): JsonResponse
    {
        return response()->json(
            $product->load('category', 'attributeValues.attribute')
        );
    }

    public function getProducts()
    {
        $products = ProductCategory::with('products', 'attributes', 'products.attributeValues')->get();

        return new ProductCollection($products);
    }
}