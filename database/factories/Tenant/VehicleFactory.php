<?php

namespace Database\Factories\Tenant;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model =  \App\Models\Tenant\Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->unique()->uuid(),
            'plate_number' => $this->faker->unique()->vehicleRegistration('[A-Z]{2}-[0-9]{5}'),
            'brand' => $this->faker->unique()->vehicleArray()['brand'],
            'model' => $this->faker->unique()->vehicleArray()['model'],
            'color' => $this->faker->unique()->colorName(),
        ];
    }
}
