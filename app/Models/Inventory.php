<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $primaryKey = 'food_id';
    protected $guarded = [];
    public function food()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'food_id');
    }
}
