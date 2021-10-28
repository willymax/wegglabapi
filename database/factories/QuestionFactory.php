<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence(5);
        return [
            //
            'title' => $title,
            'body' => $this->faker->paragraph(5),
            'slug' => Str::slug($title),
            'views' => $this->faker->randomDigit(),
            'image' => $this->faker->imageUrl(640, 480, 'solution', true),
            'active' => $this->faker->boolean(50),
            'featured' => $this->faker->boolean(50),
            'user_id' => User::all()->random()->id,
            'subject_id' => Subject::all()->random()->id,
        ];
    }
}
