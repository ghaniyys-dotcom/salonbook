@extends('layouts.admin')

@section('heading', 'Dashboard')

@section('content')
<style>
    /* Purge-Immune Robust CSS Grid & Flex Layouts for Admin Dashboard */
    .stats-grid-container {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .analytics-grid-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .col-revenue { width: 100%; }
    .col-trend { width: 100%; }
    .col-donut { width: 100%; }
    
    @media (min-width: 1024px) {
        .stats-grid-container {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.5rem;
        }
        .analytics-grid-container {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1.5rem;
        }
        .col-revenue { grid-column: span 4 / span 4; }
        .col-trend { grid-column: span 5 / span 5; }
        .col-donut { grid-column: span 3 / span 3; }
    }
</style>

{{-- Stats Row --}}
<div class="stats-grid-container animate-fade-in">
    <div class="bg-white rounded-2xl p-5 border border-stone-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3" style="display: flex; align-items: center; justify-content: space-between;">
            <div class="bg-amber-100 rounded-xl flex items-center justify-center" style="width: 36px; height: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[9px] text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">Pending</span>
        </div>
        <p class="text-3xl font-extrabold text-amber-600">{{ $stats['pending'] }}</p>
        <p class="text-stone-400 text-xs font-semibold mt-0.5">Menunggu Konfirmasi</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-stone-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3" style="display: flex; align-items: center; justify-content: space-between;">
            <div class="bg-violet-100 rounded-xl flex items-center justify-center" style="width: 36px; height: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-[9px] text-violet-700 font-bold bg-violet-50 px-2 py-0.5 rounded-full border border-violet-200">Today</span>
        </div>
        <p class="text-3xl font-extrabold text-violet-600">{{ $stats['today'] }}</p>
        <p class="text-stone-400 text-xs font-semibold mt-0.5">Jadwal Hari Ini</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-stone-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3" style="display: flex; align-items: center; justify-content: space-between;">
            <div class="bg-blue-100 rounded-xl flex items-center justify-center" style="width: 36px; height: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[9px] text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">Active</span>
        </div>
        <p class="text-3xl font-extrabold text-blue-600">{{ $stats['confirmed'] }}</p>
        <p class="text-stone-400 text-xs font-semibold mt-0.5">Telah Disetujui</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-stone-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3" style="display: flex; align-items: center; justify-content: space-between;">
            <div class="bg-emerald-100 rounded-xl flex items-center justify-center" style="width: 36px; height: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span class="text-[9px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Done</span>
        </div>
        <p class="text-3xl font-extrabold text-emerald-600">{{ $stats['completed'] }}</p>
        <p class="text-stone-400 text-xs font-semibold mt-0.5">Selesai Dirawat</p>
    </div>
</div>

{{-- Analytics Row --}}
<div class="analytics-grid-container mb-6">
    
    {{-- Weekly Revenue Hero Card --}}
    <div class="col-revenue bg-gradient-to-br from-slate-900 via-slate-900 to-violet-950 rounded-3xl p-6 text-white shadow-xl flex flex-col justify-between min-h-[220px]" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 220px;">
        <div>
            <span class="text-violet-400 text-[9px] font-extrabold uppercase tracking-widest bg-violet-500/10 px-3 py-1 rounded-full border border-violet-500/20">RINGKASAN KEUANGAN</span>
            <p class="text-slate-400 text-xs mt-6">Pendapatan Minggu Ini</p>
            <p class="text-4xl font-extrabold mt-2 tracking-tight">Rp {{ number_format($stats['week_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="flex items-center justify-between border-t border-white/5 pt-4 mt-6 text-slate-400 text-xs font-semibold" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500" style="width: 6px; height: 6px; border-radius: 9999px; background-color: #10b981; display: inline-block; margin-right: 4px;"></span> {{ $stats['week_bookings'] }} Sesi Treatment</span>
            <span>Est. Omzet Klien</span>
        </div>
    </div>

    {{-- Glowing SVG Area Chart (Daily Revenue Trend) --}}
    <div class="col-trend bg-white rounded-3xl p-6 border border-stone-200 shadow-sm flex flex-col justify-between" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="flex justify-between items-center mb-3" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <div>
                    <span class="text-[9px] font-extrabold text-stone-400 uppercase tracking-widest">TREN KEUANGAN</span>
                    <h3 class="font-bold text-stone-800 text-sm mt-0.5">Pendapatan Harian Minggu Ini</h3>
                </div>
                <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2.5 py-0.5 border border-emerald-200 rounded-full font-bold">Live Update</span>
            </div>
            
            {{-- Pure SVG Area Chart --}}
            <div class="relative w-full flex items-end" style="position: relative; width: 100%; height: 120px; display: flex; align-items: flex-end;">
                @php
                    $revenues = array_column($dailyRevenue, 'revenue');
                    $maxRevenue = max(!empty($revenues) ? max($revenues) : 0, 1);
                    $points = [];
                    foreach ($dailyRevenue as $index => $day) {
                        $x = 15 + ($index * 42);
                        $y = 110 - (($day['revenue'] / $maxRevenue) * 90); // 120px scale
                        $points[] = "$x,$y";
                    }
                    $pointsString = implode(' ', $points);
                    $polygonPoints = "15,110 " . $pointsString . " 267,110";
                @endphp
                <svg viewBox="0 0 282 120" class="w-full h-full" style="width: 100%; height: 100%;">
                    <defs>
                        <linearGradient id="area-gradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.3"></stop>
                            <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.0"></stop>
                        </linearGradient>
                    </defs>
                    <line x1="15" y1="10" x2="267" y2="10" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3"></line>
                    <line x1="15" y1="60" x2="267" y2="60" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="3"></line>
                    <line x1="15" y1="110" x2="267" y2="110" stroke="#cbd5e1" stroke-width="1.5"></line>
                    <polygon points="{{ $polygonPoints }}" fill="url(#area-gradient)"></polygon>
                    <polyline points="{{ $pointsString }}" fill="none" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="drop-shadow-[0_3px_6px_rgba(139,92,246,0.3)]"></polyline>
                    @foreach($points as $idx => $pt)
                        @php list($px, $py) = explode(',', $pt); @endphp
                        <circle cx="{{ $px }}" cy="{{ $py }}" r="4" fill="#8b5cf6" stroke="#ffffff" stroke-width="1.5" class="transition-all hover:r-6"></circle>
                    @endforeach
                </svg>
            </div>
        </div>

        {{-- Chart Labels --}}
        <div class="flex justify-between items-center text-[10px] text-stone-400 font-bold px-1 border-t border-stone-100 pt-3 mt-4 select-none" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 0.75rem; margin-top: 1rem;">
            @foreach($dailyRevenue as $day)
                <span class="w-10 text-center tracking-tight">{{ $day['day'] }}</span>
            @endforeach
        </div>
    </div>

    {{-- Glowing SVG Donut Chart (Service Share) --}}
    <div class="col-donut bg-white rounded-3xl p-6 border border-stone-200 shadow-sm flex flex-col justify-between" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <span class="text-[9px] font-extrabold text-stone-400 uppercase tracking-widest">KONTRIBUSI LAYANAN</span>
            <h3 class="font-bold text-stone-800 text-sm mt-0.5 mb-5">Distribusi Omzet Perawatan</h3>
            
            <div class="flex items-center gap-6" style="display: flex; align-items: center; gap: 1.5rem;">
                {{-- Donut SVG --}}
                <div class="shrink-0 relative flex items-center justify-center" style="width: 96px; height: 96px; flex-shrink: 0; position: relative;">
                    <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90" style="width: 100%; height: 100%;">
                        <circle cx="18" cy="18" r="15.91549430918954" fill="transparent" stroke="#f1f5f9" stroke-width="3.5"></circle>
                        @php
                            $accumulated = 0;
                        @endphp
                        @foreach($servicesBreakdown as $srv)
                            @if($srv['percentage'] > 0)
                                <circle cx="18" cy="18" r="15.91549430918954" fill="transparent" 
                                    stroke="{{ $srv['color'] }}" stroke-width="3.5"
                                    stroke-dasharray="{{ $srv['percentage'] }} {{ 100 - $srv['percentage'] }}"
                                    stroke-dashoffset="{{ 100 - $accumulated + 25 }}"
                                    class="transition-all duration-300 hover:stroke-[4.5]"
                                    title="{{ $srv['name'] }}">
                                </circle>
                                @php
                                    $accumulated += $srv['percentage'];
                                @endphp
                            @endif
                        @endforeach
                    </svg>
                    {{-- Donut center label --}}
                    <div class="absolute flex flex-col items-center justify-center select-none text-center" style="position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <span class="text-stone-300 text-[8px] uppercase tracking-wider font-extrabold">Total</span>
                        <span class="text-stone-800 text-xs font-black mt-0.5">Rp {{ round($stats['week_revenue'] / 1000) }}K</span>
                    </div>
                </div>

                {{-- Legend list --}}
                <div class="flex-1 space-y-2 select-none" style="flex: 1 1 0%; display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($servicesBreakdown as $srv)
                        <div class="flex items-center justify-between text-xs gap-2" style="display: flex; align-items: center; justify-content: space-between; font-size: 12px;">
                            <div class="flex items-center gap-1.5 truncate max-w-[100px]" style="display: flex; align-items: center; gap: 6px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100px;">
                                <span class="w-2 h-2 rounded-full shrink-0" style="width: 8px; height: 8px; border-radius: 9999px; flex-shrink: 0; background-color: {{ $srv['color'] }}; display: inline-block;"></span>
                                <span class="text-stone-600 font-semibold truncate">{{ $srv['name'] }}</span>
                            </div>
                            <span class="text-stone-400 font-bold shrink-0">{{ $srv['percentage'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="text-[9px] text-stone-400 text-center border-t border-stone-100 pt-3 mt-4 select-none" style="text-align: center; border-top: 1px solid #f1f5f9; padding-top: 0.75rem; margin-top: 1rem; font-size: 9px;">
            Breakdown omzet berdasarkan reservasi sukses hari ini.
        </div>
    </div>
</div>

{{-- Predictive Insights & Load heatmaps --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
    {{-- Heatmap --}}
    <div class="lg:col-span-8 bg-white rounded-3xl p-6 border border-stone-200 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-4">
                <div>
                    <span class="text-[9px] font-extrabold text-stone-400 uppercase tracking-widest">SALON PULSE</span>
                    <h3 class="font-bold text-stone-800 text-sm mt-0.5">Heatmap Kepadatan Jadwal</h3>
                </div>
                <div class="flex gap-2 text-[9px] font-semibold text-stone-400">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded bg-stone-100"></span> Empty</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded bg-violet-100"></span> Low</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded bg-violet-300"></span> Mid</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded bg-violet-600"></span> Peak</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-[10px] text-center border-collapse">
                    <thead>
                        <tr>
                            <th class="p-1.5 text-stone-400 font-bold text-left w-12">Time</th>
                            @foreach($heatmap as $day)
                                <th class="p-1.5 text-stone-500 font-bold w-16">
                                    {{ $day['day'] }}
                                    <span class="block text-[8px] text-stone-400 font-normal mt-0.5">{{ $day['date'] }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(range(9, 19) as $hourIdx => $hour)
                            <tr class="border-t border-stone-50">
                                <td class="p-1.5 text-stone-400 font-semibold text-left font-mono">{{ sprintf('%02d:00', $hour) }}</td>
                                @foreach($heatmap as $day)
                                    @php
                                        $slot = $day['slots'][$hourIdx];
                                        $bg = 'bg-stone-50 text-stone-300';
                                        if ($slot['intensity'] === 'low') $bg = 'bg-violet-100 text-violet-700';
                                        elseif ($slot['intensity'] === 'medium') $bg = 'bg-violet-300 text-violet-950 font-bold';
                                        elseif ($slot['intensity'] === 'high') $bg = 'bg-violet-600 text-white font-extrabold shadow-sm';
                                    @endphp
                                    <td class="p-1">
                                        <div class="h-6 rounded-lg flex items-center justify-center transition-all duration-300 hover:scale-105 {{ $bg }}"
                                             title="{{ $day['day'] }} {{ $slot['hour'] }}: {{ $slot['count'] }} booking">
                                            {{ $slot['count'] > 0 ? $slot['count'] : '' }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Stylist load and retention funnel --}}
    <div class="lg:col-span-4 space-y-6">
        {{-- Stylist Utilization --}}
        <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm">
            <span class="text-[9px] font-extrabold text-stone-400 uppercase tracking-widest">UTILISASI STYLIST HARI INI</span>
            <h3 class="font-bold text-stone-800 text-sm mt-0.5 mb-4">Okupansi & Beban Kerja</h3>

            <div class="space-y-4">
                @foreach($utilization as $stylist)
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center text-xs">
                            <div>
                                <span class="font-bold text-stone-800">{{ $stylist['name'] }}</span>
                                <span class="text-[9px] text-stone-400 block mt-0.5">{{ $stylist['specialty'] }}</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                {{ $stylist['status'] === 'busy' ? 'bg-rose-50 text-rose-700' : '' }}
                                {{ $stylist['status'] === 'moderate' ? 'bg-amber-50 text-amber-700' : '' }}
                                {{ $stylist['status'] === 'idle' ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                {{ $stylist['utilization'] }}%
                            </span>
                        </div>
                        <div class="w-full bg-stone-100 h-1.5 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500
                                {{ $stylist['status'] === 'busy' ? 'bg-rose-500' : '' }}
                                {{ $stylist['status'] === 'moderate' ? 'bg-amber-500' : '' }}
                                {{ $stylist['status'] === 'idle' ? 'bg-emerald-500' : '' }}"
                                 style="width: {{ $stylist['utilization'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Retention Funnel --}}
        <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm">
            <span class="text-[9px] font-extrabold text-stone-400 uppercase tracking-widest">RETENSI PELANGGAN</span>
            <h3 class="font-bold text-stone-800 text-sm mt-0.5 mb-4">Loyalty & Churn Funnel</h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-stone-50 rounded-2xl text-center">
                    <span class="text-[9px] text-stone-400 font-bold uppercase tracking-wider block">Total Customer</span>
                    <span class="text-2xl font-black text-stone-800 block mt-1">{{ $funnel['total'] }}</span>
                </div>
                <div class="p-3 bg-violet-50 rounded-2xl text-center">
                    <span class="text-[9px] text-violet-400 font-bold uppercase tracking-wider block">Baru Bulan Ini</span>
                    <span class="text-2xl font-black text-violet-700 block mt-1">+{{ $funnel['new_this_month'] }}</span>
                </div>
            </div>

            <div class="mt-4 space-y-2.5">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-stone-500 font-semibold flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block mr-1"></span> Loyal (5+ visits)</span>
                    <span class="font-bold text-stone-800">{{ $funnel['loyal'] }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-stone-500 font-semibold flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block mr-1"></span> Active (last 30d)</span>
                    <span class="font-bold text-stone-800">{{ $funnel['active'] }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-stone-500 font-semibold flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block mr-1"></span> At Churn Risk</span>
                    <span class="font-bold text-stone-800">{{ $funnel['at_risk'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Upcoming Bookings --}}
<div class="bg-white rounded-3xl border border-stone-200 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between">
        <h2 class="font-bold text-stone-800">Antrian Reservasi Mendatang</h2>
        <a href="{{ route('admin.bookings.index') }}" class="text-violet-600 text-sm font-semibold hover:underline">Lihat Semua Antrian →</a>
    </div>

    @if($upcoming->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-stone-50 text-left select-none">
                    <tr>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[9px] uppercase tracking-widest">Ref</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[9px] uppercase tracking-widest">Pelanggan</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[9px] uppercase tracking-widest">Layanan Perawatan</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[9px] uppercase tracking-widest">Stylist Ahli</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[9px] uppercase tracking-widest">Waktu Datang</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[9px] uppercase tracking-widest">Status</th>
                        <th class="px-6 py-3 text-stone-400 font-semibold text-[9px] uppercase tracking-widest text-right">Kelola Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcoming as $booking)
                        <tr class="border-t border-stone-100 hover:bg-violet-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-violet-600 hover:underline font-mono text-xs font-bold">
                                    {{ $booking->reference }}
                                </a>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5" style="display: flex; align-items: center; gap: 6px;">
                                    <span class="font-bold text-stone-800">{{ $booking->customer_name }}</span>
                                    <a href="{{ $booking->whatsappUrl() }}" target="_blank" title="Hubungi via WhatsApp" class="text-emerald-500 hover:text-emerald-600 hover:scale-110 transition-all flex items-center" style="display: inline-flex; align-items: center; color: #10b981; transition: all 0.2s;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.733-1.455L0 24zm6.09-3.977c1.597.947 3.197 1.488 4.793 1.488 5.485.003 9.946-4.462 9.949-9.95.002-2.66-1.023-5.158-2.887-7.026C16.14 2.668 13.633 1.64 10.96 1.64 5.48 1.64 1.02 6.101 1.018 11.59c-.002 1.677.447 3.323 1.303 4.766L1.314 20.9l4.833-1.266-.002.002zM17.5 14.5c-.3-.15-1.75-.85-2.05-.95-.3-.1-.5-.15-.7.15-.2.3-.75.95-.95 1.2-.15.2-.35.25-.65.1-1.5-.75-2.65-1.35-3.65-3.1-.25-.45.25-.4.7-.13.4.25.9.4.9.75-.15.4-.55.75-.75.9-.2.15-.45.2-.75.1-.3-.1-1.2-.45-2.3-1.4-1.1-.95-1.5-1.95-1.7-2.25-.15-.3-.02-.45.13-.6.15-.15.3-.35.45-.5.15-.15.2-.25.3-.45.1-.2.05-.4-.02-.55-.08-.15-.7-1.7-.95-2.3-.25-.6-.55-.5-.7-.5h-.7c-.25 0-.6.1-.9.45-1.2 1.2-1.2 3.1 0 4.8 1.4 1.95 3.3 2.95 5.2 3.75.9.35 1.8.35 2.5.25.8-.1 1.75-.75 2-1.45.25-.7.25-1.3.15-1.45-.1-.15-.3-.25-.6-.4z"/></svg>
                                    </a>
                                </div>
                                @if(isset($booking->no_show_risk))
                                    <div class="mt-1 flex items-center">
                                        <span class="text-[8px] px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wider
                                            {{ $booking->no_show_risk >= 70 ? 'bg-rose-50 text-rose-600 border border-rose-100' : '' }}
                                            {{ $booking->no_show_risk >= 30 && $booking->no_show_risk < 70 ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                            {{ $booking->no_show_risk < 30 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}">
                                            Risk: {{ $booking->no_show_risk }}%
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-stone-600 font-medium">{{ $booking->service->name }}</td>
                            <td class="px-6 py-4 text-stone-600 font-medium">{{ $booking->stylist->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-stone-500 font-semibold">
                                {{ $booking->scheduled_at->timezone(config('app.timezone'))->format('d M, H:i') }} WIB
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider
                                    {{ $booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $booking->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $booking->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $booking->status === 'pending' ? 'bg-amber-500' : ($booking->status === 'confirmed' ? 'bg-blue-500' : ($booking->status === 'completed' ? 'bg-emerald-500' : 'bg-red-500')) }}"></span>
                                    {{ $booking->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-1.5 mt-0.5">
                                @if($booking->status === 'pending')
                                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" title="Konfirmasi Reservasi" class="w-7 h-7 rounded-lg bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center transition-all shadow-sm shadow-blue-500/10 font-bold text-xs">✓</button>
                                    </form>
                                @endif
                                @if($booking->status === 'confirmed')
                                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" title="Selesaikan Reservasi" class="w-7 h-7 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center transition-all shadow-sm shadow-emerald-500/10 font-bold text-xs">✨</button>
                                    </form>
                                @endif
                                @if($booking->status !== 'completed' && $booking->status !== 'cancelled')
                                    <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" class="inline" onsubmit="return confirm('Yakin batalkan booking ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Batalkan Reservasi" class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center transition-all font-bold text-xs">✕</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="px-6 py-12 text-center select-none">
            <p class="text-stone-400 text-sm">Tidak ada antri jadwal mendatang.</p>
        </div>
    @endif
</div>
@endsection
