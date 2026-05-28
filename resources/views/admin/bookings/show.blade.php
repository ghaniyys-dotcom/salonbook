@extends('layouts.admin')

@section('heading', 'Booking '.$booking->reference)

@section('content')
<div class="max-w-2xl">
    {{-- Status Banner --}}
    <div class="mb-6 rounded-2xl p-4 flex items-center gap-3
        {{ $booking->status === 'pending' ? 'bg-amber-50 border border-amber-200' : '' }}
        {{ $booking->status === 'confirmed' ? 'bg-blue-50 border border-blue-200' : '' }}
        {{ $booking->status === 'completed' ? 'bg-emerald-50 border border-emerald-200' : '' }}
        {{ $booking->status === 'cancelled' ? 'bg-red-50 border border-red-200' : '' }}">
        <div class="w-3 h-3 rounded-full
            {{ $booking->status === 'pending' ? 'bg-amber-500 animate-pulse' : '' }}
            {{ $booking->status === 'confirmed' ? 'bg-blue-500' : '' }}
            {{ $booking->status === 'completed' ? 'bg-emerald-500' : '' }}
            {{ $booking->status === 'cancelled' ? 'bg-red-500' : '' }}"></div>
        <span class="font-semibold
            {{ $booking->status === 'pending' ? 'text-amber-700' : '' }}
            {{ $booking->status === 'confirmed' ? 'text-blue-700' : '' }}
            {{ $booking->status === 'completed' ? 'text-emerald-700' : '' }}
            {{ $booking->status === 'cancelled' ? 'text-red-700' : '' }}">{{ $booking->statusLabel() }}</span>
    </div>

    {{-- Details Card --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100 bg-stone-50">
            <h2 class="font-bold">{{ $booking->service->name }}</h2>
            <p class="text-stone-500 text-sm font-mono mt-0.5">{{ $booking->reference }}</p>
        </div>

        <div class="p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                <div>
                    <dt class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Customer</dt>
                    <dd class="font-medium text-stone-800">{{ $booking->customer_name }}</dd>
                </div>
                <div>
                    <dt class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Telepon</dt>
                    <dd class="font-medium text-stone-800">{{ $booking->customer_phone }}</dd>
                </div>
                <div>
                    <dt class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Email</dt>
                    <dd class="font-medium text-stone-800">{{ $booking->customer_email }}</dd>
                </div>
                <div>
                    <dt class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Stylist</dt>
                    <dd class="font-medium text-stone-800">{{ $booking->stylist->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Jadwal</dt>
                    <dd class="font-medium text-stone-800">{{ $booking->scheduled_at->format('d M Y H:i') }} — {{ $booking->ends_at->format('H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Durasi</dt>
                    <dd class="font-medium text-stone-800">{{ $booking->service->duration_minutes }} menit</dd>
                </div>
                <div>
                    <dt class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Harga</dt>
                    <dd class="font-bold text-violet-600">{{ $booking->service->formattedPrice() }}</dd>
                </div>
                @if($booking->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Catatan</dt>
                        <dd class="text-stone-600 bg-stone-50 rounded-xl p-3 mt-1">{{ $booking->notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Actions / Quick Action Cards --}}
    @if(!in_array($booking->status, ['completed', 'cancelled']))
        <div class="mt-6 bg-white rounded-2xl border border-stone-200 p-6 shadow-sm">
            <h3 class="font-bold text-stone-800 text-xs uppercase tracking-widest mb-4">Quick Actions — Kelola Reservasi</h3>
            
            <div class="grid sm:grid-cols-2 gap-4">
                {{-- Confirm Action (if pending) --}}
                @if($booking->status === 'pending')
                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="w-full flex items-center justify-between p-4 rounded-xl border border-blue-100 bg-blue-50/50 hover:bg-blue-50 hover:border-blue-300 transition-all text-left group">
                            <div>
                                <span class="font-bold text-blue-700 block text-sm">Konfirmasi Reservasi</span>
                                <span class="text-blue-500/80 text-[10px] mt-1 block">Kunci jadwal stylist & kirim email konfirmasi</span>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">✓</span>
                        </button>
                    </form>
                @endif

                {{-- Complete Action (if confirmed) --}}
                @if($booking->status === 'confirmed')
                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="w-full flex items-center justify-between p-4 rounded-xl border border-emerald-100 bg-emerald-50/50 hover:bg-emerald-50 hover:border-emerald-300 transition-all text-left group">
                            <div>
                                <span class="font-bold text-emerald-700 block text-sm">Selesaikan Reservasi</span>
                                <span class="text-emerald-500/80 text-[10px] mt-1 block">Tandai perawatan selesai dan arsipkan</span>
                            </div>
                            <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">✨</span>
                        </button>
                    </form>
                @endif

                {{-- Cancel Action (always available if not completed/cancelled) --}}
                <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" onsubmit="return confirm('Yakin batalkan booking ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full flex items-center justify-between p-4 rounded-xl border border-red-100 bg-red-50/50 hover:bg-red-50 hover:border-red-300 transition-all text-left group">
                        <div>
                            <span class="font-bold text-red-700 block text-sm">Batalkan Reservasi</span>
                            <span class="text-red-500/80 text-[10px] mt-1 block">Batalkan jadwal dan bebaskan kembali slot stylist</span>
                        </div>
                        <span class="w-8 h-8 rounded-lg bg-red-500 text-white flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">✕</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('admin.bookings.index') }}" class="text-violet-600 hover:underline text-sm font-medium flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke daftar booking
        </a>
    </div>
</div>
@endsection
