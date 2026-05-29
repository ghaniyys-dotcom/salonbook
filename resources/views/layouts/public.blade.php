<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Glow Studio')</title>
    <meta name="description" content="Premium salon booking — tanpa antri, tanpa bentrok.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('schema')

    <style>
        [x-cloak] { display: none !important; }

        body { cursor: default; }
        a, button, [role="button"] { cursor: pointer; }

        /* ── Reveal animations (EDITORIAL: only 2 directions) ── */
        .reveal-up { opacity: 0; transform: translateY(30px); transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal-up.visible { opacity: 1; transform: translateY(0); }
        .reveal-left { opacity: 0; transform: translateX(-30px); transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal-left.visible { opacity: 1; transform: translateX(0); }

        /* ── Gradient text ── */
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b 0%, #eab308 50%, #facc15 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Toast ── */
        .toast-enter { animation: toastIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .toast-leave { animation: toastOut 0.3s ease-in forwards; }
        @keyframes toastIn { from { opacity: 0; transform: translateY(-20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes toastOut { from { opacity: 1; } to { opacity: 0; transform: translateY(-10px); } }

        /* ── Step form — display by Alpine x-show ── */
        .step-content.active { animation: stepFade 0.4s ease; }
        @keyframes stepFade { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }

        /* ── Brass Line ── */
        @keyframes brass-line-glow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.7; }
        }
        .brass-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(201, 169, 110, 0.4), transparent);
        }
        .brass-line-vertical {
            width: 1px;
            background: linear-gradient(180deg, transparent, rgba(201, 169, 110, 0.3), transparent);
        }
        .brass-corner {
            position: absolute;
            width: 16px; height: 16px;
            border-color: rgba(201, 169, 110, 0.3);
        }
        .brass-corner-tl { top: 0; left: 0; border-top: 1px solid; border-left: 1px solid; }
        .brass-corner-tr { top: 0; right: 0; border-top: 1px solid; border-right: 1px solid; }
        .brass-corner-bl { bottom: 0; left: 0; border-bottom: 1px solid; border-left: 1px solid; }
        .brass-corner-br { bottom: 0; right: 0; border-bottom: 1px solid; border-right: 1px solid; }

        /* Horizontal scrollbar hide */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Marquee */
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 20s linear infinite; }

        /* Subtle grain texture overlay */
        .grain-overlay::after {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 9998;
            opacity: 0.02;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='1'/%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 256px 256px;
        }

        /* Section divider — brass rule */
        .section-rule {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(201, 169, 110, 0.25), transparent);
            margin: 0 auto;
            max-width: 80px;
        }
    </style>
