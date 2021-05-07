<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question_number' => 1,
            'format' => 1,
            'question' => Str::substr($this->faker->text, 0, 255),
            'answer' => Str::substr($this->faker->text, 0, 255),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    public function questionNumber2()
    {
        return $this->state(function(array $attributes) {
            return [
                'question_number' => 2,
            ];
        });
    }
}
