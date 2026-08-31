@extends('layouts.admin')

@section('title', 'Edit Promo')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/create2.css') }}">
</head>

<div class="promos-page promo-form-page">
    <div class="promo-breadcrumb">
        <a href="{{ route('admin.promos.index') }}">Promo</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Edit Promo</span>
    </div>

    <div class="promos-header form-header">
        <div>
            <h1>Edit Promo: {{ $promo->name }}</h1>
            <p>Perbarui detail promo atau kupon.</p>
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

    <form action="{{ route('admin.promos.update', $promo) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="promo-form-layout" style="display:grid; grid-template-columns:2fr 1fr; gap:20px;">
            <div class="promo-form-main" style="background:#fff; padding:20px; border-radius:12px;">
                <h3>Informasi Promo</h3>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Nama Promo <span>*</span></label>
                    <input type="text" name="name" value="{{ old('name', $promo->name) }}" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Kode Promo <span>*</span></label>
                    <input type="text" name="code" value="{{ old('code', $promo->code) }}" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Jenis Diskon <span>*</span></label>
                    <select name="discount_type" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                        <option value="percentage" {{ old('discount_type', $promo->discount_type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('discount_type', $promo->discount_type) == 'fixed' ? 'selected' : '' }}>Potongan Nominal (Rp)</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Nilai Diskon <span>*</span></label>
                    <input type="number" name="discount_value" value="{{ old('discount_value', $promo->discount_value) }}" min="0" step="any" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Minimum Pembelian (Rp)</label>
                    <input type="number" name="minimum_purchase" value="{{ old('minimum_purchase', $promo->minimum_purchase) }}" min="0" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Maksimal Diskon (Rp)</label>
                    <input type="number" name="maximum_discount" value="{{ old('maximum_discount', $promo->maximum_discount) }}" min="0" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Kuota Promo</label>
                    <input type="number" name="quota" value="{{ old('quota', $promo->quota) }}" min="1" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Tanggal Mulai <span>*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($promo->start_date)->format('Y-m-d')) }}" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Berakhir <span>*</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date', \Carbon\Carbon::parse($promo->end_date)->format('Y-m-d')) }}" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
                    </div>
                </div>

                <div class="form-group" style="margin-top:15px;">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="4" style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">{{ old('description', $promo->description) }}</textarea>
                </div>
            </div>

            <div class="promo-form-side" style="background:#fff; padding:20px; border-radius:12px;">
                <h3>Status & Gambar</h3>
                @if($promo->image)
                    <div style="margin-bottom:10px;">
                        <img src="{{ asset('storage/' . $promo->image) }}" style="max-width:100px; border-radius:8px;">
                    </div>
                @endif
                <div class="form-group" style="margin-bottom:20px;">
                    <label>Gambar Promo</label>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" style="margin-top:8px;">
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $promo->is_active) ? 'checked' : '' }}> Aktifkan Promo Ini
                    </label>
                </div>
            </div>
        </div>

        <div style="margin-top:20px; display:flex; gap:10px;">
            <a href="{{ route('admin.promos.index') }}" style="padding:10px 20px; background:#ccc; border-radius:8px; text-decoration:none; color:#333;">Batal</a>
            <button type="submit" style="padding:10px 20px; background:#e63946; border-radius:8px; border:none; color:#fff; cursor:pointer;">
                <i class="fa-solid fa-floppy-disk"></i> Update Promo
            </button>
        </div>
    </form>
</div>
@endsection
