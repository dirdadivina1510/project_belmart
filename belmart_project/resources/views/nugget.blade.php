@extends('layouts.app')

@section('title', 'Nugget')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/category.css') }}">
@endpush

@section('content')
<div class="category-page">
    <!-- HEADER -->
    <section class="category-header">
        <div class="category-header-content">
            <div class="category-breadcrumb">
                <a href="{{ route('home') }}">Beranda</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Nugget</span>
            </div>
            <span class="category-small-title">FROZEN FOOD BELFOODS</span>
            <h1>Nugget</h1>
            <p>Pilihan nugget favorit dengan berbagai rasa dan ukuran untuk menemani waktu makan keluarga.</p>
        </div>
        <div class="category-header-icon">
            <i class="fa-solid fa-drumstick-bite"></i>
        </div>
    </section>

    <!-- TOOLBAR -->
    <div class="category-toolbar">
        <div class="category-result">
            <strong>Nugget</strong>
            <span>({{ method_exists($products, 'total') ? $products->total() : $products->count() }} produk)</span>
        </div>
        <div class="category-actions">
            <form action="{{ route('products.nugget') }}" method="GET" class="sort-form">
                <label for="sort-nugget">Urutkan:</label>
                <select name="sort" id="sort-nugget" onchange="this.form.submit()">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Paling Populer</option>
                </select>
            </form>
        </div>
    </div>

    <!-- PRODUCT GRID -->
    <div class="category-products">
        @forelse($products as $product)
            <div class="category-product-item">
                <div class="category-product-image">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <div class="product-no-image">
                            <i class="fa-solid fa-drumstick-bite"></i>
                        </div>
                    @endif
                    @if($product->is_best_seller)
                        <span class="product-badge best">⭐ Best Seller</span>
                    @endif
                </div>
                <div class="category-product-content" style="padding:16px;">
                    <h3 style="font-size:15px; font-weight:600; margin-bottom:8px; color:#3a5234;">
                        <a href="{{ route('products.show', $product->id) }}" style="text-decoration:none; color:inherit;">
                            {{ $product->name }}
                        </a>
                    </h3>
                    <div class="category-product-price" style="font-size:18px; font-weight:700; color:#5b991d; margin-bottom:12px;">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </div>
                    <div style="display:flex; gap:8px;">
                        <a href="{{ route('products.show', $product->id) }}"
                           style="flex:1; text-align:center; padding:8px; background:#eef9e7; color:#5b991d; border-radius:10px; text-decoration:none; font-size:13px; font-weight:500;">
                            Detail
                        </a>
                        <form action="{{ route('products.addToCart', $product->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="cart-btn" title="Tambah ke keranjang" style="width:38px; height:38px; border-radius:10px; background:#5b991d; color:#fff; border:none; cursor:pointer; font-size:16px;">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="category-empty" style="grid-column:1/-1; text-align:center; padding:70px 20px;">
                <i class="fa-solid fa-box-open" style="font-size:56px; color:#c5d9be; margin-bottom:20px;"></i>
                <h3 style="color:#5a7252;">Belum ada produk nugget</h3>
                <p style="color:#8fa688; margin-top:8px;">Produk nugget akan segera tersedia. Kunjungi kategori lainnya!</p>
                <a href="{{ route('home') }}" style="display:inline-block; margin-top:20px; padding:12px 24px; background:#5b991d; color:#fff; border-radius:14px; text-decoration:none;">
                    Kembali ke Beranda
                </a>
            </div>
        @endforelse
    </div>

    @if(method_exists($products, 'links'))
        <div style="margin-top:35px;">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection