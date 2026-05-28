<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StylistController extends Controller
{
    public function index(): View
    {
        $stylists = Stylist::withCount('bookings')->orderBy('name')->paginate(10);

        return view('admin.stylists.index', compact('stylists'));
    }

    public function create(): View
    {
        return view('admin.stylists.form', [
            'stylist' => new Stylist,
            'services' => Service::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $stylist = Stylist::create($data);
        $stylist->services()->sync($request->input('service_ids', []));

        return redirect()->route('admin.stylists.index')->with('success', 'Stylist ditambahkan.');
    }

    public function edit(Stylist $stylist): View
    {
        return view('admin.stylists.form', [
            'stylist' => $stylist,
            'services' => Service::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Stylist $stylist): RedirectResponse
    {
        $data = $this->validated($request);
        $stylist->update($data);
        $stylist->services()->sync($request->input('service_ids', []));

        return redirect()->route('admin.stylists.index')->with('success', 'Stylist diperbarui.');
    }

    public function destroy(Stylist $stylist): RedirectResponse
    {
        $stylist->delete();

        return redirect()->route('admin.stylists.index')->with('success', 'Stylist dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['exists:services,id'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
