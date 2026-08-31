@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/addproduk.css') }}">
</head>

<div class="products-page">
    <div class="products-header">
        <div>
            <h1>Kelola Produk</h1>
            <p>Kelola semua produk yang tersedia di toko Belfoods.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn-add-product">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>
    </div>

    {{-- STOCK SUMMARY CARDS --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px; margin-bottom:20px;">
        <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #eee; display:flex; align-items:center; gap:15px; box-shadow:0 4px 12px rgba(0,0,0,0.03);">
            <div style="width:45px; height:45px; border-radius:10px; background:#eef9e7; color:#5b991d; display:flex; align-items:center; justify-content:center; font-size:20px;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <span style="font-size:12px; color:#777; font-weight:500;">Total Jumlah Stok</span>
                <h3 style="font-size:22px; font-weight:700; color:#333; margin-top:2px;">{{ number_format($totalStockSum ?? 0, 0, ',', '.') }} <small style="font-size:12px; color:#777; font-weight:400;">unit</small></h3>
            </div>
        </div>

        <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #eee; display:flex; align-items:center; gap:15px; box-shadow:0 4px 12px rgba(0,0,0,0.03);">
            <div style="width:45px; height:45px; border-radius:10px; background:#e3f2fd; color:#1976d2; display:flex; align-items:center; justify-content:center; font-size:20px;">
                <i class="fa-solid fa-box"></i>
            </div>
            <div>
                <span style="font-size:12px; color:#777; font-weight:500;">Total Varian Produk</span>
                <h3 style="font-size:22px; font-weight:700; color:#333; margin-top:2px;">{{ number_format($totalProductsCount ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div style="background:#fff; border-radius:12px; padding:18px; border:1px solid #eee; display:flex; align-items:center; gap:15px; box-shadow:0 4px 12px rgba(0,0,0,0.03);">
            <div style="width:45px; height:45px; border-radius:10px; background:#fbe9e7; color:#d32f2f; display:flex; align-items:center; justify-content:center; font-size:20px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span style="font-size:12px; color:#777; font-weight:500;">Stok Habis / Kosong</span>
                <h3 style="font-size:22px; font-weight:700; color:#d32f2f; margin-top:2px;">{{ number_format($outOfStockCount ?? 0, 0, ',', '.') }} <small style="font-size:12px; color:#777; font-weight:400;">produk</small></h3>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form action="{{ route('admin.products.index') }}" method="GET" class="product-filter">
        <div class="product-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk, kategori, harga...">
        </div>

        <select name="category">
            <option value="all">Semua Kategori</option>
            <option value="Nugget" {{ request('category') == 'Nugget' ? 'selected' : '' }}>Nugget</option>
            <option value="Sosis" {{ request('category') == 'Sosis' ? 'selected' : '' }}>Sosis</option>
            <option value="Bakso" {{ request('category') == 'Bakso' ? 'selected' : '' }}>Bakso</option>
            <option value="Kentang" {{ request('category') == 'Kentang' ? 'selected' : '' }}>Kentang</option>
            <option value="Lainnya" {{ request('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>

        <select name="status">
            <option value="all">Semua Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <select name="sort">
            <option value="">Urutkan</option>
            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
            <option value="stock_low" {{ request('sort') == 'stock_low' ? 'selected' : '' }}>Stok Terendah</option>
            <option value="stock_high" {{ request('sort') == 'stock_high' ? 'selected' : '' }}>Stok Tertinggi</option>
        </select>

        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-filter"></i> Filter
        </button>

        @if(request()->hasAny(['search', 'category', 'status', 'sort']))
            <a href="{{ route('admin.products.index') }}" class="btn-reset">Reset</a>
        @endif
    </form>

    {{-- PRODUCT TABLE --}}
    <div class="product-table-wrapper">
        <table class="product-table">
            <thead>
                <tr>
                    <th width="55">No</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Fitur</th>
                    <th width="110">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                    <tr>
                        <td>{{ $products->firstItem() + $index }}</td>
                        <td>
                            <div class="product-info">
                                <div class="product-image">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="image-placeholder"><i class="fa-solid fa-box"></i></div>
                                    @endif
                                </div>
                                <div class="product-name">
                                    <strong>{{ $product->name }}</strong>
                                    <span>{{ strtoupper($product->slug ?? $product->name) }}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="category-badge">{{ $product->category }}</span></td>
                        <td><strong class="product-price">Rp{{ number_format($product->price, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($product->stock <= 10)
                                <span class="stock-low">{{ $product->stock }}</span>
                            @else
                                <span class="stock-normal">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="rating">
                                <span>{{ number_format($product->rating ?? 0, 1) }}</span>
                                <i class="fa-solid fa-star"></i>
                            </div>
                        </td>
                        <td>
                            @if($product->is_active)
                                <span class="status active"><span class="status-dot"></span> Aktif</span>
                            @else
                                <span class="status inactive"><span class="status-dot"></span> Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="feature-list">
                                @if($product->is_best_seller) <span class="feature best">Best Seller</span> @endif
                                @if($product->is_hemat) <span class="feature hemat">Hemat</span> @endif
                                @if($product->is_premium) <span class="feature premium">Premium</span> @endif
                                @if(!$product->is_best_seller && !$product->is_hemat && !$product->is_premium) <span class="feature-empty">-</span> @endif
                            </div>
                        </td>
                        <td>
                            <div class="product-actions">
                                <a href="{{ route('admin.products.edit', $product) }}" class="action-edit" title="Edit Produk">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="delete-form" onsubmit="return confirm('Yakin ingin menghapus produk {{ addslashes($product->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-delete" title="Hapus Produk">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-product">
                            <div>
                                <i class="fa-solid fa-box-open"></i>
                                <h3>Produk belum ditemukan</h3>
                                <p>Coba ubah pencarian atau tambahkan produk baru.</p>
                                <a href="{{ route('admin.products.create') }}" class="btn-empty-add">
                                    <i class="fa-solid fa-plus"></i> Tambah Produk
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->count() > 0)
        <div class="product-footer">
            <div class="product-count">
                Menampilkan <strong>{{ $products->firstItem() }}</strong> - <strong>{{ $products->lastItem() }}</strong> dari <strong>{{ $products->total() }}</strong> produk
            </div>
            <div class="product-pagination">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
