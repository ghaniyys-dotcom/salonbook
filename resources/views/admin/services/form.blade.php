@extends('layouts.admin')

@section('heading', $service->exists ? 'Edit Layanan' : 'Tambah Layanan')

@section('content')
<form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}" class="max-w-lg bg-white rounded-xl border p-6 space-y-4">
    @csrf
    @if($service->exists) @method('PUT') @endif
    <div>
        <label class="text-sm font-medium">Nama</label>
        <input name="name" value="{{ old('name', $service->name) }}" required class="w-full rounded-lg border-stone-300 mt-1" style="color: #1c1917 !important; background-color: #ffffff !important;">
    </div>
    <div>
        <label class="text-sm font-medium">Deskripsi</label>
        <textarea name="description" rows="3" class="w-full rounded-lg border-stone-300 mt-1" style="color: #1c1917 !important; background-color: #ffffff !important;">{{ old('description', $service->description) }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-medium">Harga (Rp)</label>
            <input type="number" name="price" value="{{ old('price', $service->price) }}" required class="w-full rounded-lg border-stone-300 mt-1" style="color: #1c1917 !important; background-color: #ffffff !important;">
        </div>
        <div>
            <label class="text-sm font-medium">Durasi (menit)</label>
            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes ?: 60) }}" required class="w-full rounded-lg border-stone-300 mt-1" style="color: #1c1917 !important; background-color: #ffffff !important;">
        </div>
    </div>
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active ?? true))>
        <span class="text-sm">Aktif</span>
    </label>
    <button class="bg-violet-600 text-white px-6 py-2 rounded-lg">Simpan</button>
</form>
@endsection
