<div class="md:hidden fixed bottom-4 left-4 right-4 z-50">
    <div
        class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-gray-100 px-4 py-3 flex justify-between items-center">

        <a href="/" class="flex flex-col items-center gap-1 group w-1/5">
            <div
                class="w-10 h-10 flex items-center justify-center rounded-xl {{ request()->is('/') ? 'bg-brand-primary text-white shadow-lg shadow-brand-primary/20' : 'bg-gray-50 text-gray-400' }} transition-all">
                <i class="fa-solid fa-house text-lg"></i>
            </div>
            <span
                class="text-[9px] font-bold tracking-tighter {{ request()->is('/') ? 'text-brand-primary' : 'text-gray-400' }}">Home</span>
        </a>

        <a href="{{ route('collections.index') }}" class="flex flex-col items-center gap-1 group w-1/5">
            <div
                class="w-10 h-10 flex items-center justify-center rounded-xl {{ request()->routeIs('collections.*') ? 'bg-brand-primary text-white shadow-lg shadow-brand-primary/20' : 'bg-gray-50 text-gray-400' }} transition-all">
                <i class="fa-solid fa-layer-group text-lg"></i>
            </div>
            <span
                class="text-[9px] font-bold tracking-tighter {{ request()->routeIs('collections.*') ? 'text-brand-primary' : 'text-gray-400' }}">Koleksi</span>
        </a>

        <a href="/cart" class="relative flex flex-col items-center gap-1 w-1/5">
            <div
                class="w-12 h-12 flex items-center justify-center rounded-2xl {{ request()->is('cart') ? 'bg-brand-primary' : 'bg-brand-dark' }} text-white shadow-lg -mt-10 border-4 border-[#FBFBFE] transition">
                <i class="fa-solid fa-cart-shopping text-lg"></i>
            </div>
            <span
                class="text-[9px] font-bold tracking-tighter {{ request()->is('cart') ? 'text-brand-primary' : 'text-brand-dark' }} mt-1">Cart</span>
            <span
                class="absolute -top-11 right-2 bg-brand-primary text-white text-[9px] w-5 h-5 rounded-full flex items-center justify-center border-2 border-white font-bold shadow-sm">
                @auth
                    {{ \App\Models\CartItem::whereHas('cart', function ($q) {
                        $q->where('user_id', auth()->id());
                    })->count() }}
                @else
                    0
                @endauth
            </span>
        </a>

        <a href="{{ route('about.index') }}" class="flex flex-col items-center gap-1 group w-1/5">
            <div
                class="w-10 h-10 flex items-center justify-center rounded-xl {{ request()->routeIs('about.*') ? 'bg-brand-primary text-white shadow-lg shadow-brand-primary/20' : 'bg-gray-50 text-gray-400' }} transition-all">
                <i class="fa-solid fa-circle-info text-lg"></i>
            </div>
            <span
                class="text-[9px] font-bold tracking-tighter {{ request()->routeIs('about.*') ? 'text-brand-primary' : 'text-gray-400' }}">Tentang</span>
        </a>

        @auth
            <a href="/user/profile" class="flex flex-col items-center gap-1 group w-1/5">
                <div
                    class="w-10 h-10 flex items-center justify-center rounded-xl {{ request()->is('user/profile*') ? 'bg-brand-primary text-white shadow-lg shadow-brand-primary/20' : 'bg-gray-50 text-gray-400' }} transition-all">
                    <i class="fa-solid fa-user text-lg"></i>
                </div>
                <span
                    class="text-[9px] font-bold tracking-tighter {{ request()->is('user/profile*') ? 'text-brand-primary' : 'text-gray-400' }}">Profile</span>
            </a>
        @else
            <a href="/login" class="flex flex-col items-center gap-1 group w-1/5">
                <div
                    class="w-10 h-10 flex items-center justify-center rounded-xl {{ request()->is('login') || request()->is('register') || request()->is('password/*') ? 'bg-brand-primary text-white' : 'bg-gray-50 text-gray-400' }} transition-all border border-dashed border-gray-200">
                    <i class="fa-solid fa-right-to-bracket text-lg"></i>
                </div>
                <span
                    class="text-[9px] font-bold tracking-tighter {{ request()->is('login') || request()->is('register') || request()->is('password/*') ? 'text-brand-primary' : 'text-gray-400' }}">Akun</span>
            </a>
        @endauth
    </div>
</div>

<div class="h-28 md:hidden"></div>
