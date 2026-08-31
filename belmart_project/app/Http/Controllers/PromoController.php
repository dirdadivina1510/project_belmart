<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoController extends Controller
{
    /**
     * Menampilkan semua promo di halaman admin.
     */
    public function index(Request $request)
    {
        $query = Promo::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $now = now();
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true)
                          ->where('start_date', '<=', $now)
                          ->where('end_date', '>=', $now);
                    break;
                case 'upcoming':
                    $query->where('is_active', true)
                          ->where('start_date', '>', $now);
                    break;
                case 'expired':
                    $query->where('end_date', '<', $now);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        $totalPromos = Promo::count();
        $now = now();
        $activePromos = Promo::where('is_active', true)->where('start_date', '<=', $now)->where('end_date', '>=', $now)->count();
        $upcomingPromos = Promo::where('is_active', true)->where('start_date', '>', $now)->count();
        $expiredPromos = Promo::where('end_date', '<', $now)->count();

        $promos = $query->latest()->paginate(10)->withQueryString();

        return view('admin.promos.index', compact(
            'promos',
            'totalPromos',
            'activePromos',
            'upcomingPromos',
            'expiredPromos'
        ));
    }

    /**
     * Menampilkan form tambah promo.
     */
    public function create()
    {
        return view('admin.promos.create');
    }

    /**
     * Menyimpan promo baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:promos,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return back()->withInput()->withErrors([
                'discount_value' => 'Diskon persentase tidak boleh lebih dari 100%.'
            ]);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('promos', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        Promo::create($validated);

        return redirect()
            ->route('admin.promos.index')
            ->with('success', 'Promo berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail promo.
     */
    public function show(Promo $promo)
    {
        return view('admin.promos.show', compact('promo'));
    }

    /**
     * Menampilkan form edit promo.
     */
    public function edit(Promo $promo)
    {
        return view('admin.promos.edit', compact('promo'));
    }

    /**
     * Update promo.
     */
    public function update(Request $request, Promo $promo)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:promos,code,' . $promo->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return back()->withInput()->withErrors([
                'discount_value' => 'Diskon persentase tidak boleh lebih dari 100%.'
            ]);
        }

        if ($request->hasFile('image')) {
            if ($promo->image && Storage::disk('public')->exists($promo->image)) {
                Storage::disk('public')->delete($promo->image);
            }
            $validated['image'] = $request->file('image')->store('promos', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $promo->update($validated);

        return redirect()
            ->route('admin.promos.index')
            ->with('success', 'Promo berhasil diperbarui.');
    }

    /**
     * Hapus promo.
     */
    public function destroy(Promo $promo)
    {
        if ($promo->image && Storage::disk('public')->exists($promo->image)) {
            Storage::disk('public')->delete($promo->image);
        }

        $promo->delete();

        return redirect()
            ->route('admin.promos.index')
            ->with('success', 'Promo berhasil dihapus.');
    }

    /**
     * Mengaktifkan / menonaktifkan promo.
     */
    public function toggleStatus(Promo $promo)
    {
        $promo->update([
            'is_active' => !$promo->is_active,
        ]);

        return back()->with(
            'success',
            $promo->is_active ? 'Promo berhasil diaktifkan.' : 'Promo berhasil dinonaktifkan.'
        );
    }
}