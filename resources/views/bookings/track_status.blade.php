@extends('layouts.public')

@section('title', 'Status Booking '.$booking->reference)

@section('content')
<section class="relative min-h-[100svh] flex flex-col justify-start overflow-hidden pt-28 pb-20 text-white" style="background: #050505;">
    {{-- Subtle glow --}}
    <div class="absolute top-1/4 left-1/3 w-[400px] h-[400px] rounded-full opacity-[0.03] pointer-events-none" style="background: radial-gradient(circle, #f59e0b, transparent 70%); filter: blur(100px);"></div>

    <div class="relative z-10 w-full max-w-2xl mx-auto px-4 sm:px-6">
        {{-- Back --}}
        <a href="{{ route('bookings.track_form') }}" class="inline-flex items-center gap-2 text-sm text-zinc-600 hover:text-amber-400 transition-colors mb-6 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7 7l-7-7 7-7"/></svg>
            <span class="text-xs font-semibold">Back to tracking</span>
        </a>

        {{-- Main card --}}
        <div class="border border-zinc-800 rounded-2xl p-6 sm:p-10 space-y-10 relative">
            {{-- Brass corners --}}
            <div class="absolute top-3 left-3 w-4 h-4 pointer-events-none" style="border-top: 1px solid rgba(245, 158, 11, 0.2); border-left: 1px solid rgba(245, 158, 11, 0.2);"></div>

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-zinc-800/60 pb-6 gap-4">
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-widest text-zinc-500">Reference Code</span>
                    <h1 class="text-2xl sm:text-3xl font-mono font-bold tracking-wider text-white mt-0.5 uppercase">{{ $booking->reference }}</h1>
                </div>
                <div class="shrink-0">
                    @if($booking->status === 'cancelled')
                        <span class="inline-flex items-center gap-1.5 bg-red-500/10 border border-red-500/20 text-red-400 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                            {{ $booking->statusLabel() }}
                        </span>
                    @elseif($booking->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 bg-amber-500/10 border border-amber-500/20 text-amber-400 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                            {{ $booking->statusLabel() }}
                        </span>
                    @elseif($booking->status === 'confirmed')
                        <span class="inline-flex items-center gap-1.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            {{ $booking->statusLabel() }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            {{ $booking->statusLabel() }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Cancellation --}}
            @if($booking->status === 'cancelled')
                <div class="bg-red-500/8 border border-red-500/15 text-red-200/80 px-5 py-4 rounded-xl text-sm leading-relaxed">
                    <p class="font-bold flex items-center gap-2 text-xs">&#10007; Booking Cancelled</p>
                    <p class="text-zinc-500 text-xs mt-1.5">Your appointment has been cancelled. Contact us via WhatsApp at 0812-3456-7890 to reschedule.</p>
                </div>
            @endif

            {{-- Timeline -- brass spine with capsule markers --}}
            <div class="select-none">
                @php
                    $steps = [
                        [
                            'title' => 'Booking Received',
                            'desc' => 'Your request has been received and is being reviewed by our team.',
                            'is_completed' => true,
                            'is_active' => $booking->status === 'pending',
                        ],
                        [
                            'title' => 'Confirmed & Stylist Assigned',
                            'desc' => 'Stylist schedule locked. Preparation for your premium session begins.',
                            'is_completed' => in_array($booking->status, ['confirmed', 'completed']),
                            'is_active' => $booking->status === 'confirmed',
                        ],
                        [
                            'title' => 'Treatment Complete',
                            'desc' => 'Thank you for trusting Glow Studio. See you at your next session!',
                            'is_completed' => $booking->status === 'completed',
                            'is_active' => $booking->status === 'completed',
                        ]
                    ];
                @endphp

                @foreach($steps as $index => $step)
                    @php
                        $isCompleted = $step['is_completed'] && $booking->status !== 'cancelled';
                        $isActive = $step['is_active'] && $booking->status !== 'cancelled';

                        if ($isActive) {
                            $markerClass = 'bg-amber-500 border-amber-500 shadow-lg shadow-amber-500/20';
                            $textClass = 'text-amber-400 font-bold';
                            $descOpacity = '';
                        } elseif ($isCompleted) {
                            $markerClass = 'bg-emerald-500/80 border-emerald-500/80';
                            $textClass = 'text-white font-semibold';
                            $descOpacity = '';
                        } else {
                            $markerClass = 'bg-zinc-900 border-zinc-800';
                            $textClass = 'text-zinc-600';
                            $descOpacity = 'opacity-30';
                        }
                    @endphp

                    <div class="flex gap-5">
                        {{-- Left: capsule marker + brass line spine --}}
                        <div class="flex flex-col items-center shrink-0" style="width: 24px;">
                            <div class="w-3 h-3 rounded-full border transition-all duration-300 {{ $markerClass }}" style="margin-top: 5px;"></div>
                            @if($index < count($steps) - 1)
                                <div class="w-px flex-1" :class="$isActive ? 'bg-amber-500/30' : 'bg-zinc-800'" style="min-height: 40px;"></div>
                            @endif
                        </div>

                        {{-- Right: text --}}
                        <div class="pb-10 flex-1">
                            <h3 class="text-sm transition-colors duration-300 {{ $textClass }}">{{ $step['title'] }}</h3>
                            <p class="text-zinc-500 text-xs mt-1.5 leading-relaxed {{ $descOpacity }}">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Details --}}
            <div class="border border-zinc-800/60 rounded-xl p-6 sm:p-8 space-y-4">
                <h3 class="font-bold text-sm text-white" style="font-family: 'Playfair Display', serif;">Reservation Details</h3>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div class="bg-zinc-950/50 border border-zinc-800/50 p-3.5 rounded-xl">
                        <span class="text-zinc-600 text-[9px] uppercase font-bold tracking-widest block mb-1">Treatment</span>
                        <span class="font-bold text-white text-sm">{{ $booking->service->name }}</span>
                    </div>
                    <div class="bg-zinc-950/50 border border-zinc-800/50 p-3.5 rounded-xl">
                        <span class="text-zinc-600 text-[9px] uppercase font-bold tracking-widest block mb-1">Stylist</span>
                        <span class="font-bold text-white text-sm">{{ $booking->stylist->name ?? '-' }}</span>
                    </div>
                    <div class="bg-zinc-950/50 border border-zinc-800/50 p-3.5 rounded-xl">
                        <span class="text-zinc-600 text-[9px] uppercase font-bold tracking-widest block mb-1">Arrival</span>
                        <span class="font-bold text-white text-sm">{{ $booking->scheduled_at->timezone(config('app.timezone'))->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="bg-zinc-950/50 border border-zinc-800/50 p-3.5 rounded-xl">
                        <span class="text-zinc-600 text-[9px] uppercase font-bold tracking-widest block mb-1">Total</span>
                        <span class="font-bold gradient-text text-sm">{{ $booking->service->formattedPrice() }}</span>
                    </div>
                </div>

                @if($booking->status === 'pending')
                    {{-- Cancellation --}}
                    <div x-data="{ openConfirm: false }" class="pt-5 border-t border-zinc-800/60 select-none">
                        <div x-show="!openConfirm" x-transition class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-red-500/5 border border-red-500/10 p-4 rounded-xl">
                            <div>
                                <h4 class="font-bold text-xs text-red-400/80">Need to cancel?</h4>
                                <p class="text-[10px] text-zinc-500 mt-0.5">You can cancel your reservation independently.</p>
                            </div>
                            <button type="button" @click="openConfirm = true" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 text-[10px] px-4 py-2 rounded-xl font-bold border border-red-500/20 hover:border-red-500/30 transition-all shrink-0 cursor-pointer">
                                Cancel Booking
                            </button>
                        </div>

                        <div x-show="openConfirm" x-transition x-cloak class="bg-red-500/8 border border-red-500/15 p-5 rounded-xl text-center space-y-3">
                            <p class="text-sm font-semibold text-white">Are you sure you want to cancel?</p>
                            <p class="text-[10px] text-zinc-500">This action is permanent and cannot be undone automatically.</p>
                            <div class="flex justify-center gap-3">
                                <button type="button" @click="openConfirm = false" class="bg-zinc-900 border border-zinc-800 hover:bg-zinc-800 text-white text-[10px] px-4 py-2 rounded-xl font-bold transition-all cursor-pointer">
                                    Keep Booking
                                </button>
                                <form method="POST" action="{{ route('bookings.cancel_client', $booking) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-[10px] px-4 py-2 rounded-xl font-bold transition-all shadow-lg cursor-pointer">
                                        Yes, Cancel Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('bookings.track_form') }}" class="inline-flex items-center gap-2 border border-zinc-800 text-zinc-500 hover:text-white hover:border-zinc-700 px-6 py-3 rounded-xl text-xs font-bold transition-all">
                Track Another Booking
            </a>
        </div>
    </div>
</section>
@endsection
