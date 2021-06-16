<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeWeeklyTimeSheet extends Model
{
    use HasFactory;
    protected $table = 'employee_weekly_timesheet';
    protected $guarded = [];

    protected $casts = [
        'date' => 'date:Y-m-d l',
        'time_from' => 'date:h:i:s A',
        'time_to' => 'date:h:i:s A',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s A');
    }
    public function getPayRateAttribute($value)
    {
        return json_decode($value);
    }
    public function setPayRateAttribute($value)
    {
        $this->attributes['pay_rate'] = json_encode($value);
    }
}
