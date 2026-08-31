<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PUBLIC CATEGORIES & SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function nugget(Request $request)
    {
        return $this->getCategoryView('Nugget', 'nugget', $request);
    }

    public function sosis(Request $request)
    {
        return $this->getCategoryView('Sosis', 'sosis', $request);
    }

    public function bakso(Request $request)
    {
        return $this->getCategoryView('Bakso', 'bakso', $request);
    }

    public function forkids(Request $request)
    {
        return $this->getCategoryView('For Kids', 'forkids', $request);
    }

    public function others(Request $request)
    {
        return $this->getCategoryView('Others', 'others', $request);
    }

    private function getCategoryView($categoryName, $viewName, Request $request)
    {
        $query = Product::where('is_active', true);

        if ($categoryName !== 'Semua') {
            $query->where(function ($q) use ($categoryName) {
                $q->where('category', 'like', '%' . $categoryName . '%');
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('sold_count', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        return view($viewName, compact('products'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADD TO CART
    |--------------------------------------------------------------------------
    */

    public function addToCart(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = (int) $request->quantity;

        if ($quantity > $product->stock) {
            return back()->with('error', 'Jumlah produk melebihi stok yang tersedia.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $newQuantity = $cart[$product->id]['quantity'] + $quantity;

            if ($newQuantity > $product->stock) {
                return back()->with('error', 'Jumlah produk di keranjang melebihi stok.');
            }

            $cart[$product->id]['quantity'] = $newQuantity;
            $cart[$product->id]['price'] = $product->price;
            $cart[$product->id]['name'] = $product->name;
            $cart[$product->id]['image'] = $product->image;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN PRODUCT CRUD
    |--------------------------------------------------------------------------
    */

    public function adminIndex(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'stock_low':
                    $query->orderBy('stock', 'asc');
                    break;
                case 'stock_high':
                    $query->orderBy('stock', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(10)->withQueryString();

        $totalProductsCount = Product::count();
        $totalStockSum = Product::sum('stock');
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        return view('admin.products.index', compact('products', 'totalProductsCount', 'totalStockSum', 'outOfStockCount'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'shelf_life' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_best_seller' => 'nullable|boolean',
            'is_hemat' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_hemat'] = $request->boolean('is_hemat');
        $validated['is_premium'] = $request->boolean('is_premium');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'shelf_life' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_best_seller' => 'nullable|boolean',
            'is_hemat' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_hemat'] = $request->boolean('is_hemat');
        $validated['is_premium'] = $request->boolean('is_premium');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}