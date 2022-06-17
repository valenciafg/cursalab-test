<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
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
        $faker = Faker::create();

        User::factory(100)->create();
        Category::factory(50)->create();
        Course::factory(30)->create();
        Subject::factory(300)->create();

        $courses = Course::all();
        User::all()->each(function ($user) use ($courses, $faker) {
            $coursesToSave = $courses->random(rand(1, 5))->pluck('id')->toArray();
            foreach($coursesToSave as $course_id) {
                $user->courses()->attach($course_id);
                $subjects = Subject::where('course_id', '=', $course_id)->pluck('id')->toArray();
                $isAllApproved = rand(0, 1);
                foreach($subjects as $subject_id) {
                    if ($isAllApproved === 1) {
                        $score = rand(12, 20);
                        $attempts = rand(0,2);
                    } else {
                        $score = rand(0, 20);
                        if ($score < 12) {
                            $attempts = rand(1,3);
                        } else {
                            $attempts = rand(0,2);
                        }
                    }
                    $user->subjects()->attach([
                        $subject_id => [
                            'score' => $score,
                            'attempts' => $attempts,
                            'created_at' => $faker->dateTimeBetween('-5 months', '-2 months'),
                            'updated_at' => $faker->dateTimeBetween('-2 months'),
                        ]
                    ]);
                }
            }
        });
    }
}
