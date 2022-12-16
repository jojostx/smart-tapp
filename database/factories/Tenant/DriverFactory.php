<?php

namespace Database\Factories\Tenant;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Driver>
 */
class DriverFactory extends Factory
{
    protected $model = \App\Models\Tenant\Driver::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->unique()->uuid(),
            'name' => $this->faker->name(),
            'phone_number' => $this->faker->unique()->phoneNumber(),
            'phone_number_e164' => $this->faker->unique()->e164PhoneNumber(),
            'phone_verified_at' => now(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
        ];
    }
}
