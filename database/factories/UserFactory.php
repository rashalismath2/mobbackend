<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'firstName' => $this->faker->name,
            'lastName' => $this->faker->name,
            'gender' => "male",
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt("password"), // password
            'school' => $this->faker->company(),
            'grade' => $this->faker->randomDigit(),
            'profile_img' => "https://via.placeholder.com/150",
        ];
    }
}
