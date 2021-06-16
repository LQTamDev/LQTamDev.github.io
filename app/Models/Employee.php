<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['password'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function info()
    {
        return $this->hasOne(EmployeeInfo::class);
    }

    public function tableAssigned()
    {
        return $this->hasMany(Table::class);
    }
    public function timesheet()
    {
        return $this->hasMany(EmployeeWeeklyTimeSheet::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->fname} {$this->lname}";
    }
}
