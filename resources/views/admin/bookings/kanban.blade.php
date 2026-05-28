@extends('layouts.admin')

@section('title', 'Kanban Board')
@section('heading', 'Booking Pipeline')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">Alur Kerja Operasional</p>
        <h2 class="text-2xl font-bold text-stone-800">Manajemen Pipeline Reservasi</h2>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-1.5 bg-white border border-stone-200 px-4 py-2 rounded-xl text-stone-600 hover:text-stone-800 font-medium text-xs shadow-sm transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Daftar Tabel
        </a>
        <a href="{{ route('admin.bookings.kanban') }}" class="inline-flex items-center gap-1.5 bg-violet-600 text-white px-4 py-2 rounded-xl font-medium text-xs shadow-sm shadow-violet-500/10 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
            Papan Kanban
        </a>
    </div>
</div>

{{-- Kanban Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 items-start overflow-x-auto pb-4">
    
    @php
        $columns = [
            'pending' => [
                'title' => 'Menunggu',
                'bg' => 'bg-amber-500',
                'border' => 'border-amber-200',
                'card_border' => 'hover:border-amber-300',
                'badge' => 'bg-amber-100 text-amber-800',
                'color' => 'amber'
            ],
            'confirmed' => [
                'title' => 'Dikonfirmasi',
                'bg' => 'bg-blue-500',
                'border' => 'border-blue-200',
                'card_border' => 'hover:border-blue-300',
                'badge' => 'bg-blue-100 text-blue-800',
                'color' => 'blue'
            ],
            'completed' => [
                'title' => 'Selesai',
                'bg' => 'bg-emerald-500',
                'border' => 'border-emerald-200',
                'card_border' => 'hover:border-emerald-300',
                'badge' => 'bg-emerald-100 text-emerald-800',
                'color' => 'emerald'
            ],
            'cancelled' => [
                'title' => 'Dibatalkan',
                'bg' => 'bg-red-500',
                'border' => 'border-red-200',
                'card_border' => 'hover:border-red-300',
                'badge' => 'bg-red-100 text-red-800',
                'color' => 'red'
            ]
        ];
    @endphp

    @foreach($columns as $status => $col)
        <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 shrink-0 min-w-[280px] w-full shadow-sm">
            {{-- Column Header --}}
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-stone-200/60">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $col['bg'] }}"></span>
                    <h3 class="font-bold text-stone-700 text-sm tracking-tight">{{ $col['title'] }}</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $col['badge'] }}">
                    {{ $grouped[$status]->count() }}
                </span>
            </div>

            {{-- Column Cards --}}
            <div class="space-y-3 min-h-[350px] overflow-y-auto max-h-[70vh] no-scrollbar">
                @forelse($grouped[$status] as $booking)
                    <div class="bg-white border border-stone-200 rounded-xl p-4 shadow-sm hover:shadow-md hover:scale-[1.01] {{ $col['card_border'] }} transition-all duration-200 group relative">
                        {{-- Booking Reference & Status Dot --}}
                        <div class="flex items-center justify-between gap-2 mb-2.5">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="font-mono text-xs font-extrabold text-violet-600 hover:underline">
                                {{ $booking->reference }}
                            </a>
                            <span class="text-[10px] text-stone-400 font-semibold">
                                {{ $booking->scheduled_at->timezone(config('app.timezone'))->format('H:i') }} WIB
                            </span>
                        </div>

                        {{-- Service & Stylist Info --}}
                        <div class="space-y-1.5 mb-4">
                            <p class="font-bold text-stone-800 text-sm leading-snug">{{ $booking->service->name }}</p>
                            <div class="flex items-center gap-1.5 text-xs text-stone-500">
                                <span>💇‍♀️</span>
                                <span>{{ $booking->stylist->name ?? 'Stylist' }}</span>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-stone-100 my-3"></div>

                        {{-- Customer info --}}
                        <div class="flex items-center justify-between text-[11px] text-stone-500 mb-4 select-none">
                            <span class="font-semibold text-stone-700 truncate max-w-[130px]">{{ $booking->customer_name }}</span>
                            <span class="text-stone-400 truncate max-w-[100px]">{{ $booking->customer_phone }}</span>
                        </div>

                        {{-- Action Buttons --}}
                        @if(!in_array($status, ['completed', 'cancelled']))
                            <div class="flex items-center gap-1.5 mt-3 pt-2 border-t border-stone-50">
                                {{-- Confirm button --}}
                                @if($status === 'pending')
                                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="flex-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-1.5 rounded-lg text-[10px] font-bold tracking-wider uppercase transition-colors shadow-sm shadow-blue-500/10">
                                            Confirm
                                        </button>
                                    </form>
                                @endif

                                {{-- Complete button --}}
                                @if($status === 'confirmed')
                                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="flex-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-1.5 rounded-lg text-[10px] font-bold tracking-wider uppercase transition-colors shadow-sm shadow-emerald-500/10">
                                            Complete
                                        </button>
                                    </form>
                                @endif

                                {{-- Cancel button (Delete) --}}
                                <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" onsubmit="return confirm('Yakin batalkan booking ini?')" class="flex-1">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 py-1.5 rounded-lg text-[10px] font-bold tracking-wider uppercase transition-colors">
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Visual indicators for completed/cancelled --}}
                            <div class="flex items-center justify-center py-1 rounded bg-stone-50 select-none">
                                <span class="text-[9px] font-extrabold uppercase tracking-widest text-stone-400">
                                    {{ $status === 'completed' ? '🏁 Selesai' : '❌ Dibatalkan' }}
                                </span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10 border border-dashed border-stone-200/80 rounded-xl bg-stone-100/[0.03] select-none">
                        <span class="text-stone-300 text-xs">Kolom Kosong</span>
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach

</div>
@endsection
