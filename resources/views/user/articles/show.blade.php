@extends('layouts.customer')

@section('title', $article->meta_title . ' — ' . $globalStoreName)
@section('seo_title', $article->meta_title)
@section('seo_description', $article->meta_description)
@section('seo_keywords', $article->meta_keywords)
@section('seo_image', $article->thumbnail ? asset('storage/' . $article->thumbnail) : '')
@section('canonical', route('articles.show', $article->slug))
@section('og_type', 'article')

@push('seo')
<script type="application/ld+json">{!! json_encode([
    '@context'         => 'https://schema.org',
    '@type'            => 'Article',
    'headline'         => $article->title,
    'description'      => $article->meta_description,
    'image'            => $article->thumbnail ? asset('storage/' . $article->thumbnail) : null,
    'author'           => ['@type' => 'Person', 'name' => $article->author],
    'publisher'        => ['@type' => 'Organization', 'name' => $storeName],
    'datePublished'    => optional($article->published_at)->toIso8601String(),
    'dateModified'     => $article->updated_at->toIso8601String(),
    'keywords'         => $article->meta_keywords,
    'articleSection'   => $article->category_label,
    'url'              => route('articles.show', $article->slug),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<div class="bg-[#f8f3ee]">

    {{-- Breadcrumb --}}
    <div class="border-b border-brand-secondary/20 bg-white">
        <div class="mx-auto max-w-4xl px-4 py-3 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-dark/45">
                <a href="/" class="transition hover:text-brand-primary">Home</a>
                <span>/</span>
                <a href="{{ route('articles.index') }}" class="transition hover:text-brand-primary">Artikel</a>
                <span>/</span>
                <a href="{{ route('articles.index', ['kategori' => $article->category]) }}" class="transition hover:text-brand-primary">
                    {{ $article->category_label }}
                </a>
                <span>/</span>
                <span class="text-brand-dark">{{ Str::limit($article->title, 40) }}</span>
            </nav>
        </div>
    </div>

    <article class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Header --}}
        <header class="mb-8">
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <a href="{{ route('articles.index', ['kategori' => $article->category]) }}"
                    class="border border-brand-primary bg-brand-primary px-3 py-1 text-[9px] font-bold uppercase tracking-[0.2em] text-white transition hover:bg-brand-dark">
                    {{ $article->category_label }}
                </a>
                @if($article->tags)
                    @foreach($article->tags as $tag)
                        <span class="border border-brand-secondary/50 px-3 py-1 text-[9px] font-semibold uppercase tracking-[0.16em] text-brand-dark/50">
                            {{ $tag }}
                        </span>
                    @endforeach
                @endif
            </div>

            <h1 class="text-3xl font-semibold leading-snug text-brand-dark sm:text-4xl lg:text-[2.6rem] lg:leading-tight">
                {{ $article->title }}
            </h1>

            @if($article->excerpt)
                <p class="mt-4 text-base leading-7 text-brand-dark/60">{{ $article->excerpt }}</p>
            @endif

            <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-brand-secondary/30 pt-5 text-[11px] text-brand-dark/45">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-brand-primary/60"></i>
                    <strong class="font-bold text-brand-dark/70">{{ $article->author }}</strong>
                </span>
                @if($article->published_at)
                    <span class="flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-brand-primary/60"></i>
                        {{ $article->published_at->translatedFormat('d F Y') }}
                    </span>
                @endif
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-clock text-brand-primary/60"></i>
                    {{ $article->read_time }} menit baca
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-eye text-brand-primary/60"></i>
                    {{ number_format($article->view_count) }} pembaca
                </span>
            </div>
        </header>

        {{-- Thumbnail --}}
        @if($article->thumbnail)
            <div class="mb-8 aspect-[16/9] overflow-hidden bg-[#eee5dc]">
                <img src="{{ asset('storage/' . $article->thumbnail) }}"
                    alt="{{ $article->title }}"
                    class="h-full w-full object-cover"
                    fetchpriority="high">
            </div>
        @endif

        {{-- Content --}}
        <div class="article-body prose prose-lg max-w-none text-brand-dark/80">
            {!! $article->content !!}
        </div>

        {{-- Share --}}
        <div class="mt-10 border-t border-brand-secondary/30 pt-8">
            <p class="mb-4 text-[10px] font-bold uppercase tracking-[0.22em] text-brand-dark/45">Bagikan artikel ini</p>
            <div class="flex flex-wrap gap-2">
                <a href="https://wa.me/?text={{ urlencode($article->title . ' — ' . route('articles.show', $article->slug)) }}"
                    target="_blank" rel="noopener"
                    class="flex items-center gap-2 bg-[#25D366] px-4 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-white transition hover:opacity-90">
                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(route('articles.show', $article->slug)) }}"
                    target="_blank" rel="noopener"
                    class="flex items-center gap-2 bg-black px-4 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-white transition hover:opacity-90">
                    <i class="fa-brands fa-x-twitter"></i> X
                </a>
                <button type="button" onclick="navigator.clipboard.writeText('{{ route('articles.show', $article->slug) }}').then(function(){Swal.fire({icon:'success',title:'Link disalin!',toast:true,position:'top-end',showConfirmButton:false,timer:2000})})"
                    class="flex items-center gap-2 border border-brand-secondary/60 bg-white px-4 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-brand-dark transition hover:border-brand-primary hover:text-brand-primary">
                    <i class="fa-regular fa-copy"></i> Salin Link
                </button>
            </div>
        </div>
    </article>

    {{-- Related Articles --}}
    @if($related->count() > 0)
        <section class="border-t border-brand-secondary/30 bg-white py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">Selanjutnya</p>
                <h2 class="mt-2 text-2xl font-semibold">Artikel Terkait</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($related as $rel)
                        <a href="{{ route('articles.show', $rel->slug) }}"
                            class="article-card group flex flex-col bg-[#f8f3ee]">
                            <div class="relative aspect-[16/10] overflow-hidden bg-[#eee5dc]">
                                @if($rel->thumbnail)
                                    <img src="{{ asset('storage/' . $rel->thumbnail) }}"
                                        loading="lazy" alt="{{ $rel->title }}"
                                        class="article-img h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center">
                                        <i class="fa-regular fa-newspaper text-4xl text-brand-secondary/40"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                <p class="text-[9px] font-bold uppercase tracking-[0.18em] text-brand-primary">{{ $rel->category_label }}</p>
                                <h3 class="mt-2 line-clamp-2 text-sm font-semibold leading-snug text-brand-dark transition-colors group-hover:text-brand-primary">
                                    {{ $rel->title }}
                                </h3>
                                <span class="mt-4 text-[10px] font-bold uppercase tracking-[0.16em] text-brand-primary">Baca →</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="bg-brand-dark py-16 text-center text-white">
        <div class="mx-auto max-w-xl px-4">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-secondary">Koleksi {{ $storeName }}</p>
            <h2 class="mt-3 text-3xl font-semibold">Temukan Hijab Favoritmu</h2>
            <p class="mt-4 text-sm leading-7 text-white/65">Koleksi hijab premium dengan bahan terpilih, warna lembut, dan potongan modest yang elegan.</p>
            <a href="{{ route('collections.index') }}"
                class="mt-8 inline-flex bg-white px-8 py-3 text-xs font-bold uppercase tracking-[0.18em] text-brand-dark transition hover:bg-brand-secondary">
                Lihat Koleksi
            </a>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .article-body h2 {
        font-size: 1.4rem; font-weight: 600; margin-top: 2.2rem; margin-bottom: 0.8rem;
        color: #5F4A3A; letter-spacing: -0.01em;
    }
    .article-body h3 {
        font-size: 1.15rem; font-weight: 600; margin-top: 1.8rem; margin-bottom: 0.6rem; color: #5F4A3A;
    }
    .article-body p { margin-bottom: 1.25rem; line-height: 1.85; }
    .article-body ul, .article-body ol { padding-left: 1.5rem; margin-bottom: 1.25rem; }
    .article-body li { margin-bottom: 0.5rem; line-height: 1.75; }
    .article-body strong { color: #5F4A3A; font-weight: 700; }
    .article-body a { color: #A78B6F; text-decoration: underline; }

    .article-card {
        transition: transform 0.38s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                    box-shadow 0.38s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .article-card:hover { transform: translateY(-4px); box-shadow: 0 14px 36px rgba(95,74,58,0.10); }
    .article-img { transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
    .article-card:hover .article-img { transform: scale(1.04); }
</style>
@endpush
