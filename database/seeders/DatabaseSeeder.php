<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Shipment;
use App\Models\ShipmentLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RegionSeeder::class);

        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'superadmin@sipenri.test'],
            [
                'name' => 'Super Admin Pusat',
                'password' => bcrypt('password'),
                'role' => User::ROLE_SUPER_ADMIN,
                'region_iksass_id' => null,
                'email_verified_at' => now(),
            ],
        );

        $admins = [];
        foreach (Region::all() as $region) {
            $email = strtolower($region->code).'@sipenri.test';
            $admins[] = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Admin '.$region->name,
                    'password' => bcrypt('password'),
                    'role' => User::ROLE_IKSASS_ADMIN,
                    'region_iksass_id' => $region->id,
                    'email_verified_at' => now(),
                ],
            );
        }

        if (Student::query()->count() === 0) {
            Student::factory()->count(120)->create();
        }

        if (Shipment::query()->count() === 0) {
            $shipments = Shipment::factory()->count(40)->create();

            foreach ($shipments as $shipment) {
                $logger = collect($admins)->first(fn (User $user) => $user->region_iksass_id === $shipment->region_iksass_id)
                    ?? $superAdmin;

                ShipmentLog::query()->create([
                    'shipment_id' => $shipment->id,
                    'status_from' => null,
                    'status_to' => 'created',
                    'notes' => 'Kiriman dicatat dan stiker dicetak.',
                    'logged_by' => $logger->id,
                    'created_at' => $shipment->created_at,
                ]);

                if (in_array($shipment->status, [Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_DELIVERED], true)) {
                    ShipmentLog::query()->create([
                        'shipment_id' => $shipment->id,
                        'status_from' => 'created',
                        'status_to' => 'in_transit',
                        'notes' => 'Paket diberangkatkan dari daerah asal menuju Pondok Pesantren Sukorejo.',
                        'logged_by' => $logger->id,
                        'created_at' => $shipment->created_at->copy()->addDay(),
                    ]);
                }

                if ($shipment->status === Shipment::STATUS_DELIVERED) {
                    ShipmentLog::query()->create([
                        'shipment_id' => $shipment->id,
                        'status_from' => 'in_transit',
                        'status_to' => 'delivered',
                        'notes' => 'Paket telah tiba di pondok dan langsung diserahkan kepada santri.',
                        'logged_by' => $logger->id,
                        'created_at' => $shipment->created_at->copy()->addDays(2),
                    ]);
                }
            }
        }
    }
}
