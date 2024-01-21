<?php

namespace Database\Factories;

use App\Models\Category;
use Barryvdh\LaravelIdeHelper\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(rand(1, 3), true);
        $slug = Str::of($name)->slug();

        return compact('name', 'slug');
    }

    public function withParent(): Factory
    {
        return $this->state(fn() => ['parent_id' => Category::all()->random()?->id]);
    }
}
