<?php

namespace Database\Factories;

use App\Models\Region;
use App\Models\Shipment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $region = Region::query()->inRandomOrder()->first() ?? Region::factory()->create();
        $student = Student::query()->inRandomOrder()->first() ?? Student::factory()->create();
        $admin = User::query()->where('role', User::ROLE_IKSASS_ADMIN)->inRandomOrder()->first();

        return [
            'tracking_code' => Shipment::generateTrackingCode($region),
            'student_id' => $student->id,
            'region_iksass_id' => $region->id,
            'sender_name' => fake()->name(),
            'sender_phone' => fake()->phoneNumber(),
            'location_type' => $student->location_type,
            'amount_money' => fake()->randomElement([0, 100000, 250000, 500000, 750000, 1000000]),
            'package_count' => fake()->numberBetween(1, 4),
            'status' => fake()->randomElement(array_keys(Shipment::STATUSES)),
            'created_by' => $admin ? $admin->id : 1,
        ];
    }
}
