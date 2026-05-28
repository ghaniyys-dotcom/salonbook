@extends('layouts.admin')

@section('heading', 'Kelola Stylist Ahli')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <p class="text-slate-500 text-sm">Manajemen stylist ahli bersertifikat dan status ketersediaan live mereka.</p>
    <a href="{{ route('admin.stylists.create') }}" class="bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-violet-500/10 hover:shadow-violet-500/20 hover:scale-[1.01] transition-all text-center shrink-0">
        + Tambah Stylist
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($stylists as $stylist)
        <div class="bg-white rounded-3xl border border-stone-200 p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            {{-- Edit link floating top right --}}
            <a href="{{ route('admin.stylists.edit', $stylist) }}" class="absolute top-4 right-4 text-xs font-bold text-stone-400 hover:text-violet-600 transition-colors">Edit</a>
            
            <div>
                {{-- Top Row: Avatar & Specialty --}}
                <div class="flex items-start gap-4">
                    {{-- Luxury Initial Badge --}}
                    <div class="rounded-2xl bg-gradient-to-br from-violet-600/10 to-fuchsia-600/10 border border-violet-500/20 flex items-center justify-center shrink-0 text-violet-600 font-extrabold text-lg tracking-wider" style="width: 56px; height: 56px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                        @php
                            $words = explode(' ', $stylist->name);
                            $initials = '';
                            foreach($words as $w) {
                                $initials .= strtoupper(substr($w, 0, 1));
                            }
                            echo substr($initials, 0, 2);
                        @endphp
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-stone-800 text-base leading-snug truncate pr-6">{{ $stylist->name }}</h3>
                        <span class="inline-block text-xs font-semibold text-violet-600 bg-violet-50 border border-violet-100 rounded-full px-2.5 py-0.5 mt-1">{{ $stylist->specialty ?? 'Stylist' }}</span>
                    </div>
                </div>

                {{-- Bio --}}
                <p class="text-stone-500 text-xs mt-4 line-clamp-2 italic leading-relaxed">
                    "{{ $stylist->bio ?? 'Stylist profesional spesialis perawatan rambut & pewarnaan.' }}"
                </p>
            </div>

            <div>
                {{-- Divider --}}
                <div class="border-t border-stone-100 my-4"></div>

                {{-- Bottom Row: Bookings Stats & Active Toggle --}}
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <span class="text-[9px] font-extrabold text-stone-400 uppercase tracking-widest block">Total Reservasi</span>
                        <span class="text-sm font-extrabold text-stone-700">{{ $stylist->bookings_count }} Sesi</span>
                    </div>
                    
                    {{-- Inline Toggle Form --}}
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">{{ $stylist->is_active ? 'Aktif' : 'Non-aktif' }}</span>
                        <form method="POST" action="{{ route('admin.stylists.update', $stylist) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $stylist->name }}">
                            <input type="hidden" name="specialty" value="{{ $stylist->specialty }}">
                            <input type="hidden" name="bio" value="{{ $stylist->bio }}">
                            <input type="hidden" name="is_active" value="{{ $stylist->is_active ? 0 : 1 }}">
                            <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-all duration-300 focus:outline-none cursor-pointer {{ $stylist->is_active ? 'bg-violet-600 shadow-sm shadow-violet-500/20' : 'bg-stone-200' }}">
                                <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-all duration-300 {{ $stylist->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-6">
    {{ $stylists->links() }}
</div>
@endsection
