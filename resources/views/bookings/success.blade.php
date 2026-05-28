@extends('layouts.public')

@section('title', 'Booking Berhasil — Glow Studio')

@section('content')
@php
    $startAt = $booking->scheduled_at->timezone(config('app.timezone'));
    $endAt = $booking->ends_at ? $booking->ends_at->timezone(config('app.timezone')) : $startAt->copy()->addMinutes($booking->service->duration_minutes);
    $googleCalendarUrl = 'https://www.google.com/calendar/render?action=TEMPLATE'
        . '&text=' . urlencode('Glow Studio — ' . $booking->service->name)
        . '&dates=' . $startAt->format('Ymd\\THis') . '/' . $endAt->format('Ymd\\THis')
        . '&details=' . urlencode('Booking ' . $booking->reference . "\nStylist: " . ($booking->stylist->name ?? '-') . "\n" . ($booking->notes ?? ''))
        . '&location=' . urlencode('Glow Studio Jakarta, Jl. Kecantikan No. 1')
        . '&sf=true&output=xml';
@endphp

<section class="relative min-h-[100svh] flex flex-col justify-center overflow-hidden pt-28 pb-20" style="background: #050505;">
    {{-- Halo ring animation — concentric circles --}}
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
        <div class="w-[400px] h-[400px] sm:w-[500px] sm:h-[500px] rounded-full border border-amber-500/5 animate-ping" style="animation-duration: 4s; animation-iteration-count: 1;"></div>
        <div class="absolute w-[300px] h-[300px] sm:w-[400px] sm:h-[400px] rounded-full border border-amber-500/8 animate-ping" style="animation-duration: 4s; animation-delay: 0.5s; animation-iteration-count: 1;"></div>
        <div class="absolute w-[200px] h-[200px] sm:w-[300px] sm:h-[300px] rounded-full border border-amber-500/10 animate-ping" style="animation-duration: 4s; animation-delay: 1s; animation-iteration-count: 1;"></div>
    </div>

    {{-- Subtle glow --}}
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-[400px] h-[400px] rounded-full opacity-[0.04] pointer-events-none" style="background: radial-gradient(circle, #f59e0b, transparent 70%); filter: blur(100px);"></div>

    <style>
        @keyframes sealReveal {
            0% { transform: scale(0) rotate(-30deg); opacity: 0; }
            60% { transform: scale(1.05) rotate(2deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        .animate-seal { animation: sealReveal 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    </style>

    <div class="relative z-10 w-full max-w-lg mx-auto px-4 sm:px-6 space-y-6">
        {{-- Booking seal --}}
        <div class="flex justify-center animate-seal" style="animation-delay: 0.2s;">
            <div class="relative">
                <div class="w-24 h-24 rounded-2xl bg-amber-500/10 border border-amber-500/25 flex items-center justify-center shadow-xl shadow-amber-500/5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                {{-- Brass corner accents on seal --}}
                <div class="absolute -top-1 -left-1 w-3 h-3 pointer-events-none" style="border-top: 1px solid rgba(245, 158, 11, 0.4); border-left: 1px solid rgba(245, 158, 11, 0.4);"></div>
                <div class="absolute -bottom-1 -right-1 w-3 h-3 pointer-events-none" style="border-bottom: 1px solid rgba(245, 158, 11, 0.4); border-right: 1px solid rgba(245, 158, 11, 0.4);"></div>
            </div>
        </div>

        <div class="text-center space-y-3 animate-fade-in-up" style="animation-delay: 0.4s;">
            <h1 class="text-2xl sm:text-3xl font-bold text-white" style="font-family: 'Playfair Display', serif;">Booking Confirmed</h1>
            <p class="text-zinc-500 text-sm max-w-sm mx-auto leading-relaxed">Your reservation has been received. Save your reference code to track status anytime.</p>
        </div>

        {{-- Reference code — booking seal badge --}}
        <div class="border border-zinc-800 rounded-2xl p-6 sm:p-8 text-center animate-fade-in-up" style="animation-delay: 0.5s;">
            <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mb-2">Reference Code</p>
            <p class="text-2xl sm:text-3xl font-mono font-bold text-amber-400 tracking-[0.12em]">{{ $booking->reference }}</p>
            <p class="brass-line max-w-[60px] mx-auto mt-3"></p>
            <p class="text-[10px] text-zinc-600 mt-3">Use this code to track your booking anytime.</p>
        </div>

        {{-- Itinerary card --}}
        <div class="border border-zinc-800 rounded-2xl p-6 sm:p-8 animate-fade-in-up" style="animation-delay: 0.6s;">
            <h3 class="font-bold text-sm text-white mb-4" style="font-family: 'Playfair Display', serif;">Itinerary</h3>
            <div class="divide-y divide-zinc-800/60 border border-zinc-800/60 rounded-xl overflow-hidden text-xs">
                <div class="flex justify-between items-center px-4 py-3.5">
                    <span class="text-zinc-500">Treatment</span>
                    <span class="font-bold text-white">{{ $booking->service->name }}</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3.5">
                    <span class="text-zinc-500">Stylist</span>
                    <span class="font-bold text-white">{{ $booking->stylist->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3.5">
                    <span class="text-zinc-500">Date & Time</span>
                    <span class="font-bold text-white">{{ $startAt->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3.5">
                    <span class="text-zinc-500">Duration</span>
                    <span class="font-bold text-white">{{ $booking->service->duration_minutes }} min</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3.5">
                    <span class="text-zinc-500">Total</span>
                    <span class="font-bold gradient-text">{{ $booking->service->formattedPrice() }}</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3.5">
                    <span class="text-zinc-500">Status</span>
                    <span class="inline-flex items-center gap-1.5 bg-amber-500/10 border border-amber-500/20 text-amber-400 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                        {{ $booking->statusLabel() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="grid sm:grid-cols-2 gap-3 animate-fade-in-up" style="animation-delay: 0.8s;">
            <a href="{{ route('bookings.track_status', ['reference' => $booking->reference]) }}"
               class="inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-400 text-black px-5 py-3.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all shadow-lg shadow-amber-500/15 active:scale-[0.97]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Track Status
            </a>
            <a href="{{ $googleCalendarUrl }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 border border-zinc-800 text-zinc-400 hover:text-white hover:border-zinc-700 px-5 py-3.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Add to Calendar
            </a>
            <a href="{{ route('home') }}"
               class="sm:col-span-2 inline-flex items-center justify-center gap-1 text-zinc-600 hover:text-zinc-400 px-5 py-3 rounded-xl transition-all text-xs font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7 7l-7-7 7-7"/></svg>
                Back to Home
            </a>
        </div>
    </div>
</section>
@endsection
