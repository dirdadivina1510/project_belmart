@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/create2.css') }}">
</head>

<div class="products-page product-form-page">
    <div class="product-breadcrumb">
        <a href="{{ route('admin.products.index') }}">Produk</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Tambah Produk</span>
    </div>

    <div class="products-header form-header">
        <div>
            <h1>Tambah Produk</h1>
            <p>Tambahkan produk baru ke toko Belfoods.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert-error" style="padding:15px; background:#f8d7da; color:#721c24; border-radius:8px; margin-bottom:20px;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Ada data yang perlu diperbaiki:</strong>
                <ul style="margin:5px 0 0 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productCreateForm">
        @csrf
        <div class="product-form-layout">
            <div class="product-form-main">
                <div class="form-section">
                    <div class="section-heading">
                        <div>
                            <h3>Informasi Produk</h3>
                            <p>Masukkan informasi dasar produk.</p>
                        </div>
                    </div>

                    <div class="form-grid two-column">
                        <div class="form-group">
                            <label for="name">Nama Produk <span>*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Belfoods Chicken Nugget 500gr" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Kategori <span>*</span></label>
                            <select id="category" name="category" required>
                                <option value="Nugget" selected>Nugget</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid three-column">
                        <div class="form-group">
                            <label for="price">Harga <span>*</span></label>
                            <div class="input-prefix">
                                <span>Rp</span>
                                <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" placeholder="35000" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stok <span>*</span></label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" placeholder="0" required>
                        </div>

                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <select id="rating" name="rating">
                                <option value="0">0.0</option>
                                @for($i = 1; $i <= 5; $i += 0.5)
                                    <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ number_format($i, 1) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="shelf_life">Estimasi Ketahanan</label>
                        <input type="text" id="shelf_life" name="shelf_life" value="{{ old('shelf_life') }}" placeholder="Contoh: 6 - 10 bulan">
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi Produk</label>
                        <textarea id="description" name="description" rows="6" maxlength="2000" placeholder="Tuliskan deskripsi produk...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-heading">
                        <div>
                            <h3>Fitur Produk</h3>
                            <p>Tandai fitur yang sesuai dengan produk.</p>
                        </div>
                    </div>

                    <div class="feature-options">
                        <label class="feature-checkbox">
                            <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller') ? 'checked' : '' }}>
                            <span class="feature-text"><strong>Best Seller</strong> <small>Produk paling banyak diminati.</small></span>
                        </label>
                        <label class="feature-checkbox">
                            <input type="checkbox" name="is_hemat" value="1" {{ old('is_hemat') ? 'checked' : '' }}>
                            <span class="feature-text"><strong>Hemat</strong> <small>Produk ekonomis.</small></span>
                        </label>
                        <label class="feature-checkbox">
                            <input type="checkbox" name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}>
                            <span class="feature-text"><strong>Premium</strong> <small>Produk premium.</small></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="product-form-side">
                <div class="form-section image-section">
                    <div class="section-heading">
                        <div>
                            <h3>Foto Produk</h3>
                            <p>PNG, JPG, WEBP maksimal 2 MB.</p>
                        </div>
                    </div>
                    <div class="image-upload" id="imageUpload">
                        <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-heading">
                        <div>
                            <h3>Status Produk</h3>
                        </div>
                    </div>
                    <label class="status-radio">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span><strong>Aktif</strong> <small>Produk bisa dilihat dan dibeli.</small></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-actions" style="margin-top:20px; display:flex; gap:10px;">
            <a href="{{ route('admin.products.index') }}" class="btn-cancel" style="padding:10px 20px; background:#ccc; border-radius:8px; text-decoration:none; color:#333;">Batal</a>
            <button type="submit" class="btn-save-product" style="padding:10px 20px; background:#e63946; border-radius:8px; border:none; color:#fff; cursor:pointer;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Produk
            </button>
        </div>
    </form>
</div>
@endsection
