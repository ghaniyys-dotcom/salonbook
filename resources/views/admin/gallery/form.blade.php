@extends('layouts.admin')

@section('heading', $item ? 'Edit Gallery Item' : 'Tambah Gallery Item')

@section('content')
<div class="max-w-2xl">
    <form method="POST" 
          action="{{ $item ? route('admin.gallery.update', $item) : route('admin.gallery.store') }}" 
          enctype="multipart/form-data" 
          class="space-y-6"
          x-data="{ type: '{{ old('type', $item?->type ?? 'portfolio') }}' }">
        @csrf
        @if($item) @method('PUT') @endif

        {{-- Type Selector --}}
        <div>
            <label class="block text-sm font-bold text-stone-700 mb-2">Tipe Item</label>
            <div class="grid grid-cols-3 gap-3">
                <button type="button" @click="type = 'before_after'" 
                    :class="type === 'before_after' ? 'border-violet-500 bg-violet-50 text-violet-700' : 'border-stone-200 text-stone-600 hover:bg-stone-50'"
                    class="border-2 rounded-xl p-3 text-center text-xs font-bold transition-all">
                    📸 Before/After
                </button>
                <button type="button" @click="type = 'portfolio'"
                    :class="type === 'portfolio' ? 'border-violet-500 bg-violet-50 text-violet-700' : 'border-stone-200 text-stone-600 hover:bg-stone-50'"
                    class="border-2 rounded-xl p-3 text-center text-xs font-bold transition-all">
                    🖼️ Portfolio
                </button>
                <button type="button" @click="type = 'testimonial'"
                    :class="type === 'testimonial' ? 'border-violet-500 bg-violet-50 text-violet-700' : 'border-stone-200 text-stone-600 hover:bg-stone-50'"
                    class="border-2 rounded-xl p-3 text-center text-xs font-bold transition-all">
                    💬 Testimonial
                </button>
            </div>
            <input type="hidden" name="type" :value="type">
        </div>

        {{-- Before/After Images --}}
        <div x-show="type === 'before_after'" class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-stone-700 mb-1">Foto Before</label>
                <input type="file" name="before_image" accept="image/*" class="w-full text-sm text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-violet-50 file:text-violet-600 hover:file:bg-violet-100">
                @if($item?->before_image_path)
                    <img src="{{ Storage::url($item->before_image_path) }}" class="mt-2 w-full h-24 object-cover rounded-xl">
                @endif
            </div>
            <div>
                <label class="block text-sm font-bold text-stone-700 mb-1">Foto After</label>
                <input type="file" name="after_image" accept="image/*" class="w-full text-sm text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-violet-50 file:text-violet-600 hover:file:bg-violet-100">
                @if($item?->after_image_path)
                    <img src="{{ Storage::url($item->after_image_path) }}" class="mt-2 w-full h-24 object-cover rounded-xl">
                @endif
            </div>
        </div>

        {{-- Portfolio Image --}}
        <div x-show="type === 'portfolio'">
            <label class="block text-sm font-bold text-stone-700 mb-1">Foto Portfolio</label>
            <input type="file" name="image" accept="image/*" class="w-full text-sm text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-violet-50 file:text-violet-600 hover:file:bg-violet-100">
            @if($item?->image_path)
                <img src="{{ Storage::url($item->image_path) }}" class="mt-2 w-full h-32 object-cover rounded-xl">
            @endif
        </div>

        {{-- Testimonial fields --}}
        <div x-show="type === 'testimonial'" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-stone-700 mb-1">Teks Review</label>
                <textarea name="review_text" rows="4" class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all">{{ old('review_text', $item?->review_text) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-stone-700 mb-1">Rating (1-5)</label>
                <select name="rating" class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all">
                    <option value="">— Pilih —</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ old('rating', $item?->rating) == $i ? 'selected' : '' }}>{{ str_repeat('★', $i) . str_repeat('☆', 5 - $i) }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Common fields --}}
        <div>
            <label class="block text-sm font-bold text-stone-700 mb-1">Nama Klien (opsional)</label>
            <input type="text" name="client_name" value="{{ old('client_name', $item?->client_name) }}" 
                class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all" placeholder="Nama pelanggan...">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-stone-700 mb-1">Layanan (opsional)</label>
                <select name="service_id" class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all">
                    <option value="">— Pilih —</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id', $item?->service_id) == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-stone-700 mb-1">Stylist (opsional)</label>
                <select name="stylist_id" class="w-full border border-stone-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all">
                    <option value="">— Pilih —</option>
                    @foreach($stylists as $stylist)
                        <option value="{{ $stylist->id }}" {{ old('stylist_id', $item?->stylist_id) == $stylist->id ? 'selected' : '' }}>{{ $stylist->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $item?->is_featured) ? 'checked' : '' }}
                    class="w-4 h-4 text-violet-600 border-stone-300 rounded focus:ring-violet-500">
                <span class="text-sm font-semibold text-stone-700">Tampilkan di Landing Page (Featured)</span>
            </label>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex gap-3 pt-4 border-t border-stone-200">
            <button type="submit" class="bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-violet-500/10 hover:shadow-violet-500/20 transition-all">
                {{ $item ? 'Simpan Perubahan' : 'Tambah Item' }}
            </button>
            <a href="{{ route('admin.gallery.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-stone-500 hover:text-stone-700 hover:bg-stone-100 transition-all">Batal</a>
        </div>
    </form>
</div>
@endsection
