@extends('layouts.app')

@section('title', 'Ulasan Produk')

@section('content')
    <div class="mx-auto">
        <div class="mb-8">
            <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Ulasan Pelanggan</h1>
            <nav class="text-xs md:text-sm text-gray-400 font-medium mt-1">
                <ol class="flex items-center gap-2">
                    <li><a href="/home" class="hover:text-brand-primary transition-colors">Dashboard</a></li>
                    <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                    <li class="text-brand-dark">Ulasan</li>
                </ol>
            </nav>
        </div>

        @php
            $grouped = $reviews->groupBy('product_id');
        @endphp

        @if($grouped->isEmpty())
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-16 text-center">
                <i class="fa-regular fa-comment-dots text-4xl text-gray-200 mb-4"></i>
                <p class="text-gray-400 font-semibold">Belum ada ulasan</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($grouped as $productId => $productReviews)
                    @php
                        $product    = $productReviews->first()->product;
                        $avgRating  = round($productReviews->avg('rating'), 1);
                        $totalCount = $productReviews->count();
                        $pendingCount = $productReviews->where('is_verified', false)->count();
                    @endphp

                    <div class="bg-white rounded-[28px] shadow-sm border border-gray-50 overflow-hidden">
                        {{-- Header produk (klik untuk expand) --}}
                        <button type="button"
                            onclick="toggleProduct({{ $productId }})"
                            class="w-full flex items-center gap-4 px-6 py-5 hover:bg-gray-50/60 transition-all text-left">

                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/80?text=?' }}"
                                class="w-12 h-12 rounded-2xl object-cover border border-gray-100 flex-shrink-0">

                            <div class="flex-1 min-w-0">
                                <p class="font-extrabold text-brand-dark text-sm truncate">{{ $product->name }}</p>
                                <div class="flex items-center gap-3 mt-0.5">
                                    <div class="flex text-amber-400 text-[10px] gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-{{ $i <= round($avgRating) ? 'solid' : 'regular' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-bold text-gray-500">{{ $avgRating }}</span>
                                    <span class="text-[10px] text-gray-400">({{ $totalCount }} ulasan)</span>
                                    @if($pendingCount > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-50 text-amber-600">
                                            {{ $pendingCount }} pending
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Rating bar mini --}}
                            <div class="hidden md:flex items-center gap-2 flex-shrink-0">
                                @foreach([5,4,3,2,1] as $star)
                                    @php $cnt = $productReviews->where('rating', $star)->count(); @endphp
                                    <div class="flex items-center gap-1">
                                        <span class="text-[9px] text-gray-400 w-2">{{ $star }}</span>
                                        <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-amber-400 rounded-full"
                                                style="width:{{ $totalCount ? round($cnt/$totalCount*100) : 0 }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <i id="chevron-{{ $productId }}" class="fa-solid fa-chevron-down text-gray-300 text-xs flex-shrink-0 transition-transform duration-200 ml-2"></i>
                        </button>

                        {{-- Daftar ulasan (collapsed by default) --}}
                        <div id="reviews-{{ $productId }}" class="hidden border-t border-gray-50 divide-y divide-gray-50">
                            @foreach($productReviews->sortByDesc('created_at') as $review)
                                <div class="px-6 py-4 flex items-start gap-4 hover:bg-gray-50/40 transition-colors">
                                    {{-- Avatar --}}
                                    <div class="w-8 h-8 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center text-xs font-black flex-shrink-0">
                                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-brand-dark text-xs">{{ $review->user->name }}</span>
                                            <div class="flex text-amber-400 text-[9px] gap-0.5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                                @endfor
                                            </div>
                                            <span class="text-[10px] text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($review->comment)
                                            <p class="text-xs text-gray-500 mt-1 italic leading-relaxed">"{{ $review->comment }}"</p>
                                        @endif
                                        @if($review->images)
                                            <div class="flex gap-1.5 mt-2 flex-wrap">
                                                @foreach(array_slice($review->images, 0, 4) as $img)
                                                    <img src="{{ asset('storage/' . $img) }}"
                                                        class="w-10 h-10 rounded-lg object-cover border border-gray-100 cursor-pointer hover:opacity-80 transition"
                                                        onclick="viewImage('{{ asset('storage/' . $img) }}')">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Status + aksi --}}
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $review->is_verified ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600' }}">
                                            {{ $review->is_verified ? 'Verified' : 'Pending' }}
                                        </span>
                                        <button onclick='viewReview(@json($review->load(["user","product"])))'
                                            class="w-7 h-7 flex items-center justify-center bg-brand-primary/10 text-brand-primary rounded-xl hover:bg-brand-primary hover:text-white transition-all">
                                            <i class="fa-solid fa-eye text-[9px]"></i>
                                        </button>
                                        <form action="/reviews/{{ $review->id }}/toggle-verify" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="w-7 h-7 flex items-center justify-center {{ $review->is_verified ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-600' }} rounded-xl hover:opacity-70 transition-all">
                                                <i class="fa-solid {{ $review->is_verified ? 'fa-xmark' : 'fa-check' }} text-[9px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    <div id="reviewModal" class="fixed inset-0 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-[100] p-4">
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20">
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-brand-primary flex items-center justify-center text-white">
                        <i class="fa-solid fa-message text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-brand-dark leading-tight">Detail Ulasan</h2>
                        <p id="modalUser" class="text-[10px] text-gray-400 font-bold uppercase tracking-widest"></p>
                    </div>
                </div>
                <button onclick="closeReviewModal()" class="w-9 h-9 flex items-center justify-center rounded-full text-gray-400 hover:text-red-500 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-8">
                <div class="mb-6 flex items-center gap-4 p-4 rounded-2xl bg-gray-50/50 border border-gray-100">
                    <img id="prodImage" src="" class="w-14 h-14 rounded-xl object-cover shadow-sm">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Produk</p>
                        <h4 id="prodName" class="font-bold text-brand-dark text-sm"></h4>
                        <div id="starRating" class="flex text-amber-400 text-[10px] mt-1"></div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 block">Komentar</label>
                        <p id="revComment" class="text-sm text-gray-600 leading-relaxed italic"></p>
                    </div>
                    <div id="revImagesContainer" class="hidden">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 block">Foto Pembeli</label>
                        <div id="revImages" class="flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50/30 flex justify-end">
                <button onclick="closeReviewModal()" class="px-10 py-3 rounded-2xl bg-white border border-gray-200 text-xs font-black uppercase text-gray-400 hover:bg-gray-50 transition-all">Tutup</button>
            </div>
        </div>
    </div>

    {{-- Image lightbox --}}
    <div id="imgLightbox" class="fixed inset-0 hidden bg-black/80 flex items-center justify-center z-[200] p-4 cursor-pointer" onclick="document.getElementById('imgLightbox').classList.add('hidden').classList.remove('flex')">
        <img id="lightboxImg" src="" class="max-h-[90vh] max-w-full rounded-2xl shadow-2xl">
    </div>

    @push('scripts')
        <script>
            function toggleProduct(id) {
                const el = document.getElementById('reviews-' + id);
                const ch = document.getElementById('chevron-' + id);
                const hidden = el.classList.contains('hidden');
                el.classList.toggle('hidden', !hidden);
                el.classList.toggle('block', hidden);
                ch.style.transform = hidden ? 'rotate(180deg)' : '';
            }

            function viewReview(data) {
                $('#modalUser').text(data.user.name);
                $('#prodName').text(data.product.name);
                $('#prodImage').attr('src', '/storage/' + data.product.image);
                $('#revComment').text('"' + data.comment + '"');
                let stars = '';
                for (let i = 1; i <= 5; i++) stars += `<i class="fa-${i <= data.rating ? 'solid' : 'regular'} fa-star mr-0.5"></i>`;
                $('#starRating').html(stars);
                $('#revImages').empty();
                if (data.images && data.images.length > 0) {
                    $('#revImagesContainer').removeClass('hidden');
                    data.images.forEach(img => {
                        $('#revImages').append(`<img src="/storage/${img}" class="w-20 h-20 rounded-xl object-cover border border-gray-100 shadow-sm cursor-pointer" onclick="viewImage('/storage/${img}')">`);
                    });
                } else {
                    $('#revImagesContainer').addClass('hidden');
                }
                $('#reviewModal').removeClass('hidden').addClass('flex');
            }

            function closeReviewModal() {
                $('#reviewModal').addClass('hidden').removeClass('flex');
            }

            function viewImage(src) {
                document.getElementById('lightboxImg').src = src;
                const lb = document.getElementById('imgLightbox');
                lb.classList.remove('hidden');
                lb.classList.add('flex');
            }

            document.getElementById('imgLightbox').addEventListener('click', function() {
                this.classList.add('hidden');
                this.classList.remove('flex');
            });
        </script>
    @endpush
@endsection
