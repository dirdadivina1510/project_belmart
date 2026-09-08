@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/create2.css') }}">
</head>

<div class="products-page product-form-page">
    <div class="product-breadcrumb">
        <a href="{{ route('admin.products.index') }}">Produk</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Edit Produk</span>
    </div>

    <div class="products-header form-header">
        <div>
            <h1>Edit Produk: {{ $product->name }}</h1>
            <p>Perbarui data produk Belfoods Store.</p>
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

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productEditForm">
        @csrf
        @method('PUT')
        <div class="product-form-layout">
            <div class="product-form-main">
                <div class="form-section">
                    <div class="section-heading">
                        <div>
                            <h3>Informasi Produk</h3>
                        </div>
                    </div>

                    <div class="form-grid two-column">
                        <div class="form-group">
                            <label for="name">Nama Produk <span>*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category">Kategori <span>*</span></label>
                            <select id="category" name="category" required>
                                <option value="Nugget" {{ old('category', $product->category) == 'Nugget' ? 'selected' : '' }}>Nugget</option>
                                <option value="Sosis" {{ old('category', $product->category) == 'Sosis' ? 'selected' : '' }}>Sosis</option>
                                <option value="Bakso" {{ old('category', $product->category) == 'Bakso' ? 'selected' : '' }}>Bakso</option>
                                <option value="For Kids" {{ old('category', $product->category) == 'For Kids' ? 'selected' : '' }}>For Kids</option>
                                <option value="Others" {{ old('category', $product->category) == 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                            @error('category')
                                <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-grid three-column">
                        <div class="form-group">
                            <label for="price">Harga (Rp) <span>*</span></label>
                            <div class="input-prefix">
                                <span>Rp</span>
                                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" min="0" required>
                            </div>
                            @error('price')
                                <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="stock">Stok <span>*</span></label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                            @error('stock')
                                <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <select id="rating" name="rating">
                                @for($i = 0; $i <= 5; $i += 0.5)
                                    <option value="{{ $i }}" {{ old('rating', $product->rating) == $i ? 'selected' : '' }}>{{ number_format($i, 1) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="shelf_life">Estimasi Ketahanan</label>
                        <input type="text" id="shelf_life" name="shelf_life" value="{{ old('shelf_life', $product->shelf_life) }}">
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi Produk</label>
                        <textarea id="description" name="description" rows="6" maxlength="2000">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-heading">
                        <div>
                            <h3>Fitur Produk</h3>
                        </div>
                    </div>

                    <div class="feature-options">
                        <label class="feature-checkbox">
                            <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }}>
                            <span class="feature-text"><strong>Best Seller</strong></span>
                        </label>
                        <label class="feature-checkbox">
                            <input type="checkbox" name="is_hemat" value="1" {{ old('is_hemat', $product->is_hemat) ? 'checked' : '' }}>
                            <span class="feature-text"><strong>Hemat</strong></span>
                        </label>
                        <label class="feature-checkbox">
                            <input type="checkbox" name="is_premium" value="1" {{ old('is_premium', $product->is_premium) ? 'checked' : '' }}>
                            <span class="feature-text"><strong>Premium</strong></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="product-form-side">
                <div class="form-section image-section">
                    <div class="section-heading">
                        <div>
                            <h3>Foto Produk</h3>
                        </div>
                    </div>
                    @if($product->image)
                        <div style="margin-bottom:10px;">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="Gambar Produk" style="max-width:100px; border-radius:8px;">
                        </div>
                    @endif
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
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <span><strong>Aktif</strong></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-actions" style="margin-top:20px; display:flex; gap:10px;">
            <a href="{{ route('admin.products.index') }}" class="btn-cancel" style="padding:10px 20px; background:#ccc; border-radius:8px; text-decoration:none; color:#333;">Batal</a>
            <button type="submit" class="btn-save-product" style="padding:10px 20px; background:#e63946; border-radius:8px; border:none; color:#fff; cursor:pointer;">
                <i class="fa-solid fa-floppy-disk"></i> Update Produk
            </button>
        </div>
    </form>
</div>
@endsection
