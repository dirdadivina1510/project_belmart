@extends('layouts.admin')

@section('title', 'Tambah Promo')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/create2.css') }}">
</head>

<div class="promos-page promo-form-page">
    <div class="promo-breadcrumb">
        <a href="{{ route('admin.promos.index') }}">Promo</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Tambah Promo</span>
    </div>

    <div class="promos-header form-header">
        <div>
            <h1>Tambah Promo</h1>
            <p>Tambahkan promo atau kupon baru untuk pelanggan.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert-error" style="padding:15px; background:#f8d7da; color:#721c24; border-radius:8px; margin-bottom:20px;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <ul style="margin:5px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.promos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="promo-form-layout" style="display:grid; grid-template-columns:2fr 1fr; gap:20px;">
            <div class="promo-form-main" style="background:#fff; padding:20px; border-radius:12px;">
                <h3>Informasi Promo</h3>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Nama Promo <span style="color:#e63946;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Diskon Kemerdekaan" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                    @error('name')
                        <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Kode Promo <span style="color:#e63946;">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="Contoh: HEMAT10" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc; text-transform:uppercase;">
                    @error('code')
                        <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Jenis Diskon <span style="color:#e63946;">*</span></label>
                    <select name="discount_type" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Potongan Nominal (Rp)</option>
                    </select>
                    @error('discount_type')
                        <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Nilai Diskon <span style="color:#e63946;">*</span></label>
                    <input type="number" name="discount_value" value="{{ old('discount_value') }}" min="0" step="any" placeholder="10 atau 10000" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                    @error('discount_value')
                        <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Minimum Pembelian (Rp)</label>
                    <input type="number" name="minimum_purchase" value="{{ old('minimum_purchase', 0) }}" min="0" placeholder="0" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                    @error('minimum_purchase')
                        <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Maksimal Diskon (Rp)</label>
                    <input type="number" name="maximum_discount" value="{{ old('maximum_discount') }}" min="0" placeholder="Kosongkan jika tidak ada batas" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                    @error('maximum_discount')
                        <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Kuota Promo</label>
                    <input type="number" name="quota" value="{{ old('quota') }}" min="1" placeholder="Kosongkan untuk unlimited" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                    @error('quota')
                        <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Tanggal Mulai <span style="color:#e63946;">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                        @error('start_date')
                            <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Tanggal Berakhir <span style="color:#e63946;">*</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                        @error('end_date')
                            <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-group" style="margin-top:15px;">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="4" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="promo-form-side" style="background:#fff; padding:20px; border-radius:12px;">
                <h3>Status & Gambar</h3>
                <div class="form-group" style="margin-bottom:20px;">
                    <label>Gambar Promo</label>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" style="margin-top:8px;">
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" checked> Aktifkan Promo Ini
                    </label>
                </div>
            </div>
        </div>

        <div style="margin-top:20px; display:flex; gap:10px;">
            <a href="{{ route('admin.promos.index') }}" style="padding:10px 20px; background:#ccc; border-radius:8px; text-decoration:none; color:#333;">Batal</a>
            <button type="submit" style="padding:10px 20px; background:#e63946; border-radius:8px; border:none; color:#fff; cursor:pointer;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Promo
            </button>
        </div>
    </form>
</div>
@endsection
