<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — SalonBook</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link.active { background: rgba(255,255,255,0.15); }
    </style>
</head><body class="font-sans antialiased bg-stone-100 min-h-screen" x-data="adminNotification()" x-init="initSSE()">
    
    {{-- Live Glowing Toast Notification --}}
    <div x-show="showNotification" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-12 opacity-0 scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100 scale-100"
         x-transition:leave-end="translate-y-12 opacity-0 scale-95"
         class="fixed bottom-6 right-6 z-[100] w-96 rounded-3xl p-[1px] shadow-2xl shadow-violet-500/20" 
         style="background: linear-gradient(135deg, #7c3aed, #db2777); display: none;">
        <div class="rounded-3xl p-5 bg-slate-950 text-white flex gap-4 items-start relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 to-transparent pointer-events-none"></div>
            
            {{-- Glowing icon indicator --}}
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500/20 to-fuchsia-500/20 flex items-center justify-center shrink-0 border border-violet-500/30 shadow-[0_0_15px_rgba(139,92,246,0.25)] animate-bounce mt-1">
                <span class="text-xl">✨</span>
            </div>

            <div class="flex-1 min-w-0 select-none">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-violet-400">Reservasi Baru Masuk</span>
                    <button @click="showNotification = false" class="text-slate-500 hover:text-white transition-colors">✕</button>
                </div>
                
                <h4 class="font-bold text-base text-white mt-1 leading-snug truncate" x-text="notification.customer_name"></h4>
                <p class="text-xs text-slate-300 mt-1 font-semibold" x-text="notification.service_name"></p>
                
                <div class="flex items-center justify-between gap-2 mt-4 pt-3 border-t border-white/5 text-[10px] text-slate-400 font-semibold">
                    <span class="truncate max-w-[140px]" x-text="'Stylist: ' + notification.stylist_name"></span>
                    <a :href="'/admin/bookings/' + notification.id" class="inline-flex items-center gap-1 bg-violet-600 hover:bg-violet-700 text-white px-3 py-1.5 rounded-lg text-[9px] font-extrabold uppercase tracking-widest transition-all shadow-sm shadow-violet-500/10 hover:scale-[1.02]">Kelola →</a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex min-h-screen">
        {{-- Sidebar — dark, compact, with icons --}}
        <aside class="w-64 bg-gradient-to-b from-slate-900 via-slate-900 to-violet-950 text-white shrink-0 hidden md:flex flex-col">
            <div class="p-5 border-b border-white/5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center shadow-lg shadow-violet-500/30">
                        <span class="text-white font-bold text-sm">G</span>
                    </div>
                    <div>
                        <span class="font-bold text-sm tracking-tight block">SalonBook</span>
                        <span class="text-[10px] text-slate-400 uppercase tracking-widest">Admin</span>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-3 space-y-0.5 text-sm overflow-y-auto">
                <p class="text-slate-500 text-[9px] font-bold uppercase tracking-[0.2em] px-3 mb-2 mt-2">Menu</p>

                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'active text-white font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.bookings.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.bookings.index') || request()->routeIs('admin.bookings.show') ? 'active text-white font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Bookings Table
                </a>

                <a href="{{ route('admin.bookings.kanban') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.bookings.kanban') ? 'active text-white font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    Pipeline Kanban
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.services.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.services.*') ? 'active text-white font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Layanan
                    </a>
                    <a href="{{ route('admin.stylists.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.stylists.*') ? 'active text-white font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Stylist
                    </a>
                    <a href="{{ route('admin.gallery.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.gallery.*') ? 'active text-white font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Gallery & Testimonials
                    </a>
                    <a href="{{ route('admin.reports.export') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-slate-300 hover:bg-white/5 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export CSV
                    </a>
                @endif
            </nav>

            <div class="p-3 border-t border-white/5 space-y-1">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-white transition-all text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Website
                </a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-all text-sm font-semibold text-left cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar (Logout)
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 min-w-0">
            <header class="bg-white border-b border-stone-200 px-6 py-3 flex justify-between items-center sticky top-0 z-30">
                <h1 class="font-bold text-lg text-stone-800">@yield('heading', 'Admin')</h1>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-stone-800">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-stone-400 uppercase tracking-wider">{{ auth()->user()->role }}</p>
                    </div>
                    <div class="w-9 h-9 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            @if (session('success'))
                <div class="mx-6 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <main class="p-6">@yield('content')</main>
        </div>
    </div>

    {{-- SSE Notification Script --}}
    <script>
        function adminNotification() {
            return {
                showNotification: false,
                notification: {
                    reference: '',
                    customer_name: '',
                    service_name: '',
                    stylist_name: '',
                    price_formatted: '',
                    time_formatted: '',
                },
                initSSE() {
                    this.playChime = () => {
                        try {
                            const ctx = new (window.AudioContext || window.webkitAudioContext)();
                            const playNode = (freq, startTime, duration) => {
                                const osc = ctx.createOscillator();
                                const gain = ctx.createGain();
                                osc.type = 'sine';
                                osc.frequency.setValueAtTime(freq, startTime);
                                gain.gain.setValueAtTime(0.15, startTime);
                                gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                                osc.connect(gain);
                                gain.connect(ctx.destination);
                                osc.start(startTime);
                                osc.stop(startTime + duration);
                            };
                            playNode(783.99, ctx.currentTime, 0.4);
                            playNode(1046.50, ctx.currentTime + 0.12, 0.6);
                        } catch(e) {
                            console.error('AudioContext error:', e);
                        }
                    };

                    let lastCheckedId = null;

                    const runPoll = () => {
                        let url = '/api/v1/live-bookings-poll';
                        if (lastCheckedId !== null) {
                            url += `?last_checked_id=${lastCheckedId}`;
                        }
                        
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                if (lastCheckedId === null) {
                                    lastCheckedId = data.last_checked_id;
                                    return;
                                }
                                
                                lastCheckedId = data.last_checked_id;

                                if (data.bookings && data.bookings.length > 0) {
                                    data.bookings.forEach(booking => {
                                        this.notification = booking;
                                        this.showNotification = true;
                                        this.playChime();
                                        
                                        window.dispatchEvent(new CustomEvent('live-booking-added', { detail: booking }));
                                        
                                        setTimeout(() => {
                                            this.showNotification = false;
                                        }, 6000);
                                    });
                                }
                            })
                            .catch(err => console.error('Polling error:', err));
                    };

                    runPoll();
                    setInterval(runPoll, 4000);
                }
            }
        }
    </script>
</body>
</html>
