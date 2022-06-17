<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category_id'];

    public function category() {
        return $this->belongsTo('App\Models\Category');
    }

    public function subjects() {
        return $this->hasMany('App\Models\Subject');
    }

    public function users() {
        $this->belongsToMany('App\Models\User');
    }
}
