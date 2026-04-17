<?php
// ================================================================
// FILE: app/Http/Controllers/Products/ProductController.php
// ================================================================

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /products — list with filters
     */
    public function index(Request $request)
    {
        $query = Product::with('createdBy')->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('gst')) {
            $query->where('gst_percent', $request->gst);
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $products   = $query->paginate(15)->withQueryString();
        $categories = Product::select('category')
            ->whereNotNull('category')->where('category','!=','')
            ->distinct()->orderBy('category')->pluck('category');

        // Stats
        $stats = [
            'total'      => Product::count(),
            'active'     => Product::where('is_active', true)->count(),
            'inactive'   => Product::where('is_active', false)->count(),
            'categories' => Product::whereNotNull('category')->distinct('category')->count('category'),
        ];

        return view('pages.products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * GET /products/create
     */
    public function create()
    {
        $categories = Product::select('category')
            ->whereNotNull('category')->where('category','!=','')
            ->distinct()->orderBy('category')->pluck('category');

        return view('pages.products.create', compact('categories'));
    }

    /**
     * POST /products
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', "Product <strong>{$product->product_name}</strong> created successfully.");
    }

    /**
     * GET /products/{product}
     */
    public function show(Product $product)
    {
        $product->load('createdBy');
        return view('pages.products.show', compact('product'));
    }

    /**
     * GET /products/{product}/edit
     */
    public function edit(Product $product)
    {
        $categories = Product::select('category')
            ->whereNotNull('category')->where('category','!=','')
            ->distinct()->orderBy('category')->pluck('category');

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * PUT /products/{product}
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', "Product <strong>{$product->product_name}</strong> updated successfully.");
    }

    /**
     * DELETE /products/{product}
     */
    public function destroy(Product $product)
    {
        $name = $product->product_name;
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', "Product <strong>{$name}</strong> has been removed.");
    }

    /**
     * PATCH /products/{product}/toggle-status
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        $status = $product->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Product <strong>{$product->product_name}</strong> {$status}.");
    }
}
