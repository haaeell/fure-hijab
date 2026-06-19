@extends('layouts.customer')

@section('title', 'Artikel & Journal — FURE')
@section('seo_title', 'FURE Journal — Tips, Styling Guide & Modest Fashion')
@section('seo_description', 'Temukan inspirasi, panduan styling, tips perawatan hijab, dan informasi terkini seputar modest fashion dari tim FURE.')
@section('seo_keywords', 'artikel hijab, tips hijab, modest fashion, styling guide, fabric notes, FURE journal')
@section('canonical', route('articles.index'))

@section('content')
<div class="bg-[#f8f3ee]">

    {{-- Hero --}}
    <section class="border-b border-brand-secondary/30 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <p class="reveal text-[11px] font-bold uppercase tracking-[0.3em] text-brand-primary">FURE Journal</p>
            <h1 class="reveal mt-3 text-4xl font-semibold leading-tight sm:text-5xl" data-delay="80">
                Cerita, Panduan &<br class="hidden sm:block"> Inspirasi Modest
            </h1>
            <p class="reveal mt-4 max-w-xl text-sm leading-7 text-brand-dark/60" data-delay="160">
                Tips styling, ulasan bahan, panduan acara, dan semua yang perlu kamu tahu seputar dunia hijab dan modest fashion.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        {{-- Filter Kategori --}}
        <div class="reveal mb-10 flex flex-wrap gap-2">
            <a href="{{ route('articles.index') }}"
                class="border px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.18em] transition
                    {{ !$category ? 'border-brand-primary bg-brand-primary text-white' : 'border-brand-secondary/60 text-brand-dark hover:border-brand-primary hover:text-brand-primary' }}">
                Semua
            </a>
            @foreach(\App\Models\Article::$categories as $key => $label)
                <a href="{{ route('articles.index', ['kategori' => $key]) }}"
                    class="border px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.18em] transition
                        {{ $category === $key ? 'border-brand-primary bg-brand-primary text-white' : 'border-brand-secondary/60 text-brand-dark hover:border-brand-primary hover:text-brand-primary' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Featured Article --}}
        @if($featured && !$category)
            <div class="reveal mb-12">
                <a href="{{ route('articles.show', $featured->slug) }}"
                    class="article-card group grid overflow-hidden bg-white lg:grid-cols-[1.2fr_1fr]">
                    <div class="relative aspect-[16/9] overflow-hidden bg-[#eee5dc] lg:aspect-auto lg:min-h-[380px]">
                        @if($featured->thumbnail)
                            <img src="{{ asset('storage/' . $featured->thumbnail) }}"
                                alt="{{ $featured->title }}"
                                class="article-img h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <i class="fa-regular fa-newspaper text-5xl text-brand-secondary/40"></i>
                            </div>
                        @endif
                        <div class="absolute left-4 top-4 bg-brand-primary px-3 py-1 text-[9px] font-bold uppercase tracking-[0.2em] text-white">
                            Terpopuler
                        </div>
                    </div>
                    <div class="flex flex-col justify-center p-8 lg:p-12">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">
                            {{ $featured->category_label }}
                        </p>
                        <h2 class="mt-3 text-2xl font-semibold leading-snug text-brand-dark transition-colors group-hover:text-brand-primary sm:text-3xl">
                            {{ $featured->title }}
                        </h2>
                        <p class="mt-4 text-sm leading-7 text-brand-dark/60">{{ $featured->excerpt }}</p>
                        <div class="mt-6 flex items-center gap-4 text-[11px] text-brand-dark/40">
                            <span class="font-semibold">{{ $featured->author }}</span>
                            <span>·</span>
                            <span>{{ $featured->read_time }} menit baca</span>
                            <span>·</span>
                            <span>{{ number_format($featured->view_count) }} pembaca</span>
                        </div>
                        <span class="mt-7 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-brand-primary transition group-hover:gap-3">
                            Baca artikel
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </a>
            </div>
        @endif

        {{-- Article Grid --}}
        @if($articles->count() > 0)
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($articles as $article)
                    @if($article->id === optional($featured)->id && !$category) @continue @endif
                    <a href="{{ route('articles.show', $article->slug) }}"
                        class="reveal article-card group flex flex-col bg-white" data-delay="{{ ($loop->index % 3) * 80 }}">
                        <div class="relative aspect-[16/10] overflow-hidden bg-[#eee5dc]">
                            @if($article->thumbnail)
                                <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                    loading="lazy" alt="{{ $article->title }}"
                                    class="article-img h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <i class="fa-regular fa-newspaper text-4xl text-brand-secondary/40"></i>
                                </div>
                            @endif
                            <div class="absolute left-3 top-3 bg-white/90 px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.16em] text-brand-primary backdrop-blur-sm">
                                {{ $article->category_label }}
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <h2 class="line-clamp-2 text-base font-semibold leading-snug text-brand-dark transition-colors group-hover:text-brand-primary">
                                {{ $article->title }}
                            </h2>
                            <p class="mt-2 line-clamp-2 text-xs leading-6 text-brand-dark/55">{{ $article->excerpt }}</p>
                            <div class="mt-auto flex items-center justify-between pt-4 text-[10px] text-brand-dark/40">
                                <span class="font-semibold">{{ $article->author }}</span>
                                <span>{{ $article->read_time }} min read</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($articles->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $articles->links() }}
                </div>
            @endif
        @else
            <div class="py-20 text-center">
                <i class="fa-regular fa-newspaper mb-4 text-5xl text-brand-secondary/40"></i>
                <p class="text-sm font-semibold text-brand-dark/50">Belum ada artikel di kategori ini.</p>
                <a href="{{ route('articles.index') }}" class="mt-4 inline-flex text-xs font-bold text-brand-primary hover:underline">
                    Lihat semua artikel
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .article-card {
        transition: transform 0.38s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                    box-shadow 0.38s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .article-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(95, 74, 58, 0.11);
    }
    .article-img {
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .article-card:hover .article-img { transform: scale(1.04); }
</style>
@endpush

@push('scripts')
<script>
(function () {
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var delay = parseInt(el.dataset.delay || '0', 10);
            setTimeout(function () { el.classList.add('revealed'); }, delay);
            observer.unobserve(el);
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(function (el) { observer.observe(el); });
})();
</script>
@endpush
