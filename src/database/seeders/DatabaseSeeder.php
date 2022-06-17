<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(50)->create();
        Category::factory(50)->create();
        Course::factory(30)->create();
        Subject::factory(30)->create();
    }
}
