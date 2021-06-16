<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json(Role::all());
    }
    public function show(Request $request)
    {
        return response()->json($request->user()->roles()->pluck('name'));
    }
}
