@extends('layouts.customer')

@section('title', 'Keranjang Belanja - ' . $storeName)

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

            {{-- Form checkout dengan item terpilih --}}
            <form id="checkoutForm" action="{{ route('cart.checkout') }}" method="POST">
            @csrf

            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-2/3 space-y-3">

                    @if($carts->isNotEmpty())
                    {{-- Header pilih semua --}}
                    <div class="flex items-center gap-3 bg-white px-5 py-3.5 rounded-2xl border border-gray-100 shadow-sm">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" id="selectAll"
                                class="h-5 w-5 rounded border-gray-300 text-brand-primary accent-brand-primary cursor-pointer">
                            <span class="text-sm font-bold text-brand-dark">Pilih Semua</span>
                        </label>
                        <span class="text-xs text-gray-400 ml-auto" id="selectedCount">0 produk dipilih</span>
                    </div>
                    @endif

                    @forelse($carts as $item)
                        <div
                            class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-gray-100 flex gap-3 md:gap-5 items-center group transition-all hover:shadow-md hover:border-brand-secondary/40"
                            data-item-id="{{ $item->id }}"
                            data-unit-price="{{ $item->price }}">

                            {{-- Checkbox --}}
                            <label class="flex-shrink-0 cursor-pointer p-1">
                                <input type="checkbox"
                                    name="selected_items[]"
                                    value="{{ $item->id }}"
                                    class="item-check h-5 w-5 rounded border-gray-300 text-brand-primary accent-brand-primary cursor-pointer"
                                    checked>
                            </label>

                            <div class="w-20 h-28 md:w-24 md:h-32 rounded-xl overflow-hidden bg-gray-50 flex-shrink-0 border border-gray-100">
                                @php $primaryImage = $item->product?->images?->where('is_primary', true)->first(); @endphp
                                <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533' }}"
                                    loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>

                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        @if(!$item->product)
                                        <p class="text-[10px] md:text-xs font-bold text-red-400 uppercase tracking-widest mb-1">Produk tidak tersedia</p>
                                        @else
                                        <p
                                            class="text-[10px] md:text-xs font-bold text-brand-primary uppercase tracking-widest mb-1">
                                            {{ $item->product->category?->name }}
                                        </p>
                                        @endif
                                        <h3 class="font-bold text-brand-dark text-sm md:text-lg leading-tight mb-2">
                                            {{ $item->product?->name ?? '(Produk dihapus)' }}
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
                                    <button type="button"
                                            class="btn-delete text-gray-300 hover:text-red-500 transition-colors p-2"
                                            data-id="{{ $item->id }}"
                                            aria-label="Hapus item">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mt-2">
                                    <p class="text-base md:text-xl font-extrabold text-brand-dark" id="price-{{ $item->id }}">
                                        Rp{{ number_format($item->price * $item->qty, 0, ',', '.') }}
                                    </p>

                                    <div class="flex items-center gap-1 bg-soft-mint rounded-xl p-1 w-fit border border-brand-primary/10"
                                         data-stock="{{ $item->variant ? $item->variant->stock : ($item->product?->stock ?? 0) }}">
                                        <button type="button"
                                            class="update-qty w-8 h-8 rounded-lg flex items-center justify-center text-brand-dark hover:bg-white transition-all disabled:opacity-40"
                                            data-id="{{ $item->id }}" data-action="minus">
                                            <i class="fa-solid fa-minus text-[10px]"></i>
                                        </button>
                                        <input type="text" value="{{ $item->qty }}"
                                            class="w-10 text-center bg-transparent font-bold text-sm text-brand-dark outline-none"
                                            readonly>
                                        <button type="button"
                                            class="update-qty w-8 h-8 rounded-lg flex items-center justify-center text-brand-dark hover:bg-white transition-all disabled:opacity-40"
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
                            <p class="text-gray-400 mb-8 max-w-xs mx-auto text-sm">Sepertinya kamu belum memilih koleksi hijab favoritmu.</p>
                            <a href="/" class="inline-flex px-8 py-3 bg-brand-primary text-white font-bold rounded-xl shadow-lg shadow-brand-primary/20 hover:-translate-y-1 transition-all">
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
                                <span class="font-bold text-white" id="cartItemCount">{{ $carts->sum('qty') }} Item</span>
                            </div>
                            <div class="flex justify-between text-sm text-white/70">
                                <span>Subtotal</span>
                                <span
                                    class="font-bold text-white" id="cartSubtotal">Rp{{ number_format($total_price ?? 0, 0, ',', '.') }}</span>
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
                                    class="text-2xl font-extrabold text-brand-primary" id="cartTotal">Rp{{ number_format($total_price ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button type="submit" id="btnCheckout"
                            class="group relative w-full py-4 bg-brand-primary text-brand-dark font-bold rounded-2xl flex items-center justify-center gap-3 overflow-hidden shadow-xl hover:shadow-brand-primary/40 transition-all hover:-translate-y-1 active:scale-95 disabled:opacity-50 disabled:pointer-events-none">
                            <span class="relative z-10">Checkout <span id="checkoutCount"></span></span>
                            <i class="fa-solid fa-chevron-right text-xs relative z-10 group-hover:translate-x-1 transition-transform"></i>
                        </button>

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
            </div>{{-- end form --}}
        </div>
    </section>
@endsection

@push('scripts')
<script>
const csrfToken = $('meta[name="csrf-token"]').attr('content');

function formatRupiah(n) {
    return 'Rp' + new Intl.NumberFormat('id-ID').format(n);
}

function recalcTotal() {
    let total = 0;
    let selectedCount = 0;
    const totalItems = $('[data-item-id]').length;

    $('[data-item-id]').each(function () {
        const checked = $(this).find('.item-check').is(':checked');
        const qty     = parseInt($(this).find('input[type="text"]').val()) || 0;
        const price   = parseInt($(this).data('unit-price')) || 0;
        if (checked) {
            total += price * qty;
            selectedCount++;
        }
    });

    $('#cartSubtotal, #cartTotal').text(formatRupiah(total));
    $('#cartItemCount').text(totalItems + ' Item');
    $('#selectedCount').text(selectedCount + ' produk dipilih');
    $('#checkoutCount').text(selectedCount > 0 ? '(' + selectedCount + ')' : '');
    $('#btnCheckout').prop('disabled', selectedCount === 0);
    $('.js-cart-count').text(totalItems > 9 ? '9+' : totalItems);

    // Sinkron checkbox "Pilih Semua"
    const allChecked = totalItems > 0 && selectedCount === totalItems;
    $('#selectAll').prop('indeterminate', selectedCount > 0 && !allChecked);
    $('#selectAll').prop('checked', allChecked);
}

function setQtyLoading($wrap, loading) {
    $wrap.find('.update-qty').prop('disabled', loading);
    $wrap.css('opacity', loading ? 0.5 : 1);
    $wrap.css('transition', 'opacity 0.2s');
}

// Update quantity
$(document).on('click', '.update-qty', function () {
    const btn    = $(this);
    const id     = btn.data('id');
    const action = btn.data('action');
    const $wrap  = btn.closest('[data-stock]');
    const stock  = parseInt($wrap.data('stock')) || 999;
    const $input = $wrap.find('input[type="text"]');
    const current = parseInt($input.val()) || 1;
    const newQty  = action === 'plus' ? current + 1 : current - 1;

    if (newQty < 1) {
        removeItem(id, btn.closest('[data-item-id]'));
        return;
    }
    if (newQty > stock) {
        // Visual shake + toast, tanpa modal berat
        $wrap.addClass('animate-pulse');
        setTimeout(() => $wrap.removeClass('animate-pulse'), 600);
        Swal.fire({
            icon: 'warning',
            title: 'Stok tersedia: ' + stock,
            toast: true, position: 'top-end',
            showConfirmButton: false, timer: 2000,
        });
        return;
    }

    setQtyLoading($wrap, true);

    $.ajax({
        url: '/cart/update/' + id,
        method: 'PATCH',
        data: { _token: csrfToken, quantity: newQty },
        success: function () {
            $input.val(newQty);
            const $row = btn.closest('[data-item-id]');
            const unitPrice = parseInt($row.data('unit-price')) || 0;
            const newSubtotal = formatRupiah(unitPrice * newQty);

            // Animasi nilai berubah
            const $price = $('#price-' + id);
            $price.css({ opacity: 0, transition: 'opacity 0.15s' });
            setTimeout(function () {
                $price.text(newSubtotal).css('opacity', 1);
            }, 150);

            recalcTotal();
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'warning',
                title: xhr.responseJSON?.message || 'Gagal memperbarui.',
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 2500,
            });
        },
        complete: function () { setQtyLoading($wrap, false); },
    });
});

// Hapus item
function removeItem(id, $row) {
    Swal.fire({
        title: 'Hapus produk?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $row.css({ opacity: 0.4, pointerEvents: 'none', transition: 'opacity 0.2s' });

        $.ajax({
            url: '/cart/delete/' + id,
            method: 'DELETE',
            data: { _token: csrfToken },
            success: function () {
                $row.slideUp(280, function () {
                    $(this).remove();
                    recalcTotal();
                    if ($('[data-item-id]').length === 0) {
                        location.reload(); // reload agar header pilih-semua ikut hilang
                    }
                });
            },
            error: function () {
                $row.css({ opacity: 1, pointerEvents: '' });
                Swal.fire({ icon: 'error', title: 'Gagal menghapus.' });
            },
        });
    });
}

$(document).on('click', '.btn-delete', function () {
    removeItem($(this).data('id'), $(this).closest('[data-item-id]'));
});

// Checkbox item individu
$(document).on('change', '.item-check', function () {
    recalcTotal();
});

// Pilih Semua / Batal Semua
$('#selectAll').on('change', function () {
    $('.item-check').prop('checked', $(this).is(':checked'));
    recalcTotal();
});

// Init saat load — hitung dari semua item (semua tercentang by default)
recalcTotal();
</script>
@endpush
