<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('master.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'nullable'
        ], [
            'name.required' => 'Nama brand wajib diisi',
            'name.unique' => 'Nama brand sudah terdaftar',
            'logo.image' => 'File harus berupa gambar',
            'logo.max' => 'Ukuran logo maksimal 2MB'
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'logo' => $data['logo'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Brand berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'nullable'
        ], [
            'name.required' => 'Nama brand wajib diisi',
            'name.unique' => 'Nama brand sudah terdaftar',
            'logo.image' => 'File harus berupa gambar',
            'logo.max' => 'Ukuran logo maksimal 2MB'
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'logo' => $data['logo'] ?? $brand->logo,
            'description' => $data['description'] ?? $brand->description,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Brand berhasil diperbarui');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()->back()->with('success', 'Brand berhasil dihapus');
    }
}
