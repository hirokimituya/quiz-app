<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        Quiz::factory()
            ->count(3)
            ->for($user)
            ->for($genre)
            ->create();
    }
}
