<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::withCount('products')->orderBy('sort_order')->get();
        return view('master.collections.index', compact('collections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:collections,name',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable',
        ]);

        Collection::create([
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Koleksi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:collections,name,' . $id,
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable',
        ]);

        $collection->update([
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Koleksi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Collection::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Koleksi berhasil dihapus.');
    }
}
