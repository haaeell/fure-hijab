@extends('layouts.customer')

@section('title', 'Keranjang Belanja - FURE')

@section('content')
    <section class="py-12 bg-gray-50 min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center gap-4 mb-10">
                <a href="/"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-brand-dark shadow-sm hover:bg-brand-primary hover:text-white transition-all">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-brand-dark">Keranjang Belanja</h1>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-2/3 space-y-4">
                    @forelse($carts as $item)
                        <div
                            class="bg-white p-4 md:p-6 rounded-[32px] shadow-sm border border-gray-100 flex gap-4 md:gap-6 items-center group transition-all hover:shadow-md">
                            <div
                                class="w-24 h-32 md:w-32 md:h-40 rounded-2xl overflow-hidden bg-gray-50 flex-shrink-0 border border-gray-50">
                                @php $primaryImage = $item->product->images->where('is_primary', true)->first(); @endphp
                                <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>

                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p
                                            class="text-[10px] md:text-xs font-bold text-brand-primary uppercase tracking-widest mb-1">
                                            {{ $item->product->category->name }}
                                        </p>
                                        <h3 class="font-bold text-brand-dark text-sm md:text-lg leading-tight mb-2">
                                            {{ $item->product->name }}
                                        </h3>

                                        @if($item->variant)
                                            <div class="flex flex-wrap gap-2 mb-2">
                                                @foreach($item->variant->attributes as $attr)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                        {{ $attr->attribute_name }}: {{ $attr->attribute_value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors p-2">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-2">
                                    <p class="text-base md:text-xl font-extrabold text-brand-dark">
                                        Rp{{ number_format($item->product->price, 0, ',', '.') }}
                                    </p>

                                    <div
                                        class="flex items-center gap-1 bg-soft-mint rounded-xl p-1 w-fit border border-brand-primary/10">
                                        <button type="button"
                                            class="update-qty w-8 h-8 rounded-lg flex items-center justify-center text-brand-dark hover:bg-white transition-all"
                                            data-id="{{ $item->id }}" data-action="minus">
                                            <i class="fa-solid fa-minus text-[10px]"></i>
                                        </button>
                                        <input type="text" value="{{ $item->qty }}"
                                            class="w-10 text-center bg-transparent font-bold text-sm text-brand-dark outline-none"
                                            readonly>
                                        <button type="button"
                                            class="update-qty w-8 h-8 rounded-lg flex items-center justify-center text-brand-dark hover:bg-white transition-all"
                                            data-id="{{ $item->id }}" data-action="plus">
                                            <i class="fa-solid fa-plus text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-[40px] p-12 text-center border-2 border-dashed border-gray-200">
                            <div class="w-20 h-20 bg-soft-mint rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-cart-shopping text-brand-primary text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-brand-dark mb-2">Keranjangmu Kosong</h3>
                            <p class="text-gray-400 mb-8 max-w-xs mx-auto text-sm">Sepertinya kamu belum memilih koleksi hijab
                                favoritmu.</p>
                            <a href="/"
                                class="inline-flex px-8 py-3 bg-brand-primary text-white font-bold rounded-xl shadow-lg shadow-brand-primary/20 hover:-translate-y-1 transition-all">
                                Mulai Belanja
                            </a>
                        </div>
                    @endforelse
                </div>

                <div class="w-full lg:w-1/3">
                    <div class="bg-brand-dark text-white p-8 rounded-[40px] shadow-2xl lg:sticky lg:top-28">
                        <h2 class="text-xl font-bold mb-6">Ringkasan Belanja</h2>

                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-sm text-white/70">
                                <span>Total Produk</span>
                                <span class="font-bold text-white">{{ $carts->sum('quantity') }} Item</span>
                            </div>
                            <div class="flex justify-between text-sm text-white/70">
                                <span>Subtotal</span>
                                <span
                                    class="font-bold text-white">Rp{{ number_format($total_price ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-white/70">
                                <span>Biaya Pengiriman</span>
                                <span class="font-bold text-white italic">Dihitung di checkout</span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-white/10 mb-8">
                            <div class="flex justify-between items-end">
                                <span class="text-sm">Total Pembayaran</span>
                                <span
                                    class="text-2xl font-extrabold text-brand-primary">Rp{{ number_format($total_price ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <a href="/checkout"
                            class="group relative w-full py-4 bg-brand-primary text-brand-dark font-bold rounded-2xl flex items-center justify-center gap-3 overflow-hidden shadow-xl hover:shadow-brand-primary/40 transition-all hover:-translate-y-1 active:scale-95">
                            <div
                                class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover:animate-shimmer">
                            </div>
                            <span class="relative z-10">Lanjutkan ke Checkout</span>
                            <i
                                class="fa-solid fa-chevron-right text-xs relative z-10 group-hover:translate-x-1 transition-transform"></i>
                        </a>

                        <div
                            class="mt-6 flex items-center justify-center gap-4 text-[10px] text-white/40 uppercase tracking-widest font-bold">
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-shield-check"></i> 100% Aman
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-truck-fast"></i> Pengiriman Cepat
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.update-qty').click(function () {
                let btn = $(this);
                let id = btn.data('id');
                let action = btn.data('action');
                let input = btn.siblings('input');
                let currentQty = parseInt(input.val());
                let newQty = action === 'plus' ? currentQty + 1 : currentQty - 1;

                if (newQty < 1) return;

                $.ajax({
                    url: `/cart/update/${id}`,
                    method: 'PATCH',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        quantity: newQty
                    },
                    success: function (response) {
                        location.reload();
                    }
                });
            });
        });
    </script>
@endpush