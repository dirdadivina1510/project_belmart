@extends('layouts.admin')

@section('title', 'Kelola Promo')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/addpromo.css') }}">
</head>

<div class="promos-page">
    <div class="promos-header">
        <div>
            <div class="promo-breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Promo</span>
            </div>
            <h1>Kelola Promo</h1>
            <p>Tambahkan, edit, dan kelola promo toko.</p>
        </div>

        <a href="{{ route('admin.promos.create') }}" class="btn-add-promo">
            <i class="fa-solid fa-plus"></i> Tambah Promo
        </a>
    </div>

    {{-- STATISTICS --}}
    <div class="promo-statistics">
        <div class="promo-stat">
            <div class="promo-stat-icon total"><i class="fa-solid fa-tags"></i></div>
            <div><span>Total Promo</span><strong>{{ $totalPromos ?? 0 }}</strong></div>
        </div>
        <div class="promo-stat">
            <div class="promo-stat-icon active"><i class="fa-solid fa-circle-check"></i></div>
            <div><span>Promo Aktif</span><strong>{{ $activePromos ?? 0 }}</strong></div>
        </div>
        <div class="promo-stat">
            <div class="promo-stat-icon upcoming"><i class="fa-solid fa-calendar-days"></i></div>
            <div><span>Akan Datang</span><strong>{{ $upcomingPromos ?? 0 }}</strong></div>
        </div>
        <div class="promo-stat">
            <div class="promo-stat-icon expired"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div><span>Berakhir</span><strong>{{ $expiredPromos ?? 0 }}</strong></div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form action="{{ route('admin.promos.index') }}" method="GET" class="promo-filter">
        <div class="promo-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama promo atau kode...">
        </div>

        <select name="status">
            <option value="">Semua Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Berakhir</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <button type="submit" class="btn-promo-filter">
            <i class="fa-solid fa-filter"></i> Filter
        </button>

        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.promos.index') }}" class="btn-promo-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>

    {{-- TABLE --}}
    <div class="promo-table-wrapper">
        <table class="promo-table">
            <thead>
                <tr>
                    <th>PROMO</th>
                    <th>KODE</th>
                    <th>DISKON</th>
                    <th>MIN. PEMBELIAN</th>
                    <th>PERIODE</th>
                    <th>KUOTA</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promos as $promo)
                    <tr>
                        <td>
                            <div class="promo-info">
                                <div class="promo-image">
                                    @if($promo->image)
                                        <img src="{{ asset('storage/' . $promo->image) }}" alt="{{ $promo->name }}">
                                    @else
                                        <div class="promo-image-empty"><i class="fa-solid fa-tag"></i></div>
                                    @endif
                                </div>
                                <div class="promo-name">
                                    <strong>{{ $promo->name }}</strong>
                                    @if($promo->description)
                                        <span>{{ Str::limit($promo->description, 45) }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($promo->code)
                                <span class="promo-code">{{ $promo->code }}</span>
                            @else
                                <span class="no-code">Tanpa kode</span>
                            @endif
                        </td>
                        <td>
                            <div class="discount-info">
                                <strong>
                                    @if($promo->discount_type === 'percentage')
                                        {{ $promo->discount_value }}%
                                    @else
                                        Rp{{ number_format($promo->discount_value, 0, ',', '.') }}
                                    @endif
                                </strong>
                                <span>{{ $promo->discount_type === 'percentage' ? 'Persentase' : 'Potongan nominal' }}</span>
                            </div>
                        </td>
                        <td>
                            @if($promo->minimum_purchase > 0)
                                <span class="minimum-price">Rp{{ number_format($promo->minimum_purchase, 0, ',', '.') }}</span>
                            @else
                                <span class="free-minimum">Tanpa minimum</span>
                            @endif
                        </td>
                        <td>
                            <div class="promo-period">
                                <span><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($promo->start_date)->format('d M Y') }}</span>
                                <small>sampai</small>
                                <span><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($promo->end_date)->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td>
                            @if($promo->quota !== null)
                                <div class="quota-info">
                                    <div class="quota-number">
                                        <strong>{{ $promo->quota }}</strong>
                                    </div>
                                </div>
                            @else
                                <span class="unlimited-quota"><i class="fa-solid fa-infinity"></i> Unlimited</span>
                            @endif
                        </td>
                        <td>
                            @if($promo->is_active)
                                <span class="promo-status active">Aktif</span>
                            @else
                                <span class="promo-status inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="promo-actions" style="display:flex; gap:6px;">
                                <a href="{{ route('admin.promos.edit', $promo) }}" class="promo-action edit" title="Edit Promo">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.promos.toggle-status', $promo) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="promo-action toggle" title="Aktif / Nonaktif">
                                        <i class="fa-solid fa-toggle-{{ $promo->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" onsubmit="return confirm('Hapus promo ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="promo-action delete" title="Hapus Promo">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="promo-empty">
                            <div>
                                <i class="fa-solid fa-tags"></i>
                                <h3>Belum ada promo</h3>
                                <p>Tambahkan promo pertama untuk toko kamu.</p>
                                <a href="{{ route('admin.promos.create') }}" class="btn-empty-promo">
                                    <i class="fa-solid fa-plus"></i> Tambah Promo
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($promos->count() > 0)
        <div style="margin-top:20px;">
            {{ $promos->links() }}
        </div>
    @endif
</div>
@endsection
