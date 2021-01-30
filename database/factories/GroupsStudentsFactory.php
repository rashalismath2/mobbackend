<?php

namespace Database\Factories;

use App\Models\GroupsStudents;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\Group;
use App\Models\User;

class GroupsStudentsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GroupsStudents::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'student_id' => User::factory(),
            'group_student_id' => Str::random(10),
            'allowed' => $this->faker->boolean($chanceOfGettingTrue = 50),
            'user_removed' => $this->faker->boolean($chanceOfGettingTrue = 50),
        ];
    }
}
