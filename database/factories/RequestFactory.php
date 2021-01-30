<?php

namespace Database\Factories;

use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Group;

class RequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Request::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'student_id' => User::factory(),
            'group_id' =>  Group::factory(),
            'accepted' => $this->faker->boolean($chanceOfGettingTrue = 50),
            'declined' => $this->faker->boolean($chanceOfGettingTrue = 50),
        ];
    }
}
