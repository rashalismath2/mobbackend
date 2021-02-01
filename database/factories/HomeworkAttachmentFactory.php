<?php

namespace Database\Factories;

use App\Models\HomeworkAttachments;
use Illuminate\Database\Eloquent\Factories\Factory;

class HomeworkAttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HomeworkAttachments::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "file_path"=>"",
            "file_type"=>"pdf",
        ];
    }
}
