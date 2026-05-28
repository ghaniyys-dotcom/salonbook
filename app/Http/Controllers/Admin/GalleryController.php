<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $items = GalleryItem::with(['service', 'stylist'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.gallery.index', compact('items'));
    }

    public function create(): View
    {
        $services = Service::active()->orderBy('name')->get();
        $stylists = Stylist::where('is_active', true)->orderBy('name')->get();

        return view('admin.gallery.form', [
            'item' => null,
            'services' => $services,
            'stylists' => $stylists,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:before_after,portfolio,testimonial'],
            'service_id' => ['nullable', 'exists:services,id'],
            'stylist_id' => ['nullable', 'exists:stylists,id'],
            'before_image' => ['nullable', 'image', 'max:5120'],
            'after_image' => ['nullable', 'image', 'max:5120'],
            'image' => ['nullable', 'image', 'max:5120'],
            'client_name' => ['nullable', 'string', 'max:120'],
            'review_text' => ['nullable', 'string', 'max:1000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $data = collect($validated)->except(['before_image', 'after_image', 'image'])->toArray();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('before_image')) {
            $data['before_image_path'] = $request->file('before_image')->store('gallery', 'public');
        }
        if ($request->hasFile('after_image')) {
            $data['after_image_path'] = $request->file('after_image')->store('gallery', 'public');
        }
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('gallery', 'public');
        }

        GalleryItem::create($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery item berhasil ditambahkan.');
    }

    public function edit(GalleryItem $gallery): View
    {
        $services = Service::active()->orderBy('name')->get();
        $stylists = Stylist::where('is_active', true)->orderBy('name')->get();

        return view('admin.gallery.form', [
            'item' => $gallery,
            'services' => $services,
            'stylists' => $stylists,
        ]);
    }

    public function update(Request $request, GalleryItem $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:before_after,portfolio,testimonial'],
            'service_id' => ['nullable', 'exists:services,id'],
            'stylist_id' => ['nullable', 'exists:stylists,id'],
            'before_image' => ['nullable', 'image', 'max:5120'],
            'after_image' => ['nullable', 'image', 'max:5120'],
            'image' => ['nullable', 'image', 'max:5120'],
            'client_name' => ['nullable', 'string', 'max:120'],
            'review_text' => ['nullable', 'string', 'max:1000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $data = collect($validated)->except(['before_image', 'after_image', 'image'])->toArray();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('before_image')) {
            if ($gallery->before_image_path) {
                Storage::disk('public')->delete($gallery->before_image_path);
            }
            $data['before_image_path'] = $request->file('before_image')->store('gallery', 'public');
        }
        if ($request->hasFile('after_image')) {
            if ($gallery->after_image_path) {
                Storage::disk('public')->delete($gallery->after_image_path);
            }
            $data['after_image_path'] = $request->file('after_image')->store('gallery', 'public');
        }
        if ($request->hasFile('image')) {
            if ($gallery->image_path) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            $data['image_path'] = $request->file('image')->store('gallery', 'public');
        }

        $gallery->update($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery item berhasil diperbarui.');
    }

    public function destroy(GalleryItem $gallery): RedirectResponse
    {
        // Clean up files
        foreach (['before_image_path', 'after_image_path', 'image_path'] as $field) {
            if ($gallery->$field) {
                Storage::disk('public')->delete($gallery->$field);
            }
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery item berhasil dihapus.');
    }
}
