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
    @endphp

    @section('seo_title', $product->name)
    @section('seo_description', $productSeo['description'] ?: 'Belanja ' . $product->name . ' dari koleksi FURE dengan bahan nyaman, warna elegan, dan tampilan modest yang rapi.')
    @section('seo_keywords', $productSeo['keywords'])
    @section('seo_image', $productSeo['image'])
    @section('canonical', route('collections.show', $product->slug))
    @section('og_type', 'product')

    @push('seo')
        <script type="application/ld+json">{!! json_encode($productSeo['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
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

                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" id="wishlistBtn"
                                    data-product-id="{{ $product->id }}"
                                    data-in-wishlist="{{ $inWishlist ? '1' : '0' }}"
                                    data-auth="{{ $isAuthenticated ? '1' : '0' }}"
                                    class="inline-flex w-full items-center justify-center gap-3 border px-5 py-4 text-xs font-bold uppercase tracking-[0.18em] transition
                                        {{ $inWishlist
                                            ? 'border-brand-primary bg-[#f8f3ee] text-brand-primary'
                                            : 'border-brand-secondary/60 bg-white text-brand-dark hover:border-brand-primary' }}">
                                    <i id="wishlistIcon" class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                    <span id="wishlistLabel">{{ $inWishlist ? 'Tersimpan' : 'Wishlist' }}</span>
                                </button>
                                <button type="button" id="shareBtn"
                                    class="inline-flex w-full items-center justify-center gap-3 border border-brand-secondary/60 bg-white px-5 py-4 text-xs font-bold uppercase tracking-[0.18em] text-brand-dark transition hover:border-brand-primary hover:text-brand-primary">
                                    <i class="fa-solid fa-share-nodes"></i>
                                    <span>Bagikan</span>
                                </button>
                            </div>
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
                                            @if(!empty($review->images))
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @foreach($review->images as $img)
                                                        <a href="{{ asset('storage/' . $img) }}" target="_blank" rel="noopener">
                                                            <img src="{{ asset('storage/' . $img) }}" class="h-16 w-16 object-cover border border-brand-secondary/30 hover:opacity-80 transition" alt="Foto ulasan">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
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
                                        @if(!empty($review->images))
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach($review->images as $img)
                                                    <a href="{{ asset('storage/' . $img) }}" target="_blank" rel="noopener">
                                                        <img src="{{ asset('storage/' . $img) }}" class="h-16 w-16 object-cover border border-brand-secondary/30 hover:opacity-80 transition" alt="Foto ulasan">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
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

    {{-- Share Modal --}}
    <div id="shareModal" class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="shareModalBackdrop"></div>
        <div class="relative z-10 w-full max-w-sm bg-white p-6 sm:rounded-none shadow-2xl mx-0 sm:mx-4">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-bold uppercase tracking-[0.2em] text-brand-dark">Bagikan Produk</h3>
                <button type="button" id="closeShareModal" class="text-brand-dark/40 hover:text-brand-dark transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="mb-5 flex items-center gap-3 border border-brand-secondary/40 bg-[#f8f3ee] p-3">
                @if($primaryImage)
                    <img src="{{ asset('storage/' . $primaryImage->image_url) }}" class="h-12 w-10 object-cover flex-shrink-0" alt="{{ $product->name }}">
                @endif
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-brand-dark truncate">{{ $product->name }}</p>
                    <p class="text-xs text-brand-primary font-bold mt-0.5">Rp{{ number_format($displayPrice, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3 mb-5">
                <a id="shareWhatsapp" href="#" target="_blank" rel="noopener"
                    class="flex flex-col items-center gap-2 p-3 hover:bg-[#f8f3ee] transition rounded-sm">
                    <div class="flex h-11 w-11 items-center justify-center bg-[#25D366] text-white">
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-brand-dark/60">WhatsApp</span>
                </a>
                <a id="shareTelegram" href="#" target="_blank" rel="noopener"
                    class="flex flex-col items-center gap-2 p-3 hover:bg-[#f8f3ee] transition rounded-sm">
                    <div class="flex h-11 w-11 items-center justify-center bg-[#229ED9] text-white">
                        <i class="fa-brands fa-telegram text-xl"></i>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-brand-dark/60">Telegram</span>
                </a>
                <a id="shareFacebook" href="#" target="_blank" rel="noopener"
                    class="flex flex-col items-center gap-2 p-3 hover:bg-[#f8f3ee] transition rounded-sm">
                    <div class="flex h-11 w-11 items-center justify-center bg-[#1877F2] text-white">
                        <i class="fa-brands fa-facebook-f text-xl"></i>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-brand-dark/60">Facebook</span>
                </a>
                <a id="shareTwitter" href="#" target="_blank" rel="noopener"
                    class="flex flex-col items-center gap-2 p-3 hover:bg-[#f8f3ee] transition rounded-sm">
                    <div class="flex h-11 w-11 items-center justify-center bg-black text-white">
                        <i class="fa-brands fa-x-twitter text-xl"></i>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-brand-dark/60">X (Twitter)</span>
                </a>
            </div>

            <button type="button" id="copyLinkBtn"
                class="flex w-full items-center justify-between gap-3 border border-brand-secondary/40 bg-[#f8f3ee] px-4 py-3 text-left transition hover:border-brand-primary group">
                <span id="copyLinkText" class="text-xs font-semibold text-brand-dark/60 truncate flex-1">{{ route('collections.show', $product->slug) }}</span>
                <span class="flex-shrink-0 text-[10px] font-bold uppercase tracking-[0.16em] text-brand-primary group-hover:text-brand-dark transition">
                    <i class="fa-solid fa-copy mr-1"></i>Salin
                </span>
            </button>
        </div>
    </div>

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

        const defaultProductImage = "{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}";

        function findMatchingVariant() {
            const match = variants.find(v => v.attributes.every(attr => selectedChoices[attr.attribute_name] === attr.attribute_value));

            if (!match) return;

            $('#selectedVariantId').val(match.id);
            $('#displayPrice').text('Rp' + new Intl.NumberFormat('id-ID').format(match.price));

            if (match.compare_price && match.compare_price > match.price) {
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

            // Ganti foto utama ke foto variant jika ada
            if (match.image) {
                const variantImageUrl = '/storage/' + match.image;
                $('#mainImage').attr('src', variantImageUrl);
            } else if (defaultProductImage) {
                $('#mainImage').attr('src', defaultProductImage);
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

                @if(!$isAuthenticated)
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

        // Share functionality
        const shareUrl = "{{ route('collections.show', $product->slug) }}";
        const shareTitle = "{{ addslashes($product->name) }}";
        const shareText = shareTitle + ' - Rp' + new Intl.NumberFormat('id-ID').format({{ $displayPrice }});

        function openShareModal() {
            const encodedUrl = encodeURIComponent(shareUrl);
            const encodedText = encodeURIComponent(shareText + '\n' + shareUrl);

            $('#shareWhatsapp').attr('href', 'https://wa.me/?text=' + encodedText);
            $('#shareTelegram').attr('href', 'https://t.me/share/url?url=' + encodedUrl + '&text=' + encodeURIComponent(shareText));
            $('#shareFacebook').attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl);
            $('#shareTwitter').attr('href', 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareText) + '&url=' + encodedUrl);

            $('#shareModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeShareModal() {
            $('#shareModal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $('#shareBtn').on('click', function () {
            if (navigator.share) {
                navigator.share({ title: shareTitle, text: shareText, url: shareUrl }).catch(() => {});
            } else {
                openShareModal();
            }
        });

        $('#closeShareModal, #shareModalBackdrop').on('click', closeShareModal);

        $('#copyLinkBtn').on('click', function () {
            navigator.clipboard.writeText(shareUrl).then(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Link disalin!',
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 2000, timerProgressBar: true,
                });
                closeShareModal();
            }).catch(function () {
                const el = document.createElement('textarea');
                el.value = shareUrl;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                Swal.fire({
                    icon: 'success',
                    title: 'Link disalin!',
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 2000, timerProgressBar: true,
                });
                closeShareModal();
            });
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') closeShareModal();
        });
    </script>
@endpush
