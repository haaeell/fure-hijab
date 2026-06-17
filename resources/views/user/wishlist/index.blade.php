@extends('layouts.customer')

@section('title', 'Wishlist Saya')

@section('content')
<section class="bg-[#f8f3ee] min-h-screen">
    <div class="bg-white border-b border-brand-secondary/20">
        <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:px-8">
            <nav class="mb-5 flex text-[10px] font-bold uppercase tracking-[0.22em] text-brand-dark/45">
                <a href="/" class="transition hover:text-brand-primary">Home</a>
                <span class="mx-2 text-brand-secondary">/</span>
                <span class="text-brand-dark">Wishlist</span>
            </nav>
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.26em] text-brand-primary">Koleksi Saya</p>
                    <h1 class="text-3xl font-semibold sm:text-4xl">Wishlist</h1>
                </div>
                <p class="text-sm font-semibold text-brand-dark/45">{{ $wishlists->total() }} produk</p>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if($wishlists->count() > 0)
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 lg:gap-4">
                @foreach($wishlists as $item)
                    @php
                        $product = $item->product;
                        $displayPrice = $product->has_variant && $product->variants->count() > 0
                            ? $product->variants->first()->price
                            : $product->price;
                        $primaryImage = $product->images->first();
                    @endphp
                    <div class="group relative bg-white p-2 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-3">
                        <button type="button"
                            class="wishlist-remove absolute right-3 top-3 z-10 flex h-8 w-8 items-center justify-center bg-white/90 text-brand-primary shadow-sm backdrop-blur-sm transition hover:bg-red-50 hover:text-red-500"
                            data-product-id="{{ $product->id }}"
                            title="Hapus dari wishlist">
                            <i class="fa-solid fa-heart text-sm"></i>
                        </button>

                        <a href="{{ route('collections.show', $product->slug) }}" class="product-card block h-full">
                            <div class="relative mb-3 aspect-[3/4] overflow-hidden bg-[#eee5dc]">
                                <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533?text=FURE' }}"
                                    class="product-image h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110"
                                    alt="{{ $product->name }}">
                            </div>

                            <div class="flex flex-col">
                                <p class="mb-1 truncate text-[9px] font-bold uppercase tracking-[0.16em] text-brand-primary md:text-[10px]">
                                    {{ $product->category->name }}
                                </p>
                                <h3 class="mb-2 line-clamp-2 text-xs font-semibold leading-snug text-brand-dark transition-colors group-hover:text-brand-primary md:text-sm">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-sm font-bold text-brand-dark md:text-base">
                                    Rp{{ number_format($displayPrice, 0, ',', '.') }}
                                </p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $wishlists->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="mb-6 flex h-20 w-20 items-center justify-center bg-white shadow-sm">
                    <i class="fa-regular fa-heart text-4xl text-brand-secondary"></i>
                </div>
                <h2 class="text-xl font-semibold text-brand-dark">Wishlist masih kosong</h2>
                <p class="mt-2 text-sm text-brand-dark/50">Simpan produk favoritmu agar mudah ditemukan kembali.</p>
                <a href="{{ route('collections.index') }}"
                    class="mt-8 inline-flex items-center gap-3 bg-brand-primary px-8 py-4 text-xs font-bold uppercase tracking-[0.18em] text-white transition hover:bg-brand-dark">
                    <i class="fa-solid fa-bag-shopping"></i>
                    Jelajahi Produk
                </a>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('.wishlist-remove').on('click', function (e) {
        e.preventDefault();
        const $card = $(this).closest('[class*="relative bg-white"]');
        const productId = $(this).data('product-id');

        $.post("{{ route('wishlist.toggle') }}", {
            _token: $('meta[name="csrf-token"]').attr('content'),
            product_id: productId,
        })
        .done(function (res) {
            $card.fadeOut(300, function () { $(this).remove(); });
            Swal.fire({
                icon: 'info',
                title: res.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
        })
        .fail(function () {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.', confirmButtonColor: '#A78B6F' });
        });
    });
});
</script>
@endpush
