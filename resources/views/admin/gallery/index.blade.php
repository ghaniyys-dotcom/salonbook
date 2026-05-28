@extends('layouts.admin')

@section('heading', 'Gallery & Testimonials')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <p class="text-slate-500 text-sm">Kelola foto before/after, portfolio, dan testimonial pelanggan untuk landing page.</p>
    <a href="{{ route('admin.gallery.create') }}" class="bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-violet-500/10 hover:shadow-violet-500/20 hover:scale-[1.01] transition-all text-center shrink-0">
        + Tambah Item
    </a>
</div>

{{-- Filter Tabs --}}
<div class="flex gap-2 mb-6 overflow-x-auto no-scrollbar" x-data="{ tab: 'all' }">
    <button @click="tab = 'all'" :class="tab === 'all' ? 'bg-violet-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all border border-slate-200 whitespace-nowrap">Semua</button>
    <button @click="tab = 'before_after'" :class="tab === 'before_after' ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-600 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all border border-slate-200 whitespace-nowrap">Before/After</button>
    <button @click="tab = 'portfolio'" :class="tab === 'portfolio' ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-600 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all border border-slate-200 whitespace-nowrap">Portfolio</button>
    <button @click="tab = 'testimonial'" :class="tab === 'testimonial' ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-600 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all border border-slate-200 whitespace-nowrap">Testimonial</button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($items as $item)
        <div class="bg-white rounded-3xl border border-stone-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative group">
            {{-- Image Section --}}
            @if($item->type === 'before_after')
                <div class="grid grid-cols-2 h-40">
                    @if($item->before_image_path)
                        <div class="relative overflow-hidden">
                            <img src="{{ Storage::url($item->before_image_path) }}" alt="Before" class="w-full h-full object-cover">
                            <span class="absolute bottom-2 left-2 bg-black/60 text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Before</span>
                        </div>
                    @else
                        <div class="bg-stone-100 flex items-center justify-center text-stone-400 text-xs">No image</div>
                    @endif
                    @if($item->after_image_path)
                        <div class="relative overflow-hidden">
                            <img src="{{ Storage::url($item->after_image_path) }}" alt="After" class="w-full h-full object-cover">
                            <span class="absolute bottom-2 right-2 bg-violet-600/80 text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">After</span>
                        </div>
                    @else
                        <div class="bg-stone-100 flex items-center justify-center text-stone-400 text-xs">No image</div>
                    @endif
                </div>
            @elseif($item->type === 'portfolio' && $item->image_path)
                <div class="h-40 overflow-hidden">
                    <img src="{{ Storage::url($item->image_path) }}" alt="Portfolio" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
            @elseif($item->type === 'testimonial')
                <div class="h-40 bg-gradient-to-br from-violet-50 to-fuchsia-50 flex items-center justify-center p-6">
                    <p class="text-stone-600 text-sm italic line-clamp-4 leading-relaxed">"{{ $item->review_text }}"</p>
                </div>
            @endif

            {{-- Content --}}
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[9px] font-bold uppercase tracking-widest px-2.5 py-0.5 rounded-full 
                        {{ $item->type === 'before_after' ? 'bg-amber-50 text-amber-600 border border-amber-200' : '' }}
                        {{ $item->type === 'portfolio' ? 'bg-blue-50 text-blue-600 border border-blue-200' : '' }}
                        {{ $item->type === 'testimonial' ? 'bg-violet-50 text-violet-600 border border-violet-200' : '' }}
                    ">{{ str_replace('_', '/', ucfirst($item->type)) }}</span>

                    @if($item->is_featured)
                        <span class="text-[9px] font-bold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">★ Featured</span>
                    @endif
                </div>

                @if($item->client_name)
                    <p class="font-bold text-stone-800 text-sm">{{ $item->client_name }}</p>
                @endif
                @if($item->service)
                    <p class="text-stone-500 text-xs mt-1">{{ $item->service->name }}</p>
                @endif
                @if($item->rating)
                    <p class="text-amber-400 text-xs mt-1">{{ $item->ratingStars() }}</p>
                @endif

                <div class="flex items-center gap-2 mt-4 pt-3 border-t border-stone-100">
                    <a href="{{ route('admin.gallery.edit', $item) }}" class="text-xs font-bold text-violet-600 hover:text-violet-800 transition-colors">Edit</a>
                    <form method="POST" action="{{ route('admin.gallery.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus item ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-16 text-stone-400">
            <p class="text-lg mb-2">Belum ada item gallery</p>
            <p class="text-sm">Tambahkan foto before/after, portfolio, atau testimonial pelanggan.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $items->links() }}
</div>
@endsection
