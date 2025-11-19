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
            'min_teacher_number' => $this->faker->numberBetween( 1, 10000 ),
            'max_teacher_number' => $this->faker->numberBetween( 15000, 30000 ),
		];
	}
}
