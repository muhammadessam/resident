<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ResidentFactory extends Factory
{
    protected $model = Resident::class;

    public function definition(): array
    {
        $type = $this->faker->randomKey(Resident::TYPE);

        return [
            'name' => $this->faker->name(),
            'number' => $this->faker->randomNumber(9),
            'type' => $type,
            'mental_disability_degree' => $this->faker->randomKey(Resident::METAL_DEGREE),
            'dob' => $this->faker->date(max: '2000-01-31'),
            'doe' => $this->faker->date(max: '2020-03-25'),
            'building' => $this->faker->randomKey($type == 'male' ? Resident::MALE_BUILDINGS : Resident::FEMALE_BUILDINGS),
            'ability_to_external_visit' => $this->faker->boolean(),
            'external_visit_authorized' => $this->faker->word(),
            'internal_visit_authorized' => $this->faker->word(),
            'notes' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deletion_reason' => null,
            'city_id' => $this->faker->numberBetween(1, 99),
        ];
    }
}
