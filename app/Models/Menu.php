<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $primaryKey = 'food_id';

    protected $guarded = [];

    public function stock()
    {
        return $this->hasOne(Inventory::class, 'menu_id', 'food_id');
    }
}
