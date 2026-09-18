<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis' => (string) fake()->unique()->numerify('##########'),
            'name' => fake()->name(),
            'dormitory' => fake()->randomElement(['Asrama Al-Ikhlas', 'Asrama Al-Furqon', 'Asrama An-Nur', 'Asrama Ar-Rohmah', 'Asrama Al-Hikmah']),
            'location_type' => fake()->randomElement(['Pusat Putra', 'Pusat Putri', "Ma'had Aly", 'Nurul Jadid/Qodliyah', 'Al-Khuzaimah', 'Az-Zainiyah']),
        ];
    }
}
