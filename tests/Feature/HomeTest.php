<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Quiz;
use App\Models\User;
use App\Models\Genre;
use Inertia\Testing\Assert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeTest extends TestCase
{
	use RefreshDatabase;
	
	public function setUp(): void
	{
		parent::setUp();

		// テストユーザー作成
		$this->user = User::factory()->create();
	}

	public function test_home_page_return_when_there_is_one_quiz()
	{
		// 前準備
		$genre = Genre::factory()->create();
		$quiz = Quiz::factory()
			->has(Item::factory())
			->has(Item::factory()->questionNumber2())
			->for($this->user)
			->for($genre)
			->create();

		// リクエスト発行
		$response = $this->get('/');

		// レスポンスデータを検証
		$response->assertStatus(200)
			->assertInertia(fn(Assert $page) => $page
				->component('Home')
				->has('quizes', 1, fn(Assert $page) => $page
					->where('title', $quiz->title)
					->where('description', $quiz->description)
					->etc()
				)
				->where('quizCount', 1)
				->where('currentPage', 1)
				->where('perPage', 10)
				->where('genreListId', 0)
				->where('sortItem', 'new')
			);
	}
	
	public function test_home_page_return_when_there_is_no_quiz() 
	{
		// リクエスト発行
		$response = $this->get('/');

		$response->assertStatus(200)
			->assertInertia(fn(Assert $page) => $page
				->component('Home')
				->has('quizes', 0)
				->where('quizCount', 0)
				->where('currentPage', 1)
				->where('perPage', 10)
				->where('genreListId', 0)
				->where('sortItem', 'new')
		);
	}
}
