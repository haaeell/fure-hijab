@props(['product', 'isFlashSale' => false])

@php
    $displayPrice = $product->has_variant && $product->variants->count() > 0
        ? $product->variants->first()->price
        : $product->price;

    $displayComparePrice = $product->has_variant && $product->variants->count() > 0
        ? $product->variants->first()->compare_price
        : $product->compare_price;

    $hasDiscount = $displayComparePrice > $displayPrice;
    $diskon = $hasDiscount ? round((($displayComparePrice - $displayPrice) / $displayComparePrice) * 100) : 0;

    $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
@endphp

<a href="{{ route('collections.show', $product->slug) }}" class="product-card group block h-full">
    <div
        class="relative flex h-full flex-col overflow-hidden border border-brand-secondary/40 bg-white transition-all duration-300 hover:border-brand-primary">

        @if($isFlashSale || $hasDiscount)
            <div
                class="absolute left-2.5 top-2.5 z-10 flex items-center gap-1 bg-brand-dark px-2.5 py-1 text-[8px] font-bold uppercase tracking-widest text-white md:text-[10px]">
                <i class="fa-solid fa-tag text-[7px]"></i>
                @if($hasDiscount)
                    Hemat {{ $diskon }}%
                @else
                    SALE
                @endif
            </div>
        @endif

        <div
            class="relative mb-3 aspect-[3/4] overflow-hidden bg-[#eee5dc]">
            <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533?text=FURE' }}"
                class="product-image w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110"
                alt="{{ $product->name }}">

            <div
                class="absolute inset-0 flex items-center justify-center bg-brand-dark/20 opacity-0 transition-opacity group-hover:opacity-100">
                <div
                    class="flex h-9 w-9 scale-75 items-center justify-center bg-white/95 text-sm text-brand-dark transition-transform group-hover:scale-100">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
        </div>

        <div class="flex flex-grow flex-col px-3 pb-3">
            <div class="flex justify-between items-center mb-1 gap-1">
                <p class="text-[8px] md:text-[10px] text-brand-primary font-bold uppercase tracking-wider truncate">
                    {{ $product->category->name }}
                </p>
                <div class="flex items-center gap-0.5 text-yellow-400 text-[9px] flex-shrink-0">
                    <i class="fa-solid fa-star"></i>
                    <span class="text-gray-400 font-medium">4.8</span>
                </div>
            </div>

            <h3
                class="font-semibold text-brand-dark text-[10px] md:text-xs line-clamp-2 mb-2 h-7 md:h-8 leading-snug group-hover:text-brand-primary transition-colors">
                {{ $product->name }}
            </h3>

            <div class="mt-auto flex items-center justify-between gap-1 border-t border-brand-secondary/30 pt-2">
                <div class="space-y-0">
                    <p class="text-xs md:text-sm font-extrabold text-brand-dark whitespace-nowrap">
                        Rp{{ number_format($displayPrice, 0, ',', '.') }}
                    </p>
                    @if($hasDiscount)
                        <p class="text-[8px] md:text-[10px] text-gray-400 line-through">
                            Rp{{ number_format($displayComparePrice, 0, ',', '.') }}
                        </p>
                    @endif
                </div>

                <div
                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center bg-[#eee5dc] text-brand-primary transition-all duration-300 hover:bg-brand-primary hover:text-white active:scale-90">
                    <i class="fa-solid fa-bag-shopping text-[10px]"></i>
                </div>
            </div>
        </div>
    </div>
</a>
