@extends('layouts.public')

@section('title', 'Glow Studio — Premium Salon')
@section('meta_description', 'Premium salon booking — tanpa antri, tanpa bentrok.')

@section('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BeautySalon",
  "name": "Glow Studio Jakarta",
  "image": "https://glowstudio.id/assets/hero.jpg",
  "@@id": "https://glowstudio.id/#salon",
  "url": "https://glowstudio.id",
  "telephone": "+6281234567890",
  "priceRange": "$$",
  "address": {
    "@@type": "PostalAddress",
    "streetAddress": "Jl. Kecantikan No. 1",
    "addressLocality": "Jakarta",
    "postalCode": "12345",
    "addressCountry": "ID"
  },
  "openingHoursSpecification": {
    "@@type": "OpeningHoursSpecification",
    "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
    "opens": "09:00",
    "closes": "20:00"
  }
}
</script>
@endsection

@section('content')

<style>
    .package-builder-container {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 2.5rem !important;
        width: 100% !important;
        align-items: stretch !important;
    }
    .pb-left-panel { flex: 1 1 58% !important; min-width: 320px !important; display: flex !important; flex-direction: column !important; gap: 1.5rem !important; }
    .pb-right-panel { flex: 1 1 36% !important; min-width: 300px !important; display: flex !important; flex-direction: column !important; justify-content: space-between !important; }
    .pb-services-grid { display: grid !important; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)) !important; gap: 1rem !important; width: 100% !important; }
    .pb-card-selected { border-color: rgba(245, 158, 11, 0.8) !important; background: rgba(245, 158, 11, 0.1) !important; box-shadow: 0 10px 30px -5px rgba(245, 158, 11, 0.2) !important; transform: translateY(-2px) !important; }
    .pb-card-unselected { border-color: rgba(255, 255, 255, 0.05) !important; background: rgba(255, 255, 255, 0.02) !important; }
    .pb-card-unselected:hover { border-color: rgba(245, 158, 11, 0.3) !important; background: rgba(255, 255, 255, 0.04) !important; transform: translateY(-3px) !important; box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.08) !important; }
    .pb-summary-card { background: rgba(255, 255, 255, 0.02) !important; border: 1px solid rgba(255, 255, 255, 0.06) !important; backdrop-filter: blur(24px) !important; -webkit-backdrop-filter: blur(24px) !important; border-radius: 1.5rem !important; padding: 2rem !important; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important; }
    .pb-summary-card:hover { border-color: rgba(245, 158, 11, 0.15) !important; }
    .pb-booking-btn { background: #f59e0b !important; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important; border-radius: 1.25rem !important; }
    .pb-booking-btn:hover { background: #d97706 !important; box-shadow: 0 12px 30px -5px rgba(245, 158, 11, 0.3) !important; transform: translateY(-2px) !important; }

    @keyframes floating-slow { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    .animate-floating-slow { animation: floating-slow 6s infinite ease-in-out; }

    /* Treatment card — minimal, architectural */
    .treatment-card { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); will-change: transform, border-color, box-shadow; }
    .treatment-card:hover { transform: translateY(-4px); border-color: rgba(245, 158, 11, 0.25); box-shadow: 0 20px 40px -15px rgba(245, 158, 11, 0.06), 0 15px 30px -10px rgba(0,0,0,0.8); }
    .treatment-card-featured { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); will-change: transform, border-color, box-shadow; }
    .treatment-card-featured:hover { transform: translateY(-5px); border-color: rgba(245, 158, 11, 0.35); box-shadow: 0 25px 50px -15px rgba(245, 158, 11, 0.12), 0 20px 40px -10px rgba(0,0,0,0.9); }

    /* Floating Golden Embers Background */
    @keyframes float-embers {
        0% { transform: translateY(0) translateX(0) scale(0.6); opacity: 0; }
        15% { opacity: 0.4; }
        85% { opacity: 0.4; }
        100% { transform: translateY(-160px) translateX(30px) scale(1); opacity: 0; }
    }
    .ember {
        position: absolute;
        pointer-events: none;
        background: radial-gradient(circle, #f59e0b 0%, rgba(245, 158, 11, 0.35) 45%, transparent 80%);
        border-radius: 50%;
        filter: blur(1px);
        animation: float-embers 14s infinite linear;
        will-change: transform, opacity;
    }
    
    /* Animated spotlight border glow on Featured card */
    @keyframes card-glow {
        0%, 100% { border-color: rgba(245, 158, 11, 0.12); box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7); }
        50% { border-color: rgba(245, 158, 11, 0.35); box-shadow: 0 25px 60px -15px rgba(245, 158, 11, 0.08); }
    }
    .featured-glow {
        animation: card-glow 6s infinite ease-in-out;
    }
</style>

{{-- ===================================================== --}}
{{-- HERO — Editorial Cover Split --}}
{{-- ===================================================== --}}
<section class="relative min-h-[95svh] flex items-center overflow-hidden bg-[#050505] border-b border-zinc-900" x-data="heroParallax()" @mousemove="move($event)" @mouseleave="reset()">
    {{-- Single subtle orb --}}
    <div class="absolute top-1/3 right-1/4 w-[450px] h-[450px] rounded-full opacity-[0.05] pointer-events-none transition-transform duration-700 ease-out" style="background: radial-gradient(circle, #f59e0b, transparent 70%); filter: blur(110px);" :style="`transform: translate3d(${xOffset * -25}px, ${yOffset * -25}px, 0)`"></div>

    {{-- Giant background watermark "RADIANCE" --}}
    <div class="absolute select-none text-[15vw] font-black uppercase tracking-[0.25em] text-white/[0.008] pointer-events-none font-serif leading-none transition-transform duration-700 ease-out" style="left: 10%; top: 35%; transform: translate3d(0,0,0); will-change: transform;" :style="`transform: translate3d(${xOffset * -15}px, ${yOffset * -15}px, 0)`">RADIANCE</div>

    {{-- Floating Gold Embers Background --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0 hidden md:block">
        <div class="ember" style="width: 4px; height: 4px; left: 12%; bottom: -5%; animation-delay: 0s; animation-duration: 12s;"></div>
        <div class="ember" style="width: 5px; height: 5px; left: 28%; bottom: -5%; animation-delay: 2s; animation-duration: 16s;"></div>
        <div class="ember" style="width: 3px; height: 3px; left: 45%; bottom: -5%; animation-delay: 4s; animation-duration: 13s;"></div>
        <div class="ember" style="width: 6px; height: 6px; left: 62%; bottom: -5%; animation-delay: 1s; animation-duration: 18s;"></div>
        <div class="ember" style="width: 4px; height: 4px; left: 78%; bottom: -5%; animation-delay: 5s; animation-duration: 15s;"></div>
        <div class="ember" style="width: 5px; height: 5px; left: 92%; bottom: -5%; animation-delay: 3s; animation-duration: 14s;"></div>
        <div class="ember" style="width: 3px; height: 3px; left: 20%; bottom: -5%; animation-delay: 7s; animation-duration: 11s;"></div>
        <div class="ember" style="width: 4px; height: 4px; left: 52%; bottom: -5%; animation-delay: 9s; animation-duration: 17s;"></div>
    </div>

    {{-- Brass corner brackets framing the hero --}}
    <div class="absolute top-0 left-0 md:ml-12 md:mt-12 w-8 h-8 hidden md:block" style="border-top: 1px solid rgba(245, 158, 11, 0.2); border-left: 1px solid rgba(245, 158, 11, 0.2);"></div>
    <div class="absolute top-0 right-0 md:mr-12 md:mt-12 w-8 h-8 hidden md:block" style="border-top: 1px solid rgba(245, 158, 11, 0.2); border-right: 1px solid rgba(245, 158, 11, 0.2);"></div>
    <div class="absolute bottom-0 left-0 md:ml-12 md:mb-12 w-8 h-8 hidden md:block" style="border-bottom: 1px solid rgba(245, 158, 11, 0.2); border-left: 1px solid rgba(245, 158, 11, 0.2);"></div>
    <div class="absolute bottom-0 right-0 md:mr-12 md:mb-12 w-8 h-8 hidden md:block" style="border-bottom: 1px solid rgba(245, 158, 11, 0.2); border-right: 1px solid rgba(245, 158, 11, 0.2);"></div>

    <div class="relative z-10 w-full max-w-6xl mx-auto px-6 md:px-12 py-32 md:py-40">
        <div class="grid md:grid-cols-12 gap-12 md:gap-16 items-center">
            {{-- LEFT: Editorial headline --}}
            <div class="md:col-span-7 space-y-8 reveal-up transition-transform duration-700 ease-out" style="will-change: transform;" :style="`transform: translate3d(${xOffset * -8}px, ${yOffset * -8}px, 0)`">
                {{-- Department label — like a magazine masthead --}}
                <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-[0.25em] text-zinc-600 mb-6">
                    <span class="w-6 h-px bg-zinc-700"></span>
                    Salon · Jakarta
                </div>
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-medium tracking-tight text-white leading-[0.95]" style="font-family: 'Playfair Display', serif;">
                    Beauty
                    <span class="text-zinc-500 font-light italic">without</span>
                    <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black uppercase tracking-[0.08em] gradient-text">the wait.</span>
                </h1>

                {{-- Salon manifesto — 2 lines of refined copy --}}
                <p class="text-zinc-500 text-sm md:text-base leading-relaxed max-w-md">
                    A curated reservation experience. Select your certified stylist, choose your moment, and receive instant confirmation — no queues, no friction.
                </p>

                {{-- Reservation rail --}}
                <div class="flex items-center gap-6 pt-4">
                    <a href="#layanan"
                       class="group inline-flex items-center gap-2.5 px-7 py-3.5 bg-amber-500 hover:bg-amber-400 text-black rounded-full text-xs font-bold uppercase tracking-wider transition-all shadow-lg shadow-amber-500/15 hover:shadow-amber-500/30 active:scale-[0.97]">
                        <span>Reserve Now</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <span class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest">or</span>
                    <a href="{{ route('bookings.track_form') }}" class="text-zinc-400 hover:text-white text-xs font-semibold tracking-wide transition-colors border-b border-zinc-800 pb-0.5">
                        Track booking
                    </a>
                </div>

                {{-- Trust indicator — minimal --}}
                <div class="flex items-center gap-4 pt-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-600">★★★★★</span>
                    <span class="text-zinc-600 text-[10px] font-semibold">4.9 · 500+ clients</span>
                    <span class="w-px h-3 bg-zinc-800"></span>
                    <span class="text-emerald-400 text-[10px] font-bold uppercase tracking-wider">Open Today</span>
                </div>
            </div>

            {{-- RIGHT: Multi-layered floating cards --}}
            <div class="md:col-span-5 relative flex items-center justify-center min-h-[350px]">
                
                {{-- Main card: Featured Treatment panel --}}
                <div class="relative bg-zinc-950/80 border border-zinc-800 rounded-3xl p-8 md:p-10 w-full shadow-2xl transition-transform duration-700 ease-out z-10 featured-glow" style="will-change: transform;" :style="`transform: translate3d(${xOffset * 15}px, ${yOffset * 15}px, 0) rotate3d(1, 1, 0, ${(xOffset + yOffset) * 4}deg)`">
                    {{-- Brass corners on the card --}}
                    <div class="absolute top-3 left-3 w-4 h-4" style="border-top: 1px solid rgba(245, 158, 11, 0.25); border-left: 1px solid rgba(245, 158, 11, 0.25);"></div>
                    <div class="absolute top-3 right-3 w-4 h-4" style="border-top: 1px solid rgba(245, 158, 11, 0.25); border-right: 1px solid rgba(245, 158, 11, 0.25);"></div>

                    <div class="text-[9px] font-bold uppercase tracking-[0.2em] text-zinc-500 mb-4">Featured Treatment</div>
                    <h3 class="text-2xl md:text-3xl font-bold text-white leading-tight" style="font-family: 'Playfair Display', serif;">
                        Hair Color<br>Treatment
                    </h3>
                    <p class="text-zinc-500 text-sm mt-3 leading-relaxed">Full colour with deep conditioning for maximum radiance.</p>

                    <div class="brass-line my-6"></div>

                    <div class="flex items-end justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-600">From</span>
                            <p class="text-3xl font-black gradient-text mt-1">Rp 450K</p>
                        </div>
                        <a href="{{ route('bookings.create', $services->first() ?: 'hair-color') }}"
                           class="w-11 h-11 rounded-full bg-amber-500 hover:bg-amber-400 text-black flex items-center justify-center transition-all hover:scale-105 active:scale-95 shadow-lg shadow-amber-500/15">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Floating Card 2: Active Stylist Standby --}}
                <div class="absolute -top-6 -left-6 bg-zinc-950/95 border border-zinc-800 rounded-2xl px-4 py-3 flex items-center gap-3 shadow-2xl backdrop-blur-md transition-transform duration-700 ease-out z-20 select-none pointer-events-none" style="will-change: transform;" :style="`transform: translate3d(${xOffset * 28}px, ${yOffset * 28}px, 0)`">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 relative flex">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </div>
                    <div class="text-[10px] font-semibold text-zinc-300">
                        <span class="font-bold text-white block">Sasha R.</span>
                        Active Standby
                    </div>
                </div>

                {{-- Floating Card 3: Trust Stats --}}
                <div class="absolute -bottom-6 -right-6 bg-zinc-950/95 border border-zinc-800 rounded-2xl px-4 py-3 flex items-center gap-3 shadow-2xl backdrop-blur-md transition-transform duration-700 ease-out z-20 select-none pointer-events-none" style="will-change: transform;" :style="`transform: translate3d(${xOffset * -15}px, ${yOffset * -15}px, 0)`">
                    <div class="text-amber-500 text-xs">★★★★★</div>
                    <div class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest leading-none">
                        500+ Clients
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================================================== --}}
{{-- MARQUEE — subtle, like a fashion show --}}
{{-- ===================================================== --}}
<section class="py-5 bg-[#050505] border-y border-zinc-900/60 overflow-hidden">
    <div class="flex whitespace-nowrap">
        <div class="animate-marquee flex items-center gap-10 text-5xl font-black uppercase tracking-[0.15em] text-white/[0.015] select-none" style="font-family: 'Playfair Display', serif;">
            @foreach(['HAIR CARE', 'NAIL ART', 'FACIAL', 'HAIR COLOR', 'SPA', 'BRIDAL', 'MAKEUP', 'TREATMENT'] as $cat)
                <span>{{ $cat }}</span>
                <span class="text-amber-500/8 text-3xl">✦</span>
            @endforeach
            @foreach(['HAIR CARE', 'NAIL ART', 'FACIAL', 'HAIR COLOR', 'SPA', 'BRIDAL', 'MAKEUP', 'TREATMENT'] as $cat)
                <span>{{ $cat }}</span>
                <span class="text-amber-500/8 text-3xl">✦</span>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================================================== --}}
{{-- SERVICES — Curated Treatment Gallery --}}
{{-- ===================================================== --}}
<section id="layanan" class="relative py-24 lg:py-32 bg-[#050505]" x-data="{ searchQuery: '' }">
    <div class="max-w-6xl mx-auto px-6 sm:px-10">
        <div class="grid lg:grid-cols-12 gap-12">
            {{-- LEFT: Gallery label + filter --}}
            <div class="lg:col-span-4 lg:sticky lg:top-24 lg:self-start space-y-6 reveal-up">
                <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500">
                    <span class="w-6 h-px bg-amber-500/40"></span>
                    Treatments
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-[1.05]" style="font-family: 'Playfair Display', serif;">
                    Curated<br>
                    <span class="gradient-text italic font-medium">gallery.</span>
                </h2>
                <p class="text-zinc-500 text-sm leading-relaxed max-w-xs">
                    Each touch is a masterpiece. Browse our exclusive treatments and discover your ideal beauty session.
                </p>

                {{-- Search --}}
                <div class="relative group">
                    <input type="text" x-model="searchQuery" placeholder="Filter treatments..."
                        class="w-full rounded-2xl border border-zinc-800 bg-zinc-950/80 px-5 py-3.5 text-xs font-semibold tracking-wide focus:ring-1 focus:ring-amber-500/40 focus:border-amber-500 outline-none transition-all text-white placeholder-zinc-600">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600 group-focus-within:text-amber-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="block text-[10px] text-zinc-600 font-semibold uppercase tracking-wider">{{ $services->count() }} treatments</span>
                </div>

                {{-- Trending Treatments Widget --}}
                @if($services->isNotEmpty())
                    <div class="border border-zinc-800/80 bg-zinc-950/30 rounded-2xl p-5 space-y-4 relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-16 h-16 rounded-full opacity-[0.02] pointer-events-none" style="background: radial-gradient(circle, #f59e0b, transparent 70%); filter: blur(20px);"></div>
                        <div class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.15em] text-amber-400">
                            <span class="flex h-1.5 w-1.5 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-amber-500"></span>
                            </span>
                            Trending This Week
                        </div>
                        <div class="space-y-3">
                            @foreach($services->take(2) as $trending)
                                <a href="{{ route('bookings.create', $trending) }}" class="group flex items-center justify-between gap-3 p-2.5 rounded-xl hover:bg-zinc-900/50 transition-all border border-transparent hover:border-zinc-800/60">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors truncate">{{ $trending->name }}</p>
                                        <p class="text-[9px] text-zinc-500 mt-0.5">{{ $trending->duration_minutes }} min · {{ $trending->stylists->count() }} stylists</p>
                                    </div>
                                    <span class="text-xs font-black text-zinc-400 group-hover:text-amber-400 shrink-0">{{ $trending->formattedPrice() }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- RIGHT: Treatment gallery --}}
            <div class="lg:col-span-8 space-y-4 reveal-up" style="transition-delay: 150ms;">
                @forelse($services as $index => $service)
                    @php
                        $isFeatured = $index === 0;
                        $bgTones = ['zinc-950/60', 'zinc-950/40', 'zinc-950/50', 'zinc-950/30'];
                        $bgTone = $bgTones[$index % 4];
                    @endphp

                    <a href="{{ route('bookings.create', $service) }}"
                        x-show="searchQuery === '' || '{{ strtolower($service->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($service->description) }}'.includes(searchQuery.toLowerCase())"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        class="treatment-card-{{ $isFeatured ? 'featured' : 'card' }} group relative flex flex-col sm:flex-row sm:items-center justify-between gap-5 rounded-2xl border border-zinc-800/60 p-6 sm:p-7 {{ $isFeatured ? 'bg-zinc-950/80 border-amber-500/10' : $bgTone }}">

                        {{-- Brass corner on featured --}}
                        @if($isFeatured)
                            <div class="absolute top-2 right-2 w-3 h-3" style="border-top: 1px solid rgba(245, 158, 11, 0.3); border-right: 1px solid rgba(245, 158, 11, 0.3);"></div>
                            <span class="absolute -top-2.5 left-6 px-3 py-0.5 bg-amber-500 text-black text-[8px] font-bold uppercase tracking-widest rounded-full">Featured</span>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-1.5">
                                <span class="text-[9px] font-bold uppercase tracking-widest text-zinc-600">{{ $service->duration_minutes }} min</span>
                                @if($isFeatured)
                                    <span class="text-[9px] font-bold text-amber-400/60">·</span>
                                    <span class="text-[9px] font-bold text-amber-400/60">Most popular</span>
                                @endif
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold text-white group-hover:text-amber-400 transition-colors duration-300">{{ $service->name }}</h3>
                            @if($service->description)
                                <p class="text-zinc-500 text-xs mt-1.5 leading-relaxed max-w-md">{{ Str::limit($service->description, 90) }}</p>
                            @endif

                            {{-- Mood tags --}}
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @php
                                    $moods = ['Essential', 'Premium', 'Express', 'Signature'];
                                    $mood = $moods[$index % 4];
                                    $moodColors = ['bg-zinc-800/60 text-zinc-400', 'bg-amber-500/8 text-amber-400/70', 'bg-zinc-800/60 text-zinc-400', 'bg-zinc-800/60 text-zinc-400'];
                                @endphp
                                <span class="text-[8px] font-bold uppercase tracking-wider border border-zinc-800 px-2.5 py-0.5 rounded-full {{ $moodColors[$index % 4] }}">{{ $mood }}</span>
                                @if($service->stylists->count() > 0)
                                    <span class="text-[8px] font-semibold text-zinc-600 border border-zinc-800 px-2.5 py-0.5 rounded-full">{{ $service->stylists->count() }} stylist</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-4 sm:shrink-0">
                            <span class="text-lg font-black text-white group-hover:text-amber-400 transition-colors tracking-tight">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                            <div class="w-9 h-9 rounded-full bg-zinc-800 group-hover:bg-amber-500 flex items-center justify-center transition-all group-hover:shadow-lg shadow-amber-500/15 opacity-50 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-zinc-400 group-hover:text-black transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-16 col-span-8">
                        <p class="text-zinc-600">No active treatments.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

