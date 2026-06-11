@extends('layouts.customer')

@section('title', $product->name . ' - FURE')

@section('content')
    @php
        $displayPrice = $product->has_variant && $product->variants->count() > 0
            ? $product->variants->first()->price
            : $product->price;

        $displayComparePrice = $product->has_variant && $product->variants->count() > 0
            ? $product->variants->first()->compare_price
            : $product->compare_price;

        $displayStock = $product->has_variant && $product->variants->count() > 0
            ? $product->variants->first()->stock
            : $product->stock;

        $primaryImage = $product->images->where('is_primary', true)->first();
    @endphp

    <section class="py-12 bg-white px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <nav class="flex text-xs font-bold text-gray-400 uppercase tracking-widest mb-8">
                <a href="/" class="hover:text-brand-primary transition-colors">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('collections.index') }}" class="hover:text-brand-primary transition-colors">Koleksi</a>
                <span class="mx-2">/</span>
                <span class="text-brand-dark">{{ $product->name }}</span>
            </nav>

            <div class="flex flex-col lg:flex-row gap-12">
                <div class="w-full  lg:w-4/12 space-y-4">
                    <div
                        class="relative w-full max-w-[340px] mx-auto rounded-[32px] overflow-hidden bg-gray-50 border border-gray-100 shadow-sm aspect-[3/4]">
                        <img id="mainImage"
                            src="{{ asset('storage/' . ($primaryImage ? $primaryImage->image_url : 'default.jpg')) }}"
                            class="w-full h-full object-cover transition-all duration-500 hover:scale-105">

                        @if($displayComparePrice > $displayPrice)
                            <div
                                class="absolute top-4 left-4 px-3 py-1.5 bg-red-500 text-white text-xs font-black rounded-full shadow-lg">
                                SALE
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-center gap-2 overflow-x-auto no-scrollbar pb-2">
                        @foreach($product->images as $img)
                            <button onclick="changeImage('{{ asset('storage/' . $img->image_url) }}')"
                                class="flex-none w-12 h-12 rounded-lg overflow-hidden border-2 {{ $img->is_primary ? 'border-brand-primary' : 'border-transparent' }} hover:border-brand-primary transition-all shadow-sm">
                                <img src="{{ asset('storage/' . $img->image_url) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="w-full lg:w-8/12  space-y-8">
                    <div>
                        <span
                            class="text-brand-primary font-bold text-xs uppercase tracking-[0.2em] mb-2 block">{{ $product->category->name }}</span>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-brand-dark leading-tight">{{ $product->name }}
                        </h1>

                        <div class="flex items-center gap-4 mt-4">
                            <div id="displayPrice"
                                class="flex items-center text-brand-dark font-black text-2xl md:text-3xl">
                                Rp{{ number_format($displayPrice, 0, ',', '.') }}
                            </div>
                            @if($displayComparePrice > $displayPrice)
                                <div id="displayComparePrice" class="text-gray-300 line-through font-bold text-lg">
                                    Rp{{ number_format($displayComparePrice, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="h-px bg-gray-100 w-full"></div>

                    <div class="prose prose-sm max-w-none text-gray-500 leading-relaxed">
                        <p class="font-bold text-brand-dark text-sm uppercase tracking-widest mb-2">Deskripsi Produk</p>
                        <div class="text-gray-600">
                            {!! $product->description !!}
                        </div>
                    </div>

                    <form id="addToCartForm" class="space-y-8">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="product_variant_id" id="selectedVariantId">

                        @if($product->has_variant && $product->variants->count() > 0)
                            @php
                                $groupedAttributes = [];
                                foreach ($product->variants as $variant) {
                                    foreach ($variant->attributes as $attr) {
                                        $groupedAttributes[$attr->attribute_name][] = $attr->attribute_value;
                                    }
                                }
                                foreach ($groupedAttributes as $name => $values) {
                                    $groupedAttributes[$name] = array_unique($values);
                                }
                            @endphp

                            <div class="space-y-6" id="variantSelection">
                                @foreach($groupedAttributes as $attrName => $values)
                                    <div class="variant-group">
                                        <p class="font-bold text-brand-dark text-xs uppercase tracking-widest mb-3">{{ $attrName }}
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($values as $value)
                                                <button type="button" data-type="{{ $attrName }}" data-value="{{ $value }}"
                                                    class="variant-btn px-4 py-2 border-2 border-gray-100 rounded-xl text-sm font-bold text-gray-500 hover:border-brand-primary transition-all">
                                                    {{ $value }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center gap-6">
                            <div class="flex items-center bg-gray-50 rounded-2xl border border-gray-100 p-1">
                                <button type="button" onclick="adjustQty(-1)"
                                    class="w-10 h-10 flex items-center justify-center text-brand-dark hover:bg-white rounded-xl transition-all shadow-sm">-</button>
                                <input type="number" name="quantity" id="qtyInput" value="1" min="1"
                                    max="{{ $displayStock }}"
                                    class="w-12 text-center bg-transparent font-bold text-brand-dark outline-none">
                                <button type="button" onclick="adjustQty(1)"
                                    class="w-10 h-10 flex items-center justify-center text-brand-dark hover:bg-white rounded-xl transition-all shadow-sm">+</button>
                            </div>
                            <p class="text-xs font-bold text-gray-400">Stok: <span class="text-brand-dark"
                                    id="productStock">{{ $displayStock }}</span></p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" id="btnAddToCart"
                                class="flex-1 py-4 bg-white text-brand-dark border-2 border-brand-primary/30 font-bold rounded-[20px] hover:bg-soft-mint transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest text-sm">
                                <i class="fa-solid fa-cart-plus"></i>
                                <span id="btnText">Tambah ke Keranjang</span>
                            </button>
                            <button type="button" id="btnBuyNow"
                                class="flex-1 py-4 bg-brand-primary text-white font-bold rounded-[20px] shadow-lg shadow-brand-primary/20 hover:shadow-brand-primary/40 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest text-sm">
                                <i class="fa-solid fa-bolt"></i>
                                <span>Beli Sekarang</span>
                            </button>
                            <button type="button"
                                class="px-6 py-4 bg-white text-brand-dark border-2 border-brand-dark/10 font-bold rounded-[20px] hover:bg-brand-dark hover:text-white transition-all active:scale-95">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>
                    </form>

                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-soft-mint/50 border border-brand-primary/10">
                            <i class="fa-solid fa-truck-fast text-brand-primary text-lg"></i>
                            <span class="text-[10px] font-bold text-brand-dark uppercase">Pengiriman Cepat</span>
                        </div>
                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-soft-blue/50 border border-blue-100">
                            <i class="fa-solid fa-shield-check text-blue-500 text-lg"></i>
                            <span class="text-[10px] font-bold text-brand-dark uppercase">Kualitas Terjamin</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-16">
                <div class="flex items-center gap-4 mb-8">
                    <h2 class="text-2xl font-extrabold text-brand-dark">Ulasan <span
                            class="text-brand-primary">Pembeli</span></h2>
                    <span class="text-sm font-bold text-gray-400">({{ $totalReviews }} ulasan)</span>
                </div>

                @if($totalReviews > 0)
                    <div class="flex items-center gap-4 mb-8 p-6 bg-soft-mint/30 rounded-2xl border border-brand-primary/10">
                        <div class="text-center">
                            <div class="text-5xl font-black text-brand-dark">{{ number_format($averageRating, 1) }}</div>
                            <div class="flex justify-center gap-1 mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fa-star text-sm {{ $i <= round($averageRating) ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <div class="text-xs text-gray-400 mt-1">dari 5</div>
                        </div>
                    </div>
                @endif

                @forelse($product->reviews as $review)
                    <div class="py-6 border-b border-gray-100 last:border-0">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-brand-primary/10 flex items-center justify-center flex-shrink-0">
                                <span
                                    class="text-brand-primary font-bold text-sm">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-bold text-brand-dark text-sm">{{ $review->user->name }}</p>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex gap-1 my-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fa-star text-xs {{ $i <= $review->rating ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-600 text-sm mt-2">{{ $review->comment }}</p>
                                @if($review->images)
                                    <div class="flex gap-2 mt-3">
                                        @foreach($review->images as $img)
                                            <img src="{{ asset('storage/' . $img) }}"
                                                class="w-16 h-16 rounded-xl object-cover border border-gray-100">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400">
                        <i class="fa-regular fa-comment text-4xl mb-3 block"></i>
                        <p class="font-bold">Belum ada ulasan untuk produk ini.</p>
                    </div>
                @endforelse
            </div>

            @if($relatedProducts->count() > 0)
                <div class="mt-24">
                    <h2 class="text-2xl font-extrabold text-brand-dark mb-8">Produk <span
                            class="text-brand-primary">Terkait</span></h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($relatedProducts as $rel)
                            @include('user.components.product-card', ['product' => $rel, 'isFlashSale' => $rel->compare_price > $rel->price])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const variants = @json($product->variants->load('attributes'));
        let selectedChoices = {};

        function changeImage(src) {
            $('#mainImage').fadeOut(200, function () {
                $(this).attr('src', src).fadeIn(200);
            });
        }

        function adjustQty(val) {
            let input = $('#qtyInput');
            let stock = parseInt($('#productStock').text());
            let current = parseInt(input.val());
            let next = current + val;
            if (next >= 1 && next <= stock) input.val(next);
        }

        function findMatchingVariant() {
            const match = variants.find(v => {
                return v.attributes.every(attr => selectedChoices[attr.attribute_name] === attr.attribute_value);
            });

            if (match) {
                $('#selectedVariantId').val(match.id);
                $('#displayPrice').text('Rp' + new Intl.NumberFormat('id-ID').format(match.price));

                if (match.compare_price > match.price) {
                    $('#displayComparePrice').text('Rp' + new Intl.NumberFormat('id-ID').format(match.compare_price)).show();
                } else {
                    $('#displayComparePrice').hide();
                }

                $('#productStock').text(match.stock);
                $('#qtyInput').attr('max', match.stock);
                if (parseInt($('#qtyInput').val()) > match.stock) $('#qtyInput').val(match.stock);

                if (match.stock <= 0) {
                    $('#btnAddToCart').prop('disabled', true).addClass('opacity-50 text-gray-300 pointer-events-none');
                    $('#btnText').text('Stok Habis');
                } else {
                    $('#btnAddToCart').prop('disabled', false).removeClass('opacity-50 text-gray-300 pointer-events-none');
                    $('#btnText').text('Tambah ke Keranjang');
                }
            }
        }

        $(document).ready(function () {
            $('.variant-btn').on('click', function () {
                const type = $(this).data('type');
                const value = $(this).data('value');

                $(this).closest('.variant-group').find('.variant-btn')
                    .removeClass('border-brand-primary bg-soft-mint text-brand-primary')
                    .addClass('border-gray-100 text-gray-500');

                $(this).addClass('border-brand-primary bg-soft-mint text-brand-primary')
                    .removeClass('border-gray-100 text-gray-500');

                selectedChoices[type] = value;
                if (Object.keys(selectedChoices).length === $('.variant-group').length) {
                    findMatchingVariant();
                }
            });

            $('#addToCartForm').on('submit', function (e) {
                e.preventDefault();

                submitCartAction('cart');
            });

            $('#btnBuyNow').on('click', function () {
                submitCartAction('buy-now');
            });

            function submitCartAction(action) {
                @if($product->has_variant)
                    if (!$('#selectedVariantId').val()) {
                        Swal.fire({ icon: 'warning', title: 'Pilih Varian', text: 'Silakan pilih warna/ukuran terlebih dahulu.' });
                        return;
                    }
                @endif

                @if(!Auth::check())
                    window.location.href = "{{ route('login') }}";
                    return;
                @endif

                let btn = action === 'buy-now' ? $('#btnBuyNow') : $('#btnAddToCart');
                btn.prop('disabled', true).addClass('opacity-70');

                $.ajax({
                    url: action === 'buy-now' ? "{{ route('cart.buy-now') }}" : "{{ route('cart.store') }}",
                    method: "POST",
                    data: $('#addToCartForm').serialize(),
                    success: function (response) {
                        if (response.status === 'success') {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                                return;
                            }

                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, showConfirmButton: false, timer: 1500 });
                            window.location.reload();
                        }
                    },
                    error: function (xhr) {
                        const response = xhr.responseJSON;

                        if (response?.status === 'blocked' && response.redirect) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pesanan Belum Dibayar',
                                text: response.message,
                                confirmButtonText: 'Lihat Pesanan',
                            }).then(() => window.location.href = response.redirect);
                            return;
                        }

                        Swal.fire({ icon: 'error', title: 'Oops...', text: response?.message || 'Gagal menambah ke keranjang.' });
                    },
                    complete: function () {
                        btn.prop('disabled', false).removeClass('opacity-70');
                    }
                });
            }

            function updateCartCount() {
                $.ajax({
                    url: "{{ route('home') }}",
                    method: "GET",
                    success: function (data) {
                        let newCount = $(data).find('#navbar-cart-count').text();
                        $('#navbar-cart-count').text(newCount);
                    }
                });
            }
        });
    </script>
@endpush
