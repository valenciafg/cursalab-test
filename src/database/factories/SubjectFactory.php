<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(255),
            'description' => $this->faker->sentence(5),
            'content' => $this->faker->paragraph(20),
            'course_id' => rand(1, 30)
        ];
    }
}