{{-- ===================================================== --}}
{{-- STYLIST MATCHER — Beauty Concierge --}}
{{-- ===================================================== --}}
<section class="max-w-6xl mx-auto px-6 sm:px-10 py-16" x-data="stylistMatcher()">
    <div class="relative rounded-3xl border border-zinc-800/80 p-8 sm:p-12 text-white overflow-hidden" style="background: #080808;">
        {{-- Subtle glow --}}
        <div class="absolute -right-20 -bottom-20 w-72 h-72 rounded-full opacity-[0.05] pointer-events-none" style="background: radial-gradient(circle, #f59e0b, transparent 70%); filter: blur(60px);"></div>

        {{-- Watermark text --}}
        <div class="absolute -left-6 top-1/2 -translate-y-1/2 text-[100px] sm:text-[140px] font-black text-white/[0.008] pointer-events-none select-none tracking-tighter leading-none" style="font-family: 'Playfair Display', serif;">CONCIERGE</div>

        <div class="relative z-10">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6 mb-10">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500">
                        <span class="w-6 h-px bg-amber-500/40"></span>
                        Beauty Concierge
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                        Find your<br><span class="gradient-text italic">counterpart.</span>
                    </h2>
                    <p class="text-zinc-500 text-sm max-w-lg">Select a vibe and let our system recommend the perfect stylist for your aesthetic.</p>
                </div>

                {{-- Vibe selector pills --}}
                <div class="flex flex-wrap gap-2">
                    <template x-for="vibe in vibes" :key="vibe.id">
                        <button @click="selectVibe(vibe)"
                            class="px-5 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all duration-300 border cursor-pointer active:scale-95"
                            :class="activeVibeId === vibe.id
                                ? 'bg-amber-500 border-amber-500 text-black shadow-lg'
                                : 'bg-zinc-900 border-zinc-800 text-zinc-400 hover:bg-zinc-800 hover:text-white'">
                            <span x-text="vibe.emoji + ' ' + vibe.name"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Result dossier --}}
            <div x-show="matchedStylist"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="border border-zinc-800/80 rounded-2xl p-6 sm:p-8 bg-zinc-950/50">
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    {{-- Avatar placeholder --}}
                    <div class="w-20 h-20 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-4xl shrink-0 shadow-inner">
                        <span x-text="matchedEmoji">💇‍♀️</span>
                    </div>

                    <div class="flex-1 min-w-0 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <h3 class="text-2xl font-bold text-white" x-text="matchedStylist.name"></h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border"
                                  :style="`background: ${matchedColor}10; border-color: ${matchedColor}30; color: ${matchedColor};`"
                                  x-text="matchedStylist.specialty || 'Master Stylist'"></span>
                        </div>
                        <p class="text-zinc-400 text-sm leading-relaxed" x-text="matchedStylist.bio || 'Senior stylist with a passion for luxury transformations.'"></p>

                        {{-- Philosophy line --}}
                        <div class="brass-line pt-1"></div>
                        <div class="flex items-center justify-between gap-4 pt-1">
                            <div>
                                <span class="text-[9px] font-bold uppercase tracking-widest text-zinc-600">Recommended treatment</span>
                                <p class="text-sm text-amber-400 font-bold mt-0.5" x-text="recommendedServiceName"></p>
                            </div>
                            <a :href="bookingUrl"
                               class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-black px-5 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all shadow-lg active:scale-95">
                                <span>Book</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function stylistMatcher() {
    return {
        vibes: [
            { id: 'glam', name: 'Glamorous Glow', emoji: '✨', color: '#f59e0b', emojiStylist: '👸' },
            { id: 'classic', name: 'Minimalist Classic', emoji: '🌿', color: '#eab308', emojiStylist: '👩‍💼' },
            { id: 'edgy', name: 'Bold & Edgy', emoji: '⚡', color: '#d97706', emojiStylist: '👨‍🎤' },
            { id: 'romance', name: 'Sensual Romance', emoji: '🌹', color: '#facc15', emojiStylist: '👩‍🎨' }
        ],
        stylists: @json($stylists),
        services: @json($services),
        activeVibeId: '',
        matchedStylist: null,
        matchedColor: '#f59e0b',
        matchedEmoji: '💇‍♀️',
        recommendedServiceName: '',
        bookingUrl: '#',

        init() {
            if (this.vibes.length > 0) this.selectVibe(this.vibes[0]);
        },

        selectVibe(vibe) {
            this.activeVibeId = vibe.id;
            this.matchedColor = vibe.color;
            this.matchedEmoji = vibe.emojiStylist;
            if (this.stylists.length === 0) return;

            const index = Math.abs(vibe.id.charCodeAt(0) + vibe.id.charCodeAt(1)) % this.stylists.length;
            this.matchedStylist = this.stylists[index];

            if (this.services.length > 0) {
                let matchedService = null;
                if (vibe.id === 'glam') matchedService = this.services.find(s => s.slug?.includes('color') || s.slug?.includes('spa')) || this.services[0];
                else if (vibe.id === 'classic') matchedService = this.services.find(s => s.slug?.includes('cut') || s.slug?.includes('manicure')) || this.services[0];
                else if (vibe.id === 'edgy') matchedService = this.services.find(s => s.slug?.includes('cut') || s.slug?.includes('color')) || this.services[0];
                else matchedService = this.services.find(s => s.slug?.includes('makeup') || s.slug?.includes('bridal') || s.slug?.includes('manicure')) || this.services[0];
                this.recommendedServiceName = matchedService.name;
                this.bookingUrl = `/book/${matchedService.slug}`;
            } else {
                this.recommendedServiceName = 'Premium Treatment';
                this.bookingUrl = '#';
            }
        }
    }
}
</script>

