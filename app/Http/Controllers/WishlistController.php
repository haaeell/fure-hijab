<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with([
            'product.images' => fn($q) => $q->where('is_primary', true),
            'product.variants' => fn($q) => $q->orderBy('price'),
            'product.category',
        ])
        ->where('user_id', auth()->id())
        ->latest()
        ->paginate(12);

        return view('user.wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['in_wishlist' => false, 'message' => 'Dihapus dari wishlist']);
        }

        Wishlist::create([
            'user_id'    => auth()->id(),
            'product_id' => $request->product_id,
        ]);

        return response()->json(['in_wishlist' => true, 'message' => 'Ditambahkan ke wishlist']);
    }
}
