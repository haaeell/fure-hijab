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

        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
        $galleryImages = $product->images->count() > 0 ? $product->images : collect([$primaryImage])->filter();
        $productSeoDescription = $product->short_description
            ?: \Illuminate\Support\Str::limit(trim(strip_tags($product->description)), 155, '');
        $productSeoImage = $primaryImage
            ? asset('storage/' . $primaryImage->image_url)
            : asset('favicon.ico');
    @endphp

    @section('seo_title', $product->name)
    @section('seo_description', $productSeoDescription ?: 'Belanja ' . $product->name . ' dari koleksi FURE dengan bahan nyaman, warna elegan, dan tampilan modest yang rapi.')
    @section('seo_keywords', implode(', ', array_filter([$product->name, $product->category->name ?? null, $product->brand->name ?? null, 'hijab premium', 'modest wear', 'FURE'])))
    @section('seo_image', $productSeoImage)
    @section('canonical', route('collections.show', $product->slug))
    @section('og_type', 'product')

    @push('seo')
        @php
            $productImages = $galleryImages->map(fn($image) => asset('storage/' . $image->image_url))->values()->all();
            $productSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $product->name,
                'image' => $productImages ?: [$productSeoImage],
                'description' => $productSeoDescription ?: trim(strip_tags($product->description)),
                'sku' => $product->sku ?: 'FURE-' . $product->id,
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $product->brand->name ?? \App\Models\Setting::getValue('store_name', 'FURE'),
                ],
                'category' => $product->category->name ?? 'Hijab',
                'offers' => [
                    '@type' => 'Offer',
                    'url' => route('collections.show', $product->slug),
                    'priceCurrency' => 'IDR',
                    'price' => (float) $displayPrice,
                    'availability' => $displayStock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'itemCondition' => 'https://schema.org/NewCondition',
                ],
            ];

            if ($totalReviews > 0) {
                $productSchema['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => round((float) $averageRating, 1),
                    'reviewCount' => $totalReviews,
                ];
            }
        @endphp
        <script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="mobile-action-safe-space bg-[#f8f3ee]">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <nav class="mb-8 flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.24em] text-brand-dark/45">
                <a href="/" class="transition hover:text-brand-primary">Home</a>
                <span class="text-brand-secondary">/</span>
                <a href="{{ route('collections.index') }}" class="transition hover:text-brand-primary">Collections</a>
                <span class="text-brand-secondary">/</span>
                <span class="text-brand-dark">{{ $product->name }}</span>
            </nav>

            <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-start">
                <div class="space-y-4">
                    <div class="overflow-hidden bg-white">
                        <div class="relative aspect-[4/5] bg-[#eee5dc]">
                            <img id="mainImage"
                                src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/900x1125?text=FURE' }}"
                                class="h-full w-full object-cover" alt="{{ $product->name }}">

                            @if($displayComparePrice > $displayPrice)
                                <div class="absolute left-4 top-4 bg-brand-dark px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-white">
                                    Sale
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($galleryImages->count() > 1)
                        <div class="no-scrollbar flex gap-3 overflow-x-auto pb-1">
                            @foreach($galleryImages as $img)
                                <button type="button" onclick="changeImage('{{ asset('storage/' . $img->image_url) }}')"
                                    class="h-20 w-16 flex-none overflow-hidden border {{ $img->is_primary ? 'border-brand-primary' : 'border-brand-secondary/60' }} bg-white transition hover:border-brand-primary">
                                    <img src="{{ asset('storage/' . $img->image_url) }}" class="h-full w-full object-cover" alt="{{ $product->name }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lg:sticky lg:top-36">
                    <div class="bg-white p-6 sm:p-8">
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-brand-primary">
                            {{ $product->category->name }}
                        </p>
                        <h1 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">
                            {{ $product->name }}
                        </h1>

                        <div class="mt-5 flex items-end gap-4">
                            <div id="displayPrice" class="text-3xl font-semibold text-brand-dark">
                                Rp{{ number_format($displayPrice, 0, ',', '.') }}
                            </div>
                            @if($displayComparePrice > $displayPrice)
                                <div id="displayComparePrice" class="pb-1 text-sm font-semibold text-brand-dark/35 line-through">
                                    Rp{{ number_format($displayComparePrice, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-dark/45">Stok</p>
                                <p class="mt-2 text-lg font-semibold text-brand-dark" id="productStock">{{ $displayStock }}</p>
                            </div>
                            <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-dark/45">Rating</p>
                                <p class="mt-2 text-lg font-semibold text-brand-dark">
                                    {{ number_format($product->reviews->avg('rating') ?: 0, 1) }}/5
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-brand-secondary/40 pt-6">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-dark/45">Deskripsi</p>
                            <div class="prose prose-sm mt-4 max-w-none text-brand-dark/65">
                                {!! $product->description !!}
                            </div>
                        </div>

                        <form id="addToCartForm" class="mt-7 space-y-6">
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

                                <div class="space-y-5" id="variantSelection">
                                    @foreach($groupedAttributes as $attrName => $values)
                                        <div class="variant-group">
                                            <p class="mb-3 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-dark/45">
                                                {{ $attrName }}
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($values as $value)
                                                    <button type="button" data-type="{{ $attrName }}" data-value="{{ $value }}"
                                                        class="variant-btn border border-brand-secondary/60 bg-white px-4 py-2 text-sm font-semibold text-brand-dark/65 transition hover:border-brand-primary hover:text-brand-primary">
                                                        {{ $value }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center gap-4">
                                <div class="flex items-center border border-brand-secondary/60 bg-white">
                                    <button type="button" onclick="adjustQty(-1)"
                                        class="h-12 w-12 text-brand-dark transition hover:bg-[#f8f3ee]">-</button>
                                    <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="{{ $displayStock }}"
                                        class="h-12 w-14 border-x border-brand-secondary/60 bg-transparent text-center text-sm font-semibold outline-none">
                                    <button type="button" onclick="adjustQty(1)"
                                        class="h-12 w-12 text-brand-dark transition hover:bg-[#f8f3ee]">+</button>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-dark/45">
                                    Sisa <span class="text-brand-dark" id="productStockLabel">{{ $displayStock }}</span>
                                </p>
                            </div>

                            <div class="desktop-only-action grid gap-3 sm:grid-cols-2">
                                <button type="submit" id="btnAddToCart"
                                    class="js-add-to-cart inline-flex items-center justify-center gap-3 border border-brand-primary bg-white px-5 py-4 text-xs font-bold uppercase tracking-[0.18em] text-brand-dark transition hover:bg-[#eee5dc]">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    <span class="js-add-text">Tambah ke Keranjang</span>
                                </button>
                                <button type="button"
                                    class="js-buy-now inline-flex items-center justify-center gap-3 bg-brand-primary px-5 py-4 text-xs font-bold uppercase tracking-[0.18em] text-white transition hover:bg-brand-dark">
                                    <i class="fa-solid fa-bolt"></i>
                                    <span>Beli Sekarang</span>
                                </button>
                            </div>

                            <button type="button" id="wishlistBtn"
                                data-product-id="{{ $product->id }}"
                                data-in-wishlist="{{ $inWishlist ? '1' : '0' }}"
                                data-auth="{{ Auth::check() ? '1' : '0' }}"
                                class="inline-flex w-full items-center justify-center gap-3 border px-5 py-4 text-xs font-bold uppercase tracking-[0.18em] transition
                                    {{ $inWishlist
                                        ? 'border-brand-primary bg-[#f8f3ee] text-brand-primary'
                                        : 'border-brand-secondary/60 bg-white text-brand-dark hover:border-brand-primary' }}">
                                <i id="wishlistIcon" class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                <span id="wishlistLabel">{{ $inWishlist ? 'Tersimpan' : 'Wishlist' }}</span>
                            </button>
                        </form>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-dark/45">Pengiriman</p>
                                <p class="mt-2 text-sm font-semibold text-brand-dark">Cepat & rapi</p>
                            </div>
                            <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-dark/45">Kualitas</p>
                                <p class="mt-2 text-sm font-semibold text-brand-dark">Material terpilih</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-16 grid gap-8 lg:grid-cols-[1fr_340px]">
                <div class="space-y-8">
                    <section class="bg-white p-6 sm:p-8">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">Reviews</p>
                                <h2 class="mt-2 text-2xl font-semibold">Ulasan Pembeli</h2>
                            </div>
                            <span class="text-sm font-semibold text-brand-dark/45">({{ $totalReviews }} ulasan)</span>
                        </div>

                        @if($totalReviews > 0)
                            <div class="mt-6 grid gap-4 sm:grid-cols-[180px_1fr]">
                                <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-5 text-center">
                                    <div class="text-5xl font-semibold text-brand-dark">{{ number_format($averageRating, 1) }}</div>
                                    <div class="mt-2 flex justify-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-star text-xs {{ $i <= round($averageRating) ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    @forelse($product->reviews->take(3) as $review)
                                        <div class="border border-brand-secondary/40 p-4">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="text-sm font-semibold text-brand-dark">{{ $review->user->name }}</p>
                                                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-brand-dark/35">
                                                    {{ $review->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="mt-2 flex gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa-star text-xs {{ $i <= $review->rating ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="mt-3 text-sm leading-6 text-brand-dark/60">{{ $review->comment }}</p>
                                        </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        @forelse($product->reviews->skip(3) as $review)
                            <div class="border-t border-brand-secondary/30 pt-5 mt-5">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center bg-brand-primary/10 text-brand-primary">
                                        <span class="text-sm font-bold">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-semibold text-brand-dark">{{ $review->user->name }}</p>
                                            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-brand-dark/35">
                                                {{ $review->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div class="mt-2 flex gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-star text-xs {{ $i <= $review->rating ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="mt-3 text-sm leading-6 text-brand-dark/60">{{ $review->comment }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            @if($totalReviews === 0)
                                <div class="mt-8 border border-dashed border-brand-secondary/50 p-8 text-center text-brand-dark/45">
                                    Belum ada ulasan untuk produk ini.
                                </div>
                            @endif
                        @endforelse
                    </section>
                </div>

                @if($relatedProducts->count() > 0)
                    <aside class="space-y-4">
                        <div class="bg-white p-6">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">Related</p>
                            <h2 class="mt-2 text-2xl font-semibold">Produk Terkait</h2>
                        </div>
                        <div class="grid gap-4">
                            @foreach($relatedProducts as $rel)
                                @include('user.components.product-card', ['product' => $rel, 'isFlashSale' => $rel->compare_price > $rel->price])
                            @endforeach
                        </div>
                    </aside>
                @endif
            </div>
        </div>
    </section>

    <x-user.components.mobile-bottom-action-bar>
        <div class="grid grid-cols-2 gap-3">
            <button type="submit" form="addToCartForm"
                class="js-add-to-cart flex min-h-[52px] items-center justify-center gap-2 rounded-2xl border border-brand-primary bg-white px-3 text-[11px] font-black uppercase tracking-tight text-brand-dark">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="js-add-text">Masukkan Keranjang</span>
            </button>
            <button type="button"
                class="js-buy-now flex min-h-[52px] items-center justify-center gap-2 rounded-2xl bg-brand-primary px-3 text-[11px] font-black uppercase tracking-tight text-white shadow-lg shadow-brand-primary/25">
                <i class="fa-solid fa-bolt"></i>
                <span>Pesan Sekarang</span>
            </button>
        </div>
    </x-user.components.mobile-bottom-action-bar>
@endsection

@push('scripts')
    <script>
        const variants = @json($product->variants->load('attributes'));
        let selectedChoices = {};

        function changeImage(src) {
            $('#mainImage').attr('src', src);
        }

        function adjustQty(val) {
            const input = $('#qtyInput');
            const stock = parseInt($('#productStock').text(), 10) || 1;
            const current = parseInt(input.val(), 10) || 1;
            const next = current + val;
            if (next >= 1 && next <= stock) input.val(next);
        }

        function findMatchingVariant() {
            const match = variants.find(v => v.attributes.every(attr => selectedChoices[attr.attribute_name] === attr.attribute_value));

            if (!match) return;

            $('#selectedVariantId').val(match.id);
            $('#displayPrice').text('Rp' + new Intl.NumberFormat('id-ID').format(match.price));

            if (match.compare_price > match.price) {
                if (!$('#displayComparePrice').length) {
                    $('#displayPrice').after('<div id="displayComparePrice" class="pb-1 text-sm font-semibold text-brand-dark/35 line-through"></div>');
                }
                $('#displayComparePrice').text('Rp' + new Intl.NumberFormat('id-ID').format(match.compare_price)).show();
            } else {
                $('#displayComparePrice').hide();
            }

            $('#productStock').text(match.stock);
            $('#productStockLabel').text(match.stock);
            $('#qtyInput').attr('max', match.stock);

            if (parseInt($('#qtyInput').val(), 10) > match.stock) {
                $('#qtyInput').val(match.stock);
            }

            if (match.stock <= 0) {
                $('.js-add-to-cart').prop('disabled', true).addClass('opacity-50 pointer-events-none');
                $('.js-add-text').text('Stok Habis');
            } else {
                $('.js-add-to-cart').prop('disabled', false).removeClass('opacity-50 pointer-events-none');
                $('.js-add-text').text(function () {
                    return $(this).closest('.desktop-only-action').length ? 'Tambah ke Keranjang' : 'Masukkan Keranjang';
                });
            }
        }

        $(document).ready(function () {
            // Wishlist toggle
            $('#wishlistBtn').on('click', function () {
                if ($(this).data('auth') == 0) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }
                const $btn = $(this);
                $btn.prop('disabled', true);
                $.post("{{ route('wishlist.toggle') }}", {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    product_id: $btn.data('product-id'),
                })
                .done(function (res) {
                    if (res.in_wishlist) {
                        $btn.removeClass('border-brand-secondary/60 bg-white text-brand-dark hover:border-brand-primary')
                            .addClass('border-brand-primary bg-[#f8f3ee] text-brand-primary');
                        $('#wishlistIcon').removeClass('fa-regular').addClass('fa-solid');
                        $('#wishlistLabel').text('Tersimpan');
                    } else {
                        $btn.removeClass('border-brand-primary bg-[#f8f3ee] text-brand-primary')
                            .addClass('border-brand-secondary/60 bg-white text-brand-dark hover:border-brand-primary');
                        $('#wishlistIcon').removeClass('fa-solid').addClass('fa-regular');
                        $('#wishlistLabel').text('Wishlist');
                    }
                    Swal.fire({
                        icon: res.in_wishlist ? 'success' : 'info',
                        title: res.message,
                        toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 2000, timerProgressBar: true,
                    });
                })
                .fail(function () {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.', confirmButtonColor: '#A78B6F' });
                })
                .always(function () { $btn.prop('disabled', false); });
            });

            $('.variant-btn').on('click', function () {
                const type = $(this).data('type');
                const value = $(this).data('value');

                $(this).closest('.variant-group').find('.variant-btn')
                    .removeClass('border-brand-primary bg-[#eee5dc] text-brand-primary')
                    .addClass('border-brand-secondary/60 bg-white text-brand-dark/65');

                $(this).addClass('border-brand-primary bg-[#eee5dc] text-brand-primary')
                    .removeClass('border-brand-secondary/60 bg-white text-brand-dark/65');

                selectedChoices[type] = value;
                if (Object.keys(selectedChoices).length === $('.variant-group').length) {
                    findMatchingVariant();
                }
            });

            $('#addToCartForm').on('submit', function (e) {
                e.preventDefault();
                submitCartAction('cart');
            });

            $('.js-buy-now').on('click', function () {
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

                const btn = action === 'buy-now' ? $('.js-buy-now') : $('.js-add-to-cart');
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

                            window.FureCartDrawer?.reloadAndOpen();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1400,
                                timerProgressBar: true,
                            });
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
        });
    </script>
@endpush
