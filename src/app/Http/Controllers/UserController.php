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

    public function userRanking($top)
    {
        $list = UserCourseSubject::all()->toArray();
        $users = array_column($list, 'email');
        // Obtiene lista de usuarios unicos
        $users = array_values(array_unique($users));
        if ($top < 1 || $top > sizeof($users)) {
            return response(["error" => 'Invalid top value'], 400);
        }
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
            $approvedTotal = 0;
            $completedTotal = 0;
            $lastDayFromAllCourses = new DateTime();
            $totalAttempts = 0;
            foreach($courses as $c) {
                $subjects = [];
                $isCompleted = true;
                $isApproved = true;
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
                        if ($date < $lastDayFromAllCourses) {
                            $lastDayFromAllCourses = $date;
                        }
                        $subjects[] = $s;
                    }
                }
                if ($isApproved === true) {
                    $approvedTotal += 1;
                }
                if ($isCompleted === true) {
                    $completedTotal += 1;
                }
            }
            $coursesSize = sizeof($courses);
            $coursesByUser[] = [
                "email" => $user,
                "coursesTotal" => $coursesSize,
                'approvedTotal' => $approvedTotal,
                'lastDay' => $lastDayFromAllCourses->format('Y-m-d H:i:s'),
                'completedAverage' => $completedTotal / $coursesSize,
                'totalAttempts' => $totalAttempts
            ];
        }
        //  Ordenamientos por:
        //  -   Total de cursos aprobados
        //  -   Promedio de cursos completados
        //  -   Fecha de ultimo curso aprobado
        //  -   Total de intentos
        $approvedTotalList = array_column($coursesByUser, 'approvedTotal');
        $completedAverageList = array_column($coursesByUser, 'completedAverage');
        $lastDayList = array_column($coursesByUser, 'lastDay');
        $totalAttemptsList = array_column($coursesByUser, 'totalAttempts');
        array_multisort(
            $approvedTotalList, SORT_DESC,
            $completedAverageList, SORT_DESC,
            $lastDayList, SORT_DESC,
            $totalAttemptsList, SORT_ASC,
            $coursesByUser
        );
        $topList = array_slice($coursesByUser, 0, $top);
        return response($topList);
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
        $existCourse = User::whereRelation('courses', 'course_id', '=', $course_id)->find($id);
        if (!$existCourse) {
            return response(["error" => 'the user is not registered to this course'], 400);
        }
        $existSubject = User::whereRelation('subjects', 'subject_id', '=', $subject_id)->find($id);
        if (!$existSubject) {
            return response(["error" => 'the user is not registered to this subject'], 400);
        }
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
