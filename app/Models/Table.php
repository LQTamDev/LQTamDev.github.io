<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $primaryKey = 'table_id';

    protected $guarded = [];

    public function waiter()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id', 'table_id');
    }
}
