@extends('layouts.admin')

@section('heading', 'Bookings')

@section('content')
{{-- Filter — minimal bar --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, ref, WA..." class="rounded-xl border-stone-200 text-sm px-4 py-2.5 bg-white max-w-[200px]" style="background-color: #ffffff; color: #1c1917;">
    <select name="status" class="rounded-xl border-stone-200 text-sm px-4 py-2.5 bg-white">
        <option value="">Semua status</option>
        @foreach(\App\Models\Booking::STATUSES as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border-stone-200 text-sm px-4 py-2.5 bg-white">
    <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border-stone-200 text-sm px-4 py-2.5 bg-white">
    <button class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-colors cursor-pointer">Filter</button>
    <a href="{{ route('admin.reports.export', request()->query()) }}" class="ml-auto flex items-center gap-1.5 text-violet-600 hover:text-violet-800 px-4 py-2.5 rounded-xl text-sm font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export CSV
    </a>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
    @if($bookings->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-stone-50/80 text-left">
                    <tr>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[10px] uppercase tracking-widest">Ref</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[10px] uppercase tracking-widest">Customer</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[10px] uppercase tracking-widest">Layanan</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[10px] uppercase tracking-widest">Jadwal</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[10px] uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        <tr class="border-t border-stone-100 hover:bg-violet-50/20 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
                            <td class="px-6 py-4"><span class="font-mono text-xs font-bold text-violet-600">{{ $booking->reference }}</span></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5" style="display: flex; align-items: center; gap: 6px;">
                                    <span class="font-bold text-stone-800">{{ $booking->customer_name }}</span>
                                    <a href="{{ $booking->whatsappUrl() }}" target="_blank" onclick="event.stopPropagation();" title="Hubungi via WhatsApp" class="text-emerald-500 hover:text-emerald-600 hover:scale-110 transition-all flex items-center" style="display: inline-flex; align-items: center; color: #10b981; transition: all 0.2s;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.733-1.455L0 24zm6.09-3.977c1.597.947 3.197 1.488 4.793 1.488 5.485.003 9.946-4.462 9.949-9.95.002-2.66-1.023-5.158-2.887-7.026C16.14 2.668 13.633 1.64 10.96 1.64 5.48 1.64 1.02 6.101 1.018 11.59c-.002 1.677.447 3.323 1.303 4.766L1.314 20.9l4.833-1.266-.002.002zM17.5 14.5c-.3-.15-1.75-.85-2.05-.95-.3-.1-.5-.15-.7.15-.2.3-.75.95-.95 1.2-.15.2-.35.25-.65.1-1.5-.75-2.65-1.35-3.65-3.1-.25-.45.25-.4.7-.13.4.25.9.4.9.75-.15.4-.55.75-.75.9-.2.15-.45.2-.75.1-.3-.1-1.2-.45-2.3-1.4-1.1-.95-1.5-1.95-1.7-2.25-.15-.3-.02-.45.13-.6.15-.15.3-.35.45-.5.15-.15.2-.25.3-.45.1-.2.05-.4-.02-.55-.08-.15-.7-1.7-.95-2.3-.25-.6-.55-.5-.7-.5h-.7c-.25 0-.6.1-.9.45-1.2 1.2-1.2 3.1 0 4.8 1.4 1.95 3.3 2.95 5.2 3.75.9.35 1.8.35 2.5.25.8-.1 1.75-.75 2-1.45.25-.7.25-1.3.15-1.45-.1-.15-.3-.25-.6-.4z"/></svg>
                                    </a>
                                </div>
                                <p class="text-stone-400 text-xs mt-0.5">{{ $booking->customer_phone }}</p>
                            </td>
                            <td class="px-6 py-4 text-stone-600">{{ $booking->service->name }}</td>
                            <td class="px-6 py-4 text-stone-500 text-xs">{{ $booking->scheduled_at->format('d M Y · H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $booking->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $booking->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $booking->status === 'pending' ? 'bg-amber-500 animate-pulse' : ($booking->status === 'confirmed' ? 'bg-blue-500' : ($booking->status === 'completed' ? 'bg-emerald-500' : 'bg-red-500')) }}"></span>
                                    {{ $booking->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="px-6 py-16 text-center">
            <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-stone-500 font-medium">Tidak ada booking ditemukan</p>
            <p class="text-stone-400 text-sm mt-1">Coba ubah filter atau tunggu booking baru.</p>
        </div>
    @endif
</div>

@if($bookings->isNotEmpty())
    <div class="mt-4">{{ $bookings->appends(request()->query())->links() }}</div>
@endif
@endsection
