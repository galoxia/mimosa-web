<?php

namespace Database\Factories;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WorkshopFactory extends Factory
{
	protected $model = Workshop::class;

	public function definition(): array
	{
		return [
			'name' => $this->faker->word(),
			'description' => $this->faker->text(),
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
		];
	}
}
