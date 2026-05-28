<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Stylist;
use App\Services\PredictiveAnalyticsService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(PredictiveAnalyticsService $analytics): View
    {
        $today = Carbon::today(config('app.timezone'));
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $stats = [
            'pending' => Booking::where('status', 'pending')->count(),
            'today' => Booking::whereDate('scheduled_at', $today)
                ->whereNotIn('status', ['cancelled'])
                ->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'total_bookings' => Booking::whereNotIn('status', ['cancelled'])->count(),
            'week_bookings' => Booking::whereBetween('scheduled_at', [$weekStart, $weekEnd])
                ->whereNotIn('status', ['cancelled'])
                ->count(),
            'total_services' => Service::count(),
            'total_stylists' => Stylist::where('is_active', true)->count(),
        ];

        // Revenue this week (sum of service prices for completed/confirmed bookings)
        $weekRevenue = Booking::whereBetween('scheduled_at', [$weekStart, $weekEnd])
            ->whereIn('status', ['confirmed', 'completed'])
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->sum('services.price');

        $stats['week_revenue'] = $weekRevenue;

        // Daily bookings and revenue for the week (for charts)
        $dailyBookings = [];
        $dailyRevenue = [];
        $dailyData = Booking::selectRaw('DATE(scheduled_at) as date, COUNT(*) as count')
            ->whereBetween('scheduled_at', [$weekStart, $weekEnd])
            ->whereNotIn('status', ['cancelled'])
            ->groupByRaw('DATE(scheduled_at)')
            ->pluck('count', 'date');

        $dailyRevenueData = Booking::whereBetween('scheduled_at', [$weekStart, $weekEnd])
            ->whereIn('status', ['confirmed', 'completed'])
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->selectRaw('DATE(scheduled_at) as date, SUM(services.price) as revenue')
            ->groupByRaw('DATE(scheduled_at)')
            ->pluck('revenue', 'date');

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $count = (int) ($dailyData[$dateStr] ?? 0);
            $rev = (int) ($dailyRevenueData[$dateStr] ?? 0);

            $dailyBookings[] = [
                'day' => $date->format('D'),
                'date' => $date->format('d M'),
                'count' => $count,
            ];

            $dailyRevenue[] = [
                'day' => $date->format('D'),
                'date' => $date->format('d M'),
                'revenue' => $rev,
                'revenue_k' => round($rev / 1000),
            ];
        }

        // Service revenue breakdown for Donut Chart
        $servicesBreakdown = [];
        $activeServices = Service::all();
        $totalRevenue = max(1, Booking::whereIn('status', ['confirmed', 'completed'])
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->sum('services.price'));

        $colors = ['#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6'];
        foreach ($activeServices as $index => $srv) {
            $rev = (int) Booking::where('service_id', $srv->id)
                ->whereIn('status', ['confirmed', 'completed'])
                ->join('services', 'bookings.service_id', '=', 'services.id')
                ->sum('services.price');
            
            $percent = $totalRevenue > 0 ? round(($rev / $totalRevenue) * 100) : 0;
            $servicesBreakdown[] = [
                'name' => $srv->name,
                'revenue' => $rev,
                'percentage' => $percent,
                'color' => $colors[$index % count($colors)],
            ];
        }

        $upcoming = Booking::with(['service', 'stylist', 'customer'])
            ->where('scheduled_at', '>=', now())
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        foreach ($upcoming as $b) {
            $b->no_show_risk = $analytics->calculateNoShowRisk($b);
        }

        $heatmap = $analytics->getHeatmapData($weekStart);
        $utilization = $analytics->getStylistUtilization($today);
        $forecast = $analytics->forecastRevenue(7);
        $funnel = $analytics->getRetentionFunnel();

        return view('admin.dashboard', compact(
            'stats',
            'upcoming',
            'dailyBookings',
            'dailyRevenue',
            'servicesBreakdown',
            'heatmap',
            'utilization',
            'forecast',
            'funnel'
        ));
    }
}
