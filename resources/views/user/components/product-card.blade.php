@props(['product', 'isFlashSale' => false])

@php
    $displayPrice = $product->has_variant && $product->variants->count() > 0
        ? $product->variants->first()->price
        : $product->price;

    $displayComparePrice = $product->has_variant && $product->variants->count() > 0
        ? $product->variants->first()->compare_price
        : $product->compare_price;

    $hasDiscount = $displayComparePrice > $displayPrice;
    $diskon      = $hasDiscount ? round((($displayComparePrice - $displayPrice) / $displayComparePrice) * 100) : 0;
    $isOutOfStock = $product->stock <= 0;

    $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
@endphp

<a href="{{ route('collections.show', $product->slug) }}" class="product-card group block h-full">
    <div class="relative flex h-full flex-col bg-transparent">

        @if($isOutOfStock)
            <div class="absolute left-2 top-2 z-10 bg-gray-500 px-2.5 py-1 text-[8px] font-bold uppercase tracking-widest text-white md:text-[10px]">
                Stok Habis
            </div>
        @elseif($isFlashSale || $hasDiscount)
            <div class="absolute left-2 top-2 z-10 flex items-center gap-1 bg-brand-dark px-2.5 py-1 text-[8px] font-bold uppercase tracking-widest text-white md:text-[10px]">
                <i class="fa-solid fa-tag text-[7px]"></i>
                @if($hasDiscount)
                    Hemat {{ $diskon }}%
                @else
                    SALE
                @endif
            </div>
        @endif

        <div class="relative mb-3 aspect-[3/4] overflow-hidden bg-[#eee5dc] {{ $isOutOfStock ? 'opacity-60' : '' }}">
            <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533?text=FURE' }}"
                loading="lazy"
                class="product-image w-full h-full object-cover"
                alt="{{ $product->name }}">

            @if(!$isOutOfStock)
            <div class="card-overlay absolute inset-0 flex items-center justify-center bg-brand-dark/18">
                <div class="card-zoom-icon flex h-9 w-9 items-center justify-center bg-white/95 text-sm text-brand-dark">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            @endif
        </div>

        <div class="flex flex-grow flex-col">
            <div class="mb-1 flex items-center justify-between gap-2">
                <p class="truncate text-[9px] font-bold uppercase tracking-[0.16em] text-brand-primary md:text-[10px]">
                    {{ $product->category->name }}
                </p>
                <div class="flex flex-shrink-0 items-center gap-0.5 text-[9px] text-yellow-400">
                    <i class="fa-solid fa-star"></i>
                    <span class="font-medium text-brand-dark/45">4.8</span>
                </div>
            </div>

            <h3 class="mb-2 line-clamp-2 h-8 text-xs font-semibold leading-snug text-brand-dark transition-colors duration-300 group-hover:text-brand-primary md:text-sm md:leading-snug">
                {{ $product->name }}
            </h3>

            <div class="mt-auto flex items-end justify-between gap-2">
                <div class="space-y-0">
                    <p class="whitespace-nowrap text-sm font-bold text-brand-dark md:text-base">
                        Rp{{ number_format($displayPrice, 0, ',', '.') }}
                    </p>
                    @if($hasDiscount)
                        <p class="text-[10px] text-brand-dark/35 line-through md:text-xs">
                            Rp{{ number_format($displayComparePrice, 0, ',', '.') }}
                        </p>
                    @endif
                </div>

                <div class="card-bag-icon flex h-8 w-8 flex-shrink-0 items-center justify-center bg-white text-brand-primary">
                    <i class="fa-solid fa-bag-shopping text-[10px]"></i>
                </div>
            </div>
        </div>
    </div>
</a>