{{-- ===================================================== --}}
{{-- GALLERY — Gallery of Transformations --}}
{{-- ===================================================== --}}
<section id="gallery" class="relative py-24 lg:py-32 bg-[#050505] overflow-hidden" x-data="gallerySection()">
    <div class="absolute top-1/4 left-1/3 w-[300px] h-[300px] rounded-full opacity-[0.03] pointer-events-none" style="background: radial-gradient(circle, #f59e0b, transparent 70%); filter: blur(80px);"></div>

    <div class="max-w-6xl mx-auto px-6 sm:px-10">
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6 mb-12">
            <div class="space-y-3">
                <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500">
                    <span class="w-6 h-px bg-amber-500/40"></span>
                    Portfolio & Testimonials
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight" style="font-family: 'Playfair Display', serif;">
                    Gallery of<br><span class="gradient-text italic">transformations.</span>
                </h2>
            </div>

            {{-- Tabs --}}
            <div class="flex flex-wrap gap-2">
                <button @click="setTab('all')"
                    class="px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all border cursor-pointer active:scale-95"
                    :class="activeTab === 'all'
                        ? 'bg-amber-500 border-amber-500 text-black shadow-lg shadow-amber-500/15'
                        : 'bg-zinc-900 border-zinc-800 text-zinc-400 hover:bg-zinc-800 hover:text-white'">
                    All Works
                </button>
                <button @click="setTab('before_after')"
                    class="px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all border cursor-pointer active:scale-95"
                    :class="activeTab === 'before_after'
                        ? 'bg-amber-500 border-amber-500 text-black shadow-lg shadow-amber-500/15'
                        : 'bg-zinc-900 border-zinc-800 text-zinc-400 hover:bg-zinc-800 hover:text-white'">
                    Before / After
                </button>
                <button @click="setTab('portfolio')"
                    class="px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all border cursor-pointer active:scale-95"
                    :class="activeTab === 'portfolio'
                        ? 'bg-amber-500 border-amber-500 text-black shadow-lg shadow-amber-500/15'
                        : 'bg-zinc-900 border-zinc-800 text-zinc-400 hover:bg-zinc-800 hover:text-white'">
                    Artistic Portfolio
                </button>
                <button @click="setTab('testimonial')"
                    class="px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all border cursor-pointer active:scale-95"
                    :class="activeTab === 'testimonial'
                        ? 'bg-amber-500 border-amber-500 text-black shadow-lg shadow-amber-500/15'
                        : 'bg-zinc-900 border-zinc-800 text-zinc-400 hover:bg-zinc-800 hover:text-white'">
                    Client Reviews
                </button>
            </div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($galleryItems as $item)
                <div class="relative group rounded-3xl border border-zinc-800/60 bg-zinc-950/40 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 flex flex-col justify-between"
                     x-show="activeTab === 'all' || activeTab === '{{ $item->type }}'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    
                    {{-- Brass corner accent --}}
                    <div class="absolute top-3 left-3 w-3.5 h-3.5 pointer-events-none" style="border-top: 1px solid rgba(245, 158, 11, 0.2); border-left: 1px solid rgba(245, 158, 11, 0.2);"></div>
                    <div class="absolute top-3 right-3 w-3.5 h-3.5 pointer-events-none" style="border-top: 1px solid rgba(245, 158, 11, 0.2); border-right: 1px solid rgba(245, 158, 11, 0.2);"></div>

                    <div>
                        {{-- Image content based on type --}}
                        @if($item->type === 'before_after')
                            <div class="relative grid grid-cols-2 h-56 border-b border-zinc-900">
                                @if($item->before_image_path)
                                    <div class="relative h-full overflow-hidden">
                                        <img src="{{ Storage::url($item->before_image_path) }}" alt="Before" class="w-full h-full object-cover">
                                        <span class="absolute bottom-3 left-3 bg-black/70 text-zinc-400 text-[8px] font-bold px-2 py-0.5 rounded tracking-widest uppercase">Before</span>
                                    </div>
                                @else
                                    <div class="h-full bg-zinc-900/60 flex items-center justify-center text-zinc-700 text-xs">No Before Photo</div>
                                @endif

                                @if($item->after_image_path)
                                    <div class="relative h-full overflow-hidden">
                                        <img src="{{ Storage::url($item->after_image_path) }}" alt="After" class="w-full h-full object-cover">
                                        <span class="absolute bottom-3 right-3 bg-amber-500/80 text-black text-[8px] font-bold px-2 py-0.5 rounded tracking-widest uppercase">After</span>
                                    </div>
                                @else
                                    <div class="h-full bg-zinc-900/60 flex items-center justify-center text-zinc-700 text-xs">No After Photo</div>
                                @endif
                            </div>
                        @elseif($item->type === 'portfolio' && $item->image_path)
                            <div class="relative h-56 overflow-hidden border-b border-zinc-900">
                                <img src="{{ Storage::url($item->image_path) }}" alt="Portfolio" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <span class="absolute bottom-3 right-3 bg-zinc-950/70 border border-zinc-800 text-zinc-300 text-[8px] font-bold px-2.5 py-0.5 rounded-full tracking-widest uppercase">Portfolio</span>
                            </div>
                        @elseif($item->type === 'testimonial')
                            <div class="h-56 bg-zinc-950/20 flex flex-col justify-center px-8 relative border-b border-zinc-900">
                                <span class="absolute top-6 left-8 text-5xl text-amber-500/10 font-serif pointer-events-none select-none">“</span>
                                <p class="text-zinc-400 text-xs leading-relaxed italic relative z-10">"{{ $item->review_text }}"</p>
                                <span class="absolute bottom-3 right-3 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[8px] font-bold px-2.5 py-0.5 rounded-full tracking-widest uppercase">Review</span>
                            </div>
                        @endif

                        {{-- Metadata Content --}}
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-3 mb-2.5">
                                @if($item->service)
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-400/80">{{ $item->service->name }}</span>
                                @else
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-zinc-500">Premium Treatment</span>
                                @endif

                                @if($item->rating)
                                    <span class="text-amber-500 text-[10px] tracking-wider">{{ str_repeat('★', $item->rating) }}</span>
                                @endif
                            </div>

                            @if($item->client_name)
                                <h4 class="font-bold text-sm text-white">{{ $item->client_name }}</h4>
                            @endif

                            @if($item->type !== 'testimonial' && $item->review_text)
                                <p class="text-zinc-500 text-xs mt-2 leading-relaxed italic">"{{ Str::limit($item->review_text, 100) }}"</p>
                            @endif

                            @if($item->stylist)
                                <div class="flex items-center gap-2 mt-4 pt-3.5 border-t border-zinc-900">
                                    <div class="w-5 h-5 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-[9px] text-white font-bold">
                                        {{ substr($item->stylist->name, 0, 1) }}
                                    </div>
                                    <span class="text-[10px] text-zinc-400">Stylist: <strong class="text-zinc-300 font-semibold">{{ $item->stylist->name }}</strong></span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 border border-dashed border-zinc-800 rounded-3xl bg-zinc-950/10">
                    <span class="text-3xl block mb-3">✨</span>
                    <p class="text-zinc-500 text-sm font-semibold">Transformations Gallery coming soon.</p>
                    <p class="text-zinc-700 text-xs mt-1">Our featured creations will be displayed here.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<script>