</head>
<body class="font-sans antialiased bg-[#1A1220] text-cream-50 selection:bg-[#C9A96E]/30 selection:text-[#DCC9A0] overflow-x-hidden" x-data="{ scrolled: false, mobileOpen: false }" @scroll.window="scrolled = window.scrollY > 50">

    {{-- Page Transition Overlay — Premium Curtain --}}
    <div id="page-loader" class="fixed inset-0 bg-[#1A1220] z-[9999] pointer-events-auto" 
         style="transform: translateY(0); transition: transform 0.75s cubic-bezier(0.85, 0, 0.15, 1); will-change: transform;">
        {{-- Glowing Gold Laser Border on both edges --}}
        <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-[#C9A96E] to-transparent shadow-[0_0_12px_rgba(201,169,110,0.6)]"></div>
        <div class="absolute bottom-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-[#C9A96E] to-transparent shadow-[0_0_12px_rgba(201,169,110,0.6)]"></div>
    </div>

    {{-- Toast Container --}}
    <div x-data="{ toasts: [] }" @toast.window="toasts.push({ id: Date.now(), ...$event.detail }); setTimeout(() => toasts.shift(), 4000)" class="fixed top-6 right-4 z-[100] space-y-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" x-transition:enter="toast-enter" x-transition:leave="toast-leave"
                :class="toast.type === 'success' ? 'bg-[#C9A96E] text-[#1A1220]' : toast.type === 'error' ? 'bg-red-500 text-white' : 'bg-mauve-800 text-white'"
                class="px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px]">
                <template x-if="toast.type === 'success'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </template>
                <span x-text="toast.message" class="font-bold text-sm"></span>
            </div>
        </template>
    </div>

    {{-- Navigation — floating glass capsule with brass details --}}
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 ease-in-out py-4 md:py-6"
        :class="scrolled ? '!py-3.5' : ''">
        <div class="max-w-5xl mx-auto px-4 transition-all duration-500"
             :class="scrolled ? 'max-w-4xl' : ''">
            <div class="relative transition-all duration-500 ease-in-out px-6 py-3 flex items-center justify-between rounded-full border border-[#C9A96E]/10 shadow-2xl backdrop-blur-2xl bg-[#1A1220]/80 shadow-black/80"
                 :class="scrolled ? 'bg-[#1A1220]/90 border-[#C9A96E]/20 py-2' : ''">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#C9A96E] to-[#D4B896] flex items-center justify-center shadow-lg shadow-[#C9A96E]/25 group-hover:scale-105 transition-transform">
                        <span class="text-[#1A1220] font-extrabold text-sm">G</span>
                    </div>
                    <span class="font-bold text-sm tracking-widest uppercase transition-colors text-cream-50 hover:text-[#D4B896]" style="font-family: 'Playfair Display', serif;">Glow Studio</span>
                </a>

                {{-- Nav links --}}
                <nav class="hidden md:flex items-center gap-1 text-xs font-semibold uppercase tracking-wider relative" x-data="{ hoverLeft: 0, hoverWidth: 0, showLine: false }" @mouseleave="showLine = false">
                    <div class="absolute bottom-1 h-px bg-[#C9A96E]/60 transition-all duration-300 ease-out rounded-full pointer-events-none"
                         :style="`left: ${hoverLeft}px; width: ${hoverWidth}px; opacity: ${showLine ? 1 : 0};`">
                    </div>

                    @php
                        $links = [
                            ['name' => 'Beranda', 'url' => route('home')],
                            ['name' => 'Layanan', 'url' => route('home').'#layanan'],
                            ['name' => 'Lacak Booking', 'url' => route('bookings.track_form')],
                        ];
                    @endphp
                    @foreach($links as $link)
                        <a href="{{ $link['url'] }}" 
                           @mouseenter="showLine = true; hoverLeft = $el.offsetLeft; hoverWidth = $el.offsetWidth"
                           class="px-3 py-2 transition-all duration-300 relative text-cream/40 hover:text-cream-50">
                            {{ $link['name'] }}
                        </a>
                    @endforeach
                    
                    @auth
                        @if(auth()->user()->canAccessAdmin())
                            <a href="{{ route('admin.dashboard') }}" 
                               @mouseenter="showLine = true; hoverLeft = $el.offsetLeft; hoverWidth = $el.offsetWidth"
                               class="px-3 py-2 transition-all duration-300 relative text-[#C9A96E] hover:text-cream-50 border-l border-mauve-700 ml-1 pl-4">
                                Dashboard
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline-block relative ml-2">
                            @csrf
                            <button type="submit" 
                                    class="px-3 py-2 transition-all duration-300 text-cream/40 hover:text-rose-400 cursor-pointer text-xs font-semibold">
                                Keluar
                            </button>
                        </form>
                    @endauth
                </nav>

                {{-- Booking CTA — minimal, no gradient border --}}
                <a href="{{ route('home') }}#layanan" 
                   class="group hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider transition-all shadow-md active:scale-95 duration-300 bg-[#C9A96E] hover:bg-[#D4B896] text-[#1A1220] shadow-[#C9A96E]/15">
                    <span>Booking</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>

                {{-- Mobile Hamburger Button --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-xl text-cream/40 hover:text-cream-50 transition-colors" aria-label="Menu">
                    <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Mobile Navigation Drawer --}}
            <div x-show="mobileOpen" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="md:hidden mt-2 bg-zinc-950/95 backdrop-blur-2xl border border-mauve-700 rounded-2xl p-5 space-y-1 shadow-2xl">
                
                <a href="{{ route('home') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-cream/30 hover:text-cream-50 hover:bg-white/5 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Beranda
                </a>
                <a href="{{ route('home') }}#layanan" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-cream/30 hover:text-cream-50 hover:bg-white/5 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Layanan
                </a>
                <a href="{{ route('bookings.track_form') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-cream/30 hover:text-cream-50 hover:bg-white/5 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Lacak Booking
                </a>
                @auth
                    @if(auth()->user()->canAccessAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-amber-400 hover:text-white hover:bg-white/5 transition-all border-t border-mauve-700 mt-2 pt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5z"/></svg>
                            Dashboard Admin
                        </a>
                    @endif
                @endauth
                <a href="{{ route('home') }}#layanan" @click="mobileOpen = false" class="flex items-center justify-center gap-2 mt-3 w-full bg-amber-500 hover:bg-amber-400 text-black py-3 rounded-xl text-sm font-bold transition-all">
                    Booking Sekarang
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => { $dispatch('toast', { type: 'success', message: '{{ session('success') }}' }); show = false; }, 100)" x-show="show" class="fixed top-6 right-4 z-[100]">
            <div class="bg-[#C9A96E] text-[#1A1220] px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    {{-- Footer — editorial, clean, with brass rule --}}
    <footer class="mt-32 relative overflow-hidden">
        <div class="brass-line max-w-6xl mx-auto px-4"></div>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="col-span-2">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-[#C9A96E] to-[#D4B896] flex items-center justify-center">
                            <span class="text-[#1A1220] font-bold text-xs">G</span>
                        </div>
                        <span class="font-bold text-cream-50 text-sm" style="font-family: 'Playfair Display', serif;">Glow Studio</span>
                    </div>
                    <p class="text-cream/40 text-sm max-w-xs leading-relaxed">Premium salon experience. Tanpa antri, tanpa bentrok, tanpa ribet.</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-cream/30 mb-3">Navigasi</p>
                    <div class="space-y-2 text-sm">
                        <a href="{{ route('home') }}" class="block text-cream/40 hover:text-[#C9A96E] transition-colors">Beranda</a>
                        <a href="{{ route('home') }}#layanan" class="block text-cream/40 hover:text-[#C9A96E] transition-colors">Layanan</a>
                        <a href="{{ route('bookings.track_form') }}" class="block text-cream/40 hover:text-[#C9A96E] transition-colors">Lacak Booking</a>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-cream/30 mb-3">Kontak</p>
                    <div class="space-y-2 text-sm text-zinc-400">
                        <p>Jl. Kecantikan No. 1</p>
                        <p>Jakarta</p>
                        <p>0812-3456-7890</p>
                    </div>
                </div>
            </div>
            <div class="mt-10 pt-5 border-t border-mauve-700 text-center text-xs text-cream/30">
                &copy; {{ date('Y') }} Glow Studio
            </div>
        </div>
    </footer>

    {{-- Scroll Reveal — editorial, only reveal-up and reveal-left --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reveals = document.querySelectorAll('.reveal-up, .reveal-left');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
            reveals.forEach(el => observer.observe(el));
        });
    </script>

    {{-- Lenis Smooth Scroll — ultra-luxury inertia scrolling --}}
    <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.34/dist/lenis.min.js"></script>
    <script>
        const lenis = new Lenis({
            duration: 1.4,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            direction: 'vertical',
            gestureDirection: 'vertical',
            smooth: true,
            mouseMultiplier: 0.95,
            smoothTouch: false,
        })

        function raf(time) {
            lenis.raf(time)
            requestAnimationFrame(raf)
        }

        requestAnimationFrame(raf)
        
        // Custom smooth scrolling for anchor links via Lenis
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href') || '';
                    if (!href.startsWith('#') || href === '#') return;
                    
                    try {
                        const target = document.querySelector(href);
                        if (target) {
                            e.preventDefault();
                            lenis.scrollTo(target, { offset: -90, duration: 1.5 });
                        }
                    } catch (err) {
                        // Not a local selector, ignore and let standard navigation run
                    }
                });
            });
        });

        // Global Premium Page Transition Loader - Curtain Slide Out on Load
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.style.transition = 'transform 0.75s cubic-bezier(0.85, 0, 0.15, 1)';
                loader.style.transform = 'translateY(-100%)';
                loader.classList.remove('pointer-events-auto');
                loader.classList.add('pointer-events-none');
            }
        });

        // Handle Back/Forward cache (bfcache) pageshow transitions
        window.addEventListener('pageshow', function(event) {
            const loader = document.getElementById('page-loader');
            if (loader && event.persisted) {
                loader.style.transition = 'none';
                loader.style.transform = 'translateY(-100%)';
                loader.classList.remove('pointer-events-auto');
                loader.classList.add('pointer-events-none');
            }
        });

        // Intercept navigation links for smooth page exit transition
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href') || '';
                    
                    if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:') || this.getAttribute('target') === '_blank') {
                        return;
                    }
                    
                    try {
                        const targetUrl = new URL(href, window.location.href);
                        if (targetUrl.origin !== window.location.origin) {
                            return; // Don't intercept external domains
                        }
                    } catch (err) {
                        return;
                    }
                    
                    e.preventDefault();
                    
                    const loader = document.getElementById('page-loader');
                    if (loader) {
                        // Position loader at bottom first without transition
                        loader.style.transition = 'none';
                        loader.style.transform = 'translateY(100%)';
                        loader.offsetHeight; // Force DOM reflow
                        
                        // Slide loader up to cover the screen
                        loader.style.transition = 'transform 0.75s cubic-bezier(0.85, 0, 0.15, 1)';
                        loader.style.transform = 'translateY(0)';
                        loader.classList.remove('pointer-events-none');
                        loader.classList.add('pointer-events-auto');
                    }
                    
                    setTimeout(() => {
                        window.location.href = href;
                    }, 650);
                });
            });
        });

        // Upgraded Cosmic Dust Cursor Follower (Lerp interpolation with reactive expanding dot)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.matchMedia('(pointer: coarse)').matches) return; // Disable on touch devices

            const glow = document.createElement('div');
            glow.id = 'cursor-glow';
            glow.className = 'fixed pointer-events-none rounded-full blur-[100px] opacity-30 z-0 hidden md:block';
            glow.style.width = '400px';
            glow.style.height = '400px';
            glow.style.background = 'radial-gradient(circle, rgba(139, 92, 246, 0.16) 0%, rgba(245, 158, 11, 0.08) 40%, transparent 70%)';
            glow.style.transform = 'translate(-50%, -50%) translate3d(0, 0, 0)';
            glow.style.willChange = 'transform';
            document.body.appendChild(glow);

            const dot = document.createElement('div');
            dot.id = 'cursor-dot';
            dot.className = 'fixed pointer-events-none rounded-full border border-[#C9A96E]/40 z-[9997] hidden md:block';
            dot.style.width = '8px';
            dot.style.height = '8px';
            dot.style.background = 'rgba(201, 169, 110, 0.15)';
            dot.style.transform = 'translate(-50%, -50%) translate3d(0, 0, 0)';
            dot.style.transition = 'width 0.2s, height 0.2s, background-color 0.2s, border-color 0.2s';
            dot.style.willChange = 'transform';
            document.body.appendChild(dot);

            let mouseX = window.innerWidth / 2;
            let mouseY = window.innerHeight / 2;
            let currentX = mouseX;
            let currentY = mouseY;
            let dotX = mouseX;
            let dotY = mouseY;

            const easeGlow = 0.06; 
            const easeDot = 0.15; // Dot is snappier

            document.addEventListener('mousemove', function(e) {
                mouseX = e.clientX;
                mouseY = e.clientY;
            });

            // Expand dot on hover of links and interactive triggers
            const interactiveElements = 'a, button, [role="button"], label, input, select, textarea';
            document.addEventListener('mouseover', function(e) {
                if (e.target.closest(interactiveElements)) {
                    dot.style.width = '20px';
                    dot.style.height = '20px';
                    dot.style.backgroundColor = 'rgba(245, 158, 11, 0.35)';
                    dot.style.borderColor = 'rgba(245, 158, 11, 0.7)';
                }
            });
            document.addEventListener('mouseout', function(e) {
                if (e.target.closest(interactiveElements)) {
                    dot.style.width = '8px';
                    dot.style.height = '8px';
                    dot.style.backgroundColor = 'rgba(245, 158, 11, 0.15)';
                    dot.style.borderColor = 'rgba(245, 158, 11, 0.4)';
                }
            });

            function updateGlow() {
                currentX += (mouseX - currentX) * easeGlow;
                currentY += (mouseY - currentY) * easeGlow;
                glow.style.transform = `translate(-50%, -50%) translate3d(${currentX}px, ${currentY}px, 0)`;

                dotX += (mouseX - dotX) * easeDot;
                dotY += (mouseY - dotY) * easeDot;
                dot.style.transform = `translate(-50%, -50%) translate3d(${dotX}px, ${dotY}px, 0)`;

                requestAnimationFrame(updateGlow);
            }
            requestAnimationFrame(updateGlow);
        });

        // Golden Click Ripples
        document.addEventListener('click', function(e) {
            const ripple = document.createElement('div');
            ripple.className = 'fixed pointer-events-none rounded-full border border-[#C9A96E]/30 z-[9998]';
            ripple.style.width = '10px';
            ripple.style.height = '10px';
            ripple.style.left = `${e.clientX}px`;
            ripple.style.top = `${e.clientY}px`;
            ripple.style.transform = 'translate(-50%, -50%) scale(1)';
            ripple.style.background = 'radial-gradient(circle, rgba(201, 169, 110, 0.15), transparent 75%)';
            ripple.style.transition = 'transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1)';
            ripple.style.willChange = 'transform, opacity';
            document.body.appendChild(ripple);

            // Force DOM reflow to trigger transition
            ripple.offsetHeight;

            ripple.style.transform = 'translate(-50%, -50%) scale(12)';
            ripple.style.opacity = '0';

            setTimeout(() => {
                ripple.remove();
            }, 800);
        });

    </script>
</body>
</html>
