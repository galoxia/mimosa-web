<?php

namespace Database\Factories;

use App\Models\Degree;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DegreeFactory extends Factory
{
	protected $model = Degree::class;

	public function definition(): array
	{
		return [
			'name' => $this->faker->jobTitle(),
			'abbreviation' => $this->faker->unique()->regexify('[A-Z]{3}'),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),

//			'institution_id' => Institution::factory(),
		];
	}
}
