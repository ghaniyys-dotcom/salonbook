@extends('layouts.admin')

@section('heading', $stylist->exists ? 'Edit Stylist' : 'Tambah Stylist')

@section('content')
<form method="POST" action="{{ $stylist->exists ? route('admin.stylists.update', $stylist) : route('admin.stylists.store') }}" class="max-w-lg bg-white rounded-xl border p-6 space-y-4">
    @csrf
    @if($stylist->exists) @method('PUT') @endif
    <div>
        <label class="text-sm font-medium">Nama</label>
        <input name="name" value="{{ old('name', $stylist->name) }}" required class="w-full rounded-lg border-stone-300 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Spesialis</label>
        <input name="specialty" value="{{ old('specialty', $stylist->specialty) }}" class="w-full rounded-lg border-stone-300 mt-1">
    </div>
    <div>
        <label class="text-sm font-medium">Bio</label>
        <textarea name="bio" rows="3" class="w-full rounded-lg border-stone-300 mt-1">{{ old('bio', $stylist->bio) }}</textarea>
    </div>
    <div>
        <label class="text-sm font-medium block mb-2">Layanan yang dikuasai</label>
        @foreach($services as $service)
            <label class="flex items-center gap-2 text-sm mb-1">
                <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                    @checked(in_array($service->id, old('service_ids', $stylist->exists ? $stylist->services->pluck('id')->toArray() : [])))>
                {{ $service->name }}
            </label>
        @endforeach
    </div>
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $stylist->is_active ?? true))>
        <span class="text-sm">Aktif</span>
    </label>
    <button class="bg-violet-600 text-white px-6 py-2 rounded-lg">Simpan</button>
</form>
@endsection
