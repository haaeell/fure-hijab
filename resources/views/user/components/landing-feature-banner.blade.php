@if($banner)
    <section class="w-full py-4 sm:py-6">
        <a href="{{ route('collections.index') }}"
            class="group relative block min-h-[280px] overflow-hidden bg-brand-dark sm:min-h-[360px] lg:min-h-[430px]">
            <img src="{{ $banner->image }}" alt="{{ $banner->title }}"
                class="absolute inset-0 h-full w-full object-cover opacity-85 transition duration-700 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-r {{ ($banner->align ?? 'left') === 'right' ? 'from-transparent via-brand-dark/25 to-brand-dark/88' : 'from-brand-dark/88 via-brand-dark/25 to-transparent' }}"></div>
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-brand-dark/45 to-transparent"></div>

            <div class="relative mx-auto flex min-h-[280px] max-w-7xl items-center px-4 py-10 sm:min-h-[360px] sm:px-6 lg:min-h-[430px] lg:px-8">
                <div class="max-w-xl text-white {{ ($banner->align ?? 'left') === 'right' ? 'ml-auto text-left lg:text-right' : '' }}">
                    <p class="reveal inline-flex border border-white/50 px-4 py-2 text-[10px] font-bold uppercase tracking-[0.24em] text-white/90">
                        {{ $banner->eyebrow }}
                    </p>
                    <h2 class="reveal mt-5 text-4xl font-semibold leading-none tracking-normal sm:text-5xl lg:text-6xl" data-delay="100">
                        {{ $banner->title }}
                    </h2>
                    <p class="reveal mt-5 max-w-md text-sm leading-7 text-white/80 {{ ($banner->align ?? 'left') === 'right' ? 'lg:ml-auto' : '' }}" data-delay="200">
                        {{ $banner->subtitle }}
                    </p>
                    <span class="reveal mt-7 inline-flex items-center gap-3 bg-white px-6 py-3 text-xs font-bold uppercase tracking-[0.16em] text-brand-dark transition group-hover:bg-brand-secondary" data-delay="300">
                        {{ $banner->button_text ?: 'Shop Now' }}
                        <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    </span>
                </div>
            </div>
        </a>
    </section>
@endif
