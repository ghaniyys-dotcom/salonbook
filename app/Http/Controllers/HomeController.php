<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $services = Service::active()->with('stylists')->orderBy('name')->get();
        $stylists = \App\Models\Stylist::where('is_active', true)->get();
        $galleryItems = \App\Models\GalleryItem::with(['service', 'stylist'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('home', compact('services', 'stylists', 'galleryItems'));
    }
}
