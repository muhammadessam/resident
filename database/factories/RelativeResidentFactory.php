<?php

namespace Database\Factories;

use App\Models\RelativeResident;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RelativeResidentFactory extends Factory
{
    protected $model = RelativeResident::class;

    public function definition(): array
    {
        return [
            'resident_id' => $this->faker->numberBetween(1,100),
            'relative_id' => $this->faker->numberBetween(1,100),
            'relation' => $this->faker->randomKey(RelativeResident::RELATION),
            'is_guardian' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
