<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Student>
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->firstName();
        $surname1 = $this->faker->lastName();
        $surname2 = $this->faker->optional()->lastName();
        $identification_template = mt_rand( 1, 100 ) > 75 ? '########?' : '?#######?';

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'surname1' => $surname1,
            'surname2' => $surname2,
            'student_number' => $this->faker->unique()->numberBetween( 1, 100 ),
            'identification_number' => $this->faker->unique()->bothify( $identification_template ),
            'phone' => $this->faker->phoneNumber(),
            'alt_phone' => $this->faker->optional()->phoneNumber(),
            'single_marketing_consent' => $this->faker->boolean(),
            'group_marketing_consent' => true,
            'is_delegate' => $this->faker->boolean(10),
        ];
    }
}
