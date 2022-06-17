<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
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
