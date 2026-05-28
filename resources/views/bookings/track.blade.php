@extends('layouts.public')

@section('title', 'Lacak Booking — Glow Studio')

@section('content')
<section class="relative min-h-[85svh] flex items-center overflow-hidden text-white pt-24" style="background: #050505;">
    {{-- Subtle glow --}}
    <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] rounded-full opacity-[0.03] pointer-events-none" style="background: radial-gradient(circle, #f59e0b, transparent 70%); filter: blur(100px);"></div>

    <div class="relative z-10 w-full max-w-md mx-auto px-6 py-12">
        <div class="text-center mb-10 space-y-4">
            <span class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500">
                <span class="w-6 h-px bg-amber-500/40"></span>
                Reservation Ledger
            </span>
            <h1 class="text-3xl sm:text-4xl font-bold text-white leading-tight" style="font-family: 'Playfair Display', serif;">
                Track Your<br>Reservation
            </h1>
            <p class="text-zinc-500 text-sm max-w-xs mx-auto leading-relaxed">Enter your booking reference code to check live status.</p>
        </div>

        @if($errors->has('reference'))
            <div class="mb-6 bg-red-500/8 border border-red-500/15 text-red-200/80 px-5 py-4 rounded-xl text-xs flex items-center gap-2.5">
                <span>⚠</span>
                <p>{{ $errors->first('reference') }}</p>
            </div>
        @endif

        {{-- Search card --}}
        <div class="border border-zinc-800 rounded-2xl p-6 sm:p-8 relative">
            {{-- Brass corners --}}
            <div class="absolute top-2 left-2 w-3 h-3 pointer-events-none" style="border-top: 1px solid rgba(245, 158, 11, 0.2); border-left: 1px solid rgba(245, 158, 11, 0.2);"></div>

            <form method="GET" action="{{ route('bookings.track_status') }}">
                <div class="space-y-5">
                    <div>
                        <label class="text-[9px] font-bold uppercase tracking-widest text-zinc-500 mb-2 block">Reference Code</label>
                        <input type="text" name="reference" value="{{ old('reference', request('reference')) }}" required
                            placeholder="SB-XXXXXXXX"
                            class="w-full input-dark text-center text-base font-mono tracking-wider focus:ring-1 focus:ring-amber-500/40 focus:border-amber-500 outline-none transition-all placeholder-zinc-700 uppercase">
                    </div>

                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-400 text-black py-3.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all shadow-lg shadow-amber-500/15 active:scale-[0.97]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Track Now
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-xs text-zinc-600 hover:text-zinc-400 transition-colors font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 inline -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7 7l-7-7 7-7"/></svg>
                Back to Home
            </a>
        </div>
    </div>
</section>
@endsection
