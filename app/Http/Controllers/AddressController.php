<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'receiver_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'subdistrict' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'biteship_area_id' => 'nullable|string|max:255',
            'address' => 'required|string',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ]);

        $user = Auth::user();

        if ($request->has('is_default') || $user->addresses()->count() == 0) {
            $user->addresses()->update(['is_default' => false]);
            $isDefault = true;
        } else {
            $isDefault = false;
        }

        UserAddress::create([
            'user_id' => $user->id,
            'label' => $request->label,
            'receiver_name' => $request->receiver_name,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'subdistrict' => $request->subdistrict,
            'postal_code' => $request->postal_code,
            'biteship_area_id' => $request->biteship_area_id,
            'address' => $request->address,
            'is_default' => $isDefault,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        return back()->with('success', 'Alamat berhasil ditambahkan!');
    }
}
