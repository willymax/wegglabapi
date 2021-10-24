<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'body' => $this->faker->paragraph(5),
            'price' => $this->faker->randomDigit(),
            'image' => $this->faker->imageUrl(640, 480, 'solution', true),
            'active' => $this->faker->boolean(50),
            'featured' => $this->faker->boolean(50),
            'question_id' => Question::all()->random()->id,
            'user_id' => User::all()->random()->id,
        ];
    }
}
