<?php

namespace Database\Factories;

use App\Models\Relative;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RelativeFactory extends Factory
{
    protected $model = Relative::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'id_number' => $this->faker->randomNumber(8),
            'phone1' => $this->faker->phoneNumber(),
            'phone2' => $this->faker->phoneNumber(),
            'phone3' => $this->faker->phoneNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