function gallerySection() {
    return {
        activeTab: 'all',
        setTab(tab) {
            this.activeTab = tab;
        }
    }
}
</script>

{{-- ===================================================== --}}
{{-- PROCESS — 3 steps, refined --}}
{{-- ===================================================== --}}
<section class="max-w-6xl mx-auto px-6 sm:px-10 py-24 lg:py-32">
    <div class="grid lg:grid-cols-12 gap-12">
        <div class="lg:col-span-4 space-y-4 reveal-up">
            <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500">
                <span class="w-6 h-px bg-amber-500/40"></span>
                How it works
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-[0.95]" style="font-family: 'Playfair Display', serif;">
                Simple.<br>Three steps.
            </h2>
            <div class="section-rule mt-6"></div>
        </div>

        <div class="lg:col-span-8 space-y-0">
            @php
                $steps = [
                    ['num' => '01', 'title' => 'Select Service & Stylist', 'desc' => 'Browse our curated treatments and choose the stylist that matches your vibe.'],
                    ['num' => '02', 'title' => 'Pick Your Moment', 'desc' => 'Choose a date and time. Our system checks availability in real-time.'],
                    ['num' => '03', 'title' => 'Instant Confirmation', 'desc' => 'Booking confirmed instantly via email & WhatsApp. No waiting.'],
                ];
            @endphp

            @foreach($steps as $i => $step)
                <div class="group relative pl-20 pb-12 last:pb-0 reveal-left" style="transition-delay: {{ $i * 150 }}ms">
                    @if($i < count($steps) - 1)
                        <div class="absolute left-[23px] top-14 bottom-0 w-px bg-gradient-to-b from-zinc-800 to-transparent"></div>
                    @endif
                    <div class="absolute left-0 top-0 w-11 h-11 rounded-xl border border-zinc-800 flex items-center justify-center transition-colors group-hover:border-amber-500/40">
                        <span class="text-xs font-bold text-zinc-500 group-hover:text-amber-400 transition-colors">{{ $step['num'] }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-white group-hover:text-amber-400 transition-colors">{{ $step['title'] }}</h3>
                    <p class="text-zinc-500 text-sm mt-1 leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================================================== --}}
{{-- RITUAL COMPOSER — Package Builder, refined --}}
{{-- ===================================================== --}}
<section class="max-w-6xl mx-auto px-6 sm:px-10 py-16 lg:py-24" x-data="packageBuilder()">
    <div class="relative rounded-3xl border border-zinc-800/80 p-8 sm:p-12 text-white overflow-hidden" style="background: #080808;">
        <div class="absolute -right-20 -bottom-20 w-72 h-72 rounded-full opacity-[0.05] pointer-events-none" style="background: radial-gradient(circle, #eab308, transparent 70%); filter: blur(60px);"></div>
        <div class="absolute -left-6 top-1/4 text-[100px] sm:text-[140px] font-black text-white/[0.008] pointer-events-none select-none tracking-tighter leading-none" style="font-family: 'Playfair Display', serif;">RITUAL</div>

        <div class="relative z-10 package-builder-container">
            <div class="pb-left-panel">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500">
                        <span class="w-6 h-px bg-amber-500/40"></span>
                        Ritual Composer
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
                        Compose your<br><span class="gradient-text italic">session.</span>
                    </h2>
                    <p class="text-zinc-500 text-xs sm:text-sm max-w-2xl leading-relaxed">Stack multiple treatments to see total duration and investment.</p>
                </div>

                <div class="pb-services-grid pt-4">
                    @foreach($services as $service)
                        <label class="group relative flex flex-col justify-between p-5 rounded-2xl border cursor-pointer transition-all duration-300 select-none min-h-[140px]"
                            :class="selectedServices.includes({{ $service->id }}) ? 'pb-card-selected' : 'pb-card-unselected'">

                            <div class="flex items-center justify-between w-full relative z-10">
                                <div class="relative flex items-center justify-center w-5 h-5 rounded-full border transition-all duration-300 shrink-0"
                                    :class="selectedServices.includes({{ $service->id }}) ? 'border-amber-400 bg-amber-500' : 'border-zinc-700 bg-zinc-900'">
                                    <svg x-show="selectedServices.includes({{ $service->id }})" class="w-3 h-3 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-white/8 text-lg font-serif select-none">✦</span>
                            </div>

                            <div class="min-w-0 w-full relative z-10 mt-5">
                                <span class="font-bold text-sm text-white block truncate group-hover:text-amber-300 transition-colors duration-300">{{ $service->name }}</span>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-[8px] bg-zinc-900 border border-zinc-800 px-2 py-0.5 rounded text-zinc-500 font-bold tracking-wider uppercase">{{ $service->duration_minutes }} min</span>
                                    <span class="text-xs text-amber-400 font-bold">{{ $service->formattedPrice() }}</span>
                                </div>
                            </div>

                            <input type="checkbox" value="{{ $service->id }}"
                                @change="toggleService({{ $service->id }}, '{{ $service->name }}', {{ $service->price }}, {{ $service->duration_minutes }}, '{{ $service->slug }}')"
                                class="hidden">
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Summary panel --}}
            <div class="pb-right-panel pb-summary-card min-h-[320px]">
                <div>
                    <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Composition</span>
                        <span class="text-[9px] bg-zinc-900 text-zinc-400 border border-zinc-800 px-2.5 py-0.5 rounded-full font-bold" x-text="selectedServices.length + ' selected'">0 selected</span>
                    </div>

                    <div class="mt-4 space-y-2 max-h-[160px] overflow-y-auto no-scrollbar pr-1">
                        <div x-show="selectedServices.length === 0" class="text-center py-8">
                            <div class="w-8 h-8 rounded-full bg-zinc-900 flex items-center justify-center mx-auto mb-2 border border-zinc-800">
                                <span class="text-zinc-600 text-xs">✦</span>
                            </div>
                            <p class="text-xs text-zinc-600 max-w-[180px] mx-auto leading-relaxed">Select treatments to compose your session.</p>
                        </div>
                        <template x-for="item in selectedItems" :key="item.id">
                            <div class="flex items-center justify-between text-xs py-2 border-b border-zinc-800/50 px-1 rounded-lg">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-1 h-1 rounded-full bg-amber-400 shrink-0"></span>
                                    <span class="text-zinc-300 font-semibold truncate" x-text="item.name"></span>
                                </div>
                                <span class="text-amber-400 font-bold shrink-0" x-text="formatRupiah(item.price)"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="pt-5 border-t border-zinc-800 mt-6 space-y-4">
                    <div class="flex justify-between items-center text-xs text-zinc-500 font-semibold">
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Total duration</span>
                        </div>
                        <span class="text-white font-bold text-sm" x-text="totalDuration + ' min'">0</span>
                    </div>

                    <div class="flex justify-between items-end pt-2">
                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Estimated investment</span>
                        <span class="text-2xl font-black gradient-text" x-text="formatRupiah(totalPrice)">Rp 0</span>
                    </div>

                    <a :href="bookingUrl"
                        :class="selectedServices.length > 0 ? 'pb-booking-btn text-black hover:shadow-lg' : 'bg-zinc-900 text-zinc-600 pointer-events-none cursor-not-allowed border border-zinc-800'"
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-wider transition-all">
                        <span>Book Session</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function packageBuilder() {
    return {
        selectedServices: [],
        selectedItems: [],
        totalPrice: 0,
        totalDuration: 0,
        bookingUrl: '#',

        toggleService(id, name, price, duration, slug) {
            const idx = this.selectedServices.indexOf(id);
            if (idx > -1) {
                this.selectedServices.splice(idx, 1);
                this.selectedItems = this.selectedItems.filter(item => item.id !== id);
                this.totalPrice -= price;
                this.totalDuration -= duration;
            } else {
                this.selectedServices.push(id);
                this.selectedItems.push({ id, name, price, duration, slug });
                this.totalPrice += price;
                this.totalDuration += duration;
            }
            if (this.selectedItems.length > 0) {
                const primary = this.selectedItems[0];
                this.bookingUrl = `/book/${primary.slug}?package=true&services=${this.selectedItems.map(i => i.id).join(',')}`;
            } else {
                this.bookingUrl = '#';
            }
        },

        formatRupiah(value) {
            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    }
}
</script>

{{-- ===================================================== --}}
{{-- CTA — calm, editorial, with brass rule --}}
{{-- ===================================================== --}}
<section class="relative overflow-hidden border-t border-zinc-900 py-24 lg:py-32" style="background: #050505;">
    <div class="max-w-3xl mx-auto px-6 text-center space-y-8">
        <div class="section-rule"></div>
        <div class="space-y-4 reveal-up">
            <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-zinc-600">Begin your ritual</span>
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-[0.9]" style="font-family: 'Playfair Display', serif;">
                Ready to<br><span class="gradient-text italic">radiate?</span>
            </h2>
            <p class="text-zinc-500 text-sm max-w-md mx-auto leading-relaxed">Book now and experience a premium salon ritual crafted for you.</p>
        </div>
        <div class="reveal-up" style="transition-delay: 150ms;">
            <a href="#layanan" class="group inline-flex items-center gap-2.5 bg-amber-500 hover:bg-amber-400 text-black px-8 py-4 rounded-full text-xs font-bold uppercase tracking-wider transition-all shadow-lg shadow-amber-500/15 hover:shadow-amber-500/30 active:scale-[0.97]">
                <span>Book Now</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        <p class="text-zinc-700 text-xs reveal-up" style="transition-delay: 200ms;">No registration fee · Free consultation</p>
    </div>
</section>

<script>
function heroParallax() {
    return {
        xOffset: 0,
        yOffset: 0,
        move(e) {
            const centerX = window.innerWidth / 2;
            const centerY = window.innerHeight / 2;
            this.xOffset = (e.clientX - centerX) / centerX;
            this.yOffset = (e.clientY - centerY) / centerY;
        },
        reset() {
            this.xOffset = 0;
            this.yOffset = 0;
        }
    }
}
</script>

@endsection
