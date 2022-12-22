<?php

namespace Database\Factories\Tenant;

use App\Enums\Models\AccessStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Access>
 */
class AccessFactory extends Factory
{
    protected $model = \App\Models\Tenant\Access::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->unique()->uuid(),
            'url' => $this->faker->url(),
            'expiry_period' => 30,
            'validity_period' => 2,
            'issued_at' => now(),
        ];
    }

    // /**
    //  * Indicate that the Access is inactive.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Factories\Factory
    //  */
    // public function inactive()
    // {
    //     return $this->state(function (array $attributes) {
    //         return [
    //             'status' => AccessStatus::INACTIVE->value,
    //         ];
    //     });
    // }

    // /**
    //  * Indicate that the Access is active.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Factories\Factory
    //  */
    // public function active()
    // {
    //     return $this->state(function (array $attributes) {
    //         return [
    //             'status' => AccessStatus::ACTIVE->value,
    //         ];
    //     });
    // }

    // /**
    //  * Indicate that the Access has been issued.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Factories\Factory
    //  */
    // public function issued()
    // {
    //     return $this->state(function (array $attributes) {
    //         return [
    //             'status' => AccessStatus::ISSUED->value,
    //         ];
    //     });
    // }
}
