<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
use App\Models\UserCourseSubject;
use DateTime;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('courses:id,name')
            ->with('subjects:id,name,course_id')
            ->get();
        return response($users);
    }

    public function userRanking()
    {
        $list = UserCourseSubject::all()->toArray();
        $users = [];
        foreach($list as $l) {
            $users[] = $l['email'];
        }
        // Obtiene lista de usuarios unicos
        $users = array_values(array_unique($users));
        $coursesByUser = [];
        foreach($users as $user) {
            $courses = [];
            $subjects = [];
            foreach($list as $l) {
                if ($l['email'] === $user) {
                    $courses[] = $l['course_id'];
                    $subjectsByUser[] = [
                        'course_id' => $l['course_id'],
                        'subject_id' => $l['subject_id'],
                        'score' => $l['score'],
                        'attempts' => $l['attempts'],
                        'updated_at' => $l['updated_at'],
                    ];
                }
            }
            // Obtiene lista cursos y temas por usuario
            $courses = array_values(array_unique($courses));
            $coursesWithSubject = [];
            $approvedTotal = 0;
            $completedTotal = 0;
            $lastDayFromAllCourses = new DateTime();
            $totalAttempts = 0;
            foreach($courses as $c) {
                $subjects = [];
                $isCompleted = true;
                $isApproved = true;
                $lastDay = new DateTime();
                foreach($subjectsByUser as $s) {
                    if ($c === $s['course_id']) {
                        $totalAttempts += $s['attempts'];
                        if ($s['score'] === null) {
                            $isCompleted = false;
                        }
                        if ($s['score'] < 12) {
                            $isApproved = false;
                        }
                        $date = new DateTime($s['updated_at']);
                        if ($date < $lastDay) {
                            $lastDay = $date;
                        }
                        $subjects[] = $s;
                    }
                }
                $coursesWithSubject[] = [
                    'course_id' => $c,
                    'isCompleted' => $isCompleted,
                    'isApproved' => $isApproved,
                    'totalAttempts' => $totalAttempts,
                    'lastDay' => $lastDay->format('Y-m-d H:i:s'),
                    'subjects' => $subjects
                ];
                if ($isApproved === true) {
                    $approvedTotal += 1;
                }
                if ($isCompleted === true) {
                    $completedTotal += 1;
                }
                if($lastDayFromAllCourses < $lastDay) {
                    $lastDayFromAllCourses = $lastDay;
                }
            }
            $coursesSize = sizeof($courses);
            $coursesByUser[] = [
                "email" => $user,
                "coursesTotal" => $coursesSize,
                'approvedTotal' => $approvedTotal,
                'lastDayFromAllCourses' => $lastDayFromAllCourses->format('Y-m-d H:i:s'),
                'completedAverage' => $completedTotal / $coursesSize,
                'attemptsAverage' => $totalAttempts / $coursesSize,
                //  "courses" => $coursesWithSubject
            ];
        }
        // return response($list);
        return response($coursesByUser);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCourse($id, $course_id)
    {
        $existCourse = User::whereRelation('courses', 'course_id', '=', $course_id)->find($id);
        if ($existCourse) {
            return response(["error" => 'the user is already registered to this course'], 400);
        }
        $user = User::find($id);
        $user->courses()->attach($course_id);
        $subjects = Subject::where('course_id', '=', $course_id)->get();
        foreach($subjects as $subject) {
            $user->subjects()->attach($subject->id);
        }
        return response("Course ${course_id} registered to user");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSubjectScore($id, $course_id, $subject_id, $score)
    {
        //  return response(["error" => 'invalid subject'], 400);
        //  return response(["error" => 'invalid course'], 400);
        if ($score < 0 || $score > 20 ) {
            return response(["error" => 'invalid score'], 400);
        }
        return response("score ${score} added to user ${id} with course ${course_id} and subject ${subject_id}");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
