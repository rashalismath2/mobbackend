<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\Master;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'master_id' => Master::factory(),
            'groupName' =>Str::random(10).$this->faker->randomDigit(),
            'description' => $this->faker->realText($maxNbChars = 100, $indexSize = 2)
        ];
    }
}
