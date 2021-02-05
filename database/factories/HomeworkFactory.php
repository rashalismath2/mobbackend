<?php

namespace Database\Factories;

use App\Models\Homework;
use Illuminate\Database\Eloquent\Factories\Factory;

use Carbon\Carbon;
use Illuminate\Support\Str;

class HomeworkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Homework::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' =>Str::random(10).$this->faker->randomDigit(),
            'note' => $this->faker->realText($maxNbChars = 100, $indexSize = 2),
            'onetime' => $this->faker->boolean($chanceOfGettingTrue = 50),
            'startDate' => Carbon::now(),
            'endDate' =>Carbon::now()->addDays(1),
            'startTime' => Carbon::now()->toDateTimeString(),
            'endTime' => Carbon::now()->toDateTimeString(),
            'status' => "queued",
            'allow_late' =>  $this->faker->boolean($chanceOfGettingTrue = 50),
            'number_of_questions' => $this->faker->randomDigit(),
        ];
    }
}
