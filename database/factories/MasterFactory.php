<?php

namespace Database\Factories;

use App\Models\Master;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MasterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Master::class;

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
            'education' => Str::random(10),
            'profile_img' => "https://via.placeholder.com/150",
            'institute' => $this->faker->company(),
        ];
    }
}
