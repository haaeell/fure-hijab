<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = Courier::orderBy('sort_order')->get();
        return view('master.couriers.index', compact('couriers'));
    }

    public function toggle($id)
    {
        $courier = Courier::findOrFail($id);
        $courier->update(['is_active' => !$courier->is_active]);
        Cache::forget('checkout.active_couriers');

        $label = $courier->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Kurir {$courier->name} berhasil {$label}.");
    }

    public function uploadLogo(Request $request, $id)
    {
        $request->validate(['logo' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:1024']);

        $courier = Courier::findOrFail($id);
        if ($courier->logo) {
            Storage::disk('public')->delete($courier->logo);
        }
        $courier->update(['logo' => $request->file('logo')->store('couriers', 'public')]);
        Cache::forget('checkout.active_couriers');

        return redirect()->back()->with('success', 'Logo berhasil diperbarui.');
    }

    public function destroyLogo($id)
    {
        $courier = Courier::findOrFail($id);
        if ($courier->logo) {
            Storage::disk('public')->delete($courier->logo);
            $courier->update(['logo' => null]);
            Cache::forget('checkout.active_couriers');
        }
        return redirect()->back()->with('success', 'Logo berhasil dihapus.');
    }
}
