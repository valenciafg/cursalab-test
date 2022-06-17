<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserCourseSubjectsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    private function createView(): string
    {
        return <<<SQL

            CREATE VIEW user_course_subject_view AS
            select
                u.email,
                su.user_id,
                su.subject_id,
                s.course_id,
                su.score,
                su.attempts,
                su.updated_at
            from subject_user su
            inner join users u
            on u.id = su.user_id
            inner join subjects s
            on s.id = su.subject_id
            inner join courses c
            on c.id = s.course_id
            order by
                su.user_id asc,
                s.course_id asc,
                su.updated_at asc
            SQL;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    private function dropView(): string
    {
        return <<<SQL
            DROP VIEW IF EXISTS `user_course_subject_view`;
            SQL;
    }
}
