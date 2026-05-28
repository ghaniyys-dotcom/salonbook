@extends('layouts.admin')

@section('heading', 'Kelola Layanan Perawatan')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <p class="text-slate-500 text-sm">Manajemen daftar perawatan kecantikan, durasi, harga investasi, dan ketersediaan live.</p>
    <a href="{{ route('admin.services.create') }}" class="bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-violet-500/10 hover:shadow-violet-500/20 hover:scale-[1.01] transition-all text-center shrink-0">
        + Tambah Layanan
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($services as $service)
        <div class="bg-white rounded-3xl border border-stone-200 p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            {{-- Edit link floating top right --}}
            <a href="{{ route('admin.services.edit', $service) }}" class="absolute top-4 right-4 text-xs font-bold text-stone-400 hover:text-violet-600 transition-colors">Edit</a>
            
            <div>
                {{-- Header: Service Name --}}
                <div class="pr-6">
                    <span class="inline-block text-[9px] font-extrabold text-violet-600 bg-violet-50 border border-violet-100 rounded-full px-2.5 py-0.5 uppercase tracking-widest">{{ $service->duration_minutes }} MENIT TREATMENT</span>
                    <h3 class="font-bold text-stone-800 text-lg leading-snug mt-2">{{ $service->name }}</h3>
                </div>

                {{-- Description --}}
                <p class="text-stone-500 text-xs mt-3 line-clamp-2 leading-relaxed">
                    {{ $service->description ?? 'Deskripsi detail untuk layanan perawatan rambut premium dan relaksasi khusus.' }}
                </p>
            </div>

            <div>
                {{-- Divider --}}
                <div class="border-t border-stone-100 my-4"></div>

                {{-- Bottom Row: Price & Active Toggle --}}
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <span class="text-[9px] font-extrabold text-stone-400 uppercase tracking-widest block">Harga Layanan</span>
                        <span class="text-base font-extrabold text-violet-600">{{ $service->formattedPrice() }}</span>
                    </div>
                    
                    {{-- Inline Toggle Form --}}
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">{{ $service->is_active ? 'Aktif' : 'Non-aktif' }}</span>
                        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $service->name }}">
                            <input type="hidden" name="duration_minutes" value="{{ $service->duration_minutes }}">
                            <input type="hidden" name="price" value="{{ $service->price }}">
                            <input type="hidden" name="description" value="{{ $service->description }}">
                            <input type="hidden" name="is_active" value="{{ $service->is_active ? 0 : 1 }}">
                            <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-all duration-300 focus:outline-none cursor-pointer {{ $service->is_active ? 'bg-violet-600 shadow-sm shadow-violet-500/20' : 'bg-stone-200' }}">
                                <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-all duration-300 {{ $service->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-6">
    {{ $services->links() }}
</div>
@endsection
