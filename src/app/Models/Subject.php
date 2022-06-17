<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'content', 'course_id'];
    public function course() {
        $this->belongsTo('App\Models\Course');
    }

    public function users() {
        $this->belongsToMany('App\Models\User');
    }
}
