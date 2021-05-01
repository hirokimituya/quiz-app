<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quiz::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
//            'user_id' => User::factory()->create()->id,
//            'genre_id' => Genre::factory()->create()->id,
            'title' => Str::substr($this->faker->text, 0, 100),
            'description' => Str::substr($this->faker->text, 0, 255),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
