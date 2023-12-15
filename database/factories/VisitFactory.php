<?php

namespace Database\Factories;

use App\Models\Relative;
use App\Models\Resident;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VisitFactory extends Factory
{
    protected $model = Visit::class;

    public function definition(): array
    {
        $type = $this->faker->randomKey(Visit::TYPE);
        $resident = Resident::whereHas('relatives')->inRandomOrder()->first();
        $relatives_id = $resident->relatives->pluck('id')->toArray();
        return [
            'type' => $type,
            'duration_type' => $type == 'internal' ? 'hours' : $this->faker->randomKey(Visit::DURATION_TYPE),
            'duration' => $type == 'internal' ? 1 : $this->faker->numberBetween(1, 8),
            'companion_no' => $this->faker->numberBetween(0, 9),
            'date_time' => $this->faker->dateTime(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'resident_id' => $resident->id,
            'relative_id' => $this->faker->randomElement($relatives_id),

            'created_by' => 1,
        ];
    }
}
