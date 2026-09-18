<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Shipment;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $totalShipments = Shipment::query()->count();
        $inTransit = Shipment::query()->where('status', Shipment::STATUS_IN_TRANSIT)->count();
        $delivered = Shipment::query()->where('status', Shipment::STATUS_DELIVERED)->count();
        $created = Shipment::query()->where('status', Shipment::STATUS_CREATED)->count();
        $totalMoney = (int) Shipment::query()->sum('amount_money');

        $weeklyStats = $this->weeklyStats();

        $regionStats = [];
        $isSuperAdmin = $user->isSuperAdmin();

        if ($isSuperAdmin) {
            $regionStats = Region::query()
                ->withCount('shipments')
                ->get()
                ->map(fn (Region $region) => [
                    'id' => $region->id,
                    'name' => $region->name,
                    'code' => $region->code,
                    'shipments_count' => $region->shipments_count,
                ]);
        }

        $recentShipments = Shipment::query()
            ->with(['student:id,nis,name,dormitory', 'region:id,name,code'])
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Shipment $shipment) => [
                'id' => $shipment->id,
                'tracking_code' => $shipment->tracking_code,
                'status' => $shipment->status,
                'status_label' => Shipment::STATUSES[$shipment->status] ?? $shipment->status,
                'student_name' => $shipment->student?->name,
                'student_nis' => $shipment->student?->nis,
                'package_count' => $shipment->package_count,
                'amount_money' => (int) $shipment->amount_money,
                'region' => $shipment->region?->name,
                'created_at' => $shipment->created_at?->toISOString(),
            ]);

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_shipments' => $totalShipments,
                'in_transit' => $inTransit,
                'delivered' => $delivered,
                'created' => $created,
                'total_money' => $totalMoney,
                'total_students' => $isSuperAdmin ? Student::query()->count() : null,
            ],
            'weeklyStats' => $weeklyStats,
            'regionStats' => $regionStats,
            'recentShipments' => $recentShipments,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }

    /**
     * Shipment counts grouped by the last seven days (oldest first).
     *
     * @return array<int, array{label: string, short_label: string, count: int, money: int}>
     */
    protected function weeklyStats(): array
    {
        $start = now()->subDays(6)->startOfDay();

        $grouped = Shipment::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count, COALESCE(SUM(amount_money), 0) as money')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day')
            ->mapWithKeys(fn (int $count, string $day) => [$day => (int) $count]);

        $moneyGrouped = Shipment::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COALESCE(SUM(amount_money), 0) as money')
            ->groupBy('day')
            ->pluck('money', 'day')
            ->mapWithKeys(fn (int $money, string $day) => [$day => (int) $money]);

        $days = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $key = $date->toDateString();

            $days[] = [
                'label' => $date->translatedFormat('d M'),
                'short_label' => $date->translatedFormat('D'),
                'count' => $grouped[$key] ?? 0,
                'money' => $moneyGrouped[$key] ?? 0,
            ];
        }

        return $days;
    }
}
