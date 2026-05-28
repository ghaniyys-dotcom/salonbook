<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceApiController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::active()
            ->with(['stylists' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->get()
            ->map(fn (Service $s) => $this->transform($s));

        return response()->json(['data' => $services]);
    }

    public function show(Service $service): JsonResponse
    {
        $service->load(['stylists' => fn ($q) => $q->active()]);

        return response()->json(['data' => $this->transform($service)]);
    }

    private function transform(Service $service): array
    {
        return [
            'id' => $service->id,
            'name' => $service->name,
            'slug' => $service->slug,
            'description' => $service->description,
            'duration_minutes' => $service->duration_minutes,
            'price' => $service->price,
            'price_formatted' => $service->formattedPrice(),
            'stylists' => $service->stylists->map(fn ($st) => [
                'id' => $st->id,
                'name' => $st->name,
                'specialty' => $st->specialty,
            ]),
        ];
    }
}
