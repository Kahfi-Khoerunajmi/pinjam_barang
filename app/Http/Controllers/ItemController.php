<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with('category', 'creator');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_barang', 'like', "%$search%")
                ->orWhere('kode_barang', 'like', "%$search%");
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sort
        $sortBy = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');
        $query->orderBy($sortBy, $order);

        $items = $query->paginate(15);
        $categories = Category::all();

        return view('items.index', compact('items', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Item::class);
        $categories = Category::all();

        return view('items.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Item::class);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'lokasi' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['created_by'] = Auth::id();

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('items', 'public');
        }

        $item = Item::create($validated);
        $item->generateKodeBarang();
        $item->save();

        return redirect()->route('items.show', $item)
            ->with('success', 'Barang berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $this->authorize('view', $item);
        $item->load('category', 'creator', 'loans.user');
        $loanHistory = $item->loans()->with('user')->orderByDesc('tanggal_pinjam')->get();

        return view('items.show', compact('item', 'loanHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);
        $categories = Category::all();

        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'nullable|in:tersedia,dipinjam,perbaikan,hilang',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($item->gambar) {
                \Storage::disk('public')->delete($item->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('items', 'public');
        }

        $item->update($validated);

        return redirect()->route('items.show', $item)
            ->with('success', 'Barang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        if ($item->loans()->count() > 0) {
            return back()->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat peminjaman.');
        }

        if ($item->gambar) {
            \Storage::disk('public')->delete($item->gambar);
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus');
    }
}
