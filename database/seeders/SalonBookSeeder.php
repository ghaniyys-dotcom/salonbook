<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Stylist;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SalonBookSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Glow',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Staff Salon',
            'email' => 'staff@demo.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        $services = [
            ['name' => 'Haircut Premium', 'description' => 'Potong, keramas, styling.', 'duration_minutes' => 60, 'price' => 150000],
            ['name' => 'Hair Color', 'description' => 'Full color dengan treatment.', 'duration_minutes' => 120, 'price' => 450000],
            ['name' => 'Manicure Gel', 'description' => 'Manicure gel polish tahan lama.', 'duration_minutes' => 45, 'price' => 120000],
            ['name' => 'Hair Spa', 'description' => 'Perawatan scalp dan rambut.', 'duration_minutes' => 90, 'price' => 250000],
        ];

        $createdServices = [];
        foreach ($services as $svc) {
            $createdServices[] = Service::create([
                ...$svc,
                'slug' => \Illuminate\Support\Str::slug($svc['name']),
                'is_active' => true,
            ]);
        }

        $stylists = [
            ['name' => 'Dewi Kartika', 'specialty' => 'Hair & Color', 'bio' => '5 tahun pengalaman salon premium.'],
            ['name' => 'Rizky Pratama', 'specialty' => 'Men Grooming', 'bio' => 'Spesialis potong rambut pria.'],
            ['name' => 'Sinta Maharani', 'specialty' => 'Nail Art', 'bio' => 'Ahli manicure dan nail art.'],
        ];

        $createdStylists = [];
        foreach ($stylists as $st) {
            $createdStylists[] = Stylist::create([...$st, 'is_active' => true]);
        }

        foreach ($createdStylists as $stylist) {
            $stylist->services()->attach(collect($createdServices)->pluck('id'));
        }

        $tomorrow = Carbon::tomorrow(config('app.timezone'))->setTime(10, 0);

        Booking::create([
            'service_id' => $createdServices[0]->id,
            'stylist_id' => $createdStylists[0]->id,
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'scheduled_at' => $tomorrow,
            'ends_at' => $tomorrow->copy()->addMinutes(60),
            'status' => 'pending',
        ]);

        $confirmed = $tomorrow->copy()->addHours(2);
        Booking::create([
            'service_id' => $createdServices[2]->id,
            'stylist_id' => $createdStylists[2]->id,
            'customer_name' => 'Anisa Putri',
            'customer_email' => 'anisa@example.com',
            'customer_phone' => '081298765432',
            'scheduled_at' => $confirmed,
            'ends_at' => $confirmed->copy()->addMinutes(45),
            'status' => 'confirmed',
        ]);
    }
}
