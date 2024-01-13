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
        $start_date_time = Carbon::now()->subDays($this->faker->randomNumber(2));

        $duration = ($type == 'internal') ? 1 : $this->faker->numberBetween(1, 8);
        $duration_type = ($type == 'internal') ? 'hours' : 'days';
        return [
            'type' => $type,
            'duration_type' => $duration_type,
            'duration' => $duration,
            'companion_no' => $this->faker->numberBetween(0, 9),
            'date_time' => $start_date_time,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'end_date' => $start_date_time->add(unit: $duration_type, value: $duration),

            'resident_id' => $resident->id,
            'relative_id' => $this->faker->randomElement($relatives_id),
        ];
    }
}
