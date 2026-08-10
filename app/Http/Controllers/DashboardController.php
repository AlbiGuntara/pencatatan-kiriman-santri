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
        $totalMoney = (int) Shipment::query()->sum('amount_money');

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
                'total_money' => $totalMoney,
                'total_students' => $isSuperAdmin ? Student::query()->count() : null,
            ],
            'regionStats' => $regionStats,
            'recentShipments' => $recentShipments,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }
}
