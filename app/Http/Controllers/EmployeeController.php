<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return inertia('Employee/Index', [
            'employees' => Employee::all()->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'fname' => $emp->fname,
                    'lname' => $emp->lname,
                    'name' => $emp->user->name,
                    'email' => $emp->user->email,
                    'wage' => $emp->wage,
                    'ssn' => $emp->ssn,
                    'user_id' => $emp->user_id,
                    'role' => implode(',', $emp->user->getRoleNames()->toArray()),
                    'role_id' => $emp->user->roles->first()->id
                ];
            })
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'fname' => ['required', 'string', 'max:50'],
            'lname' => ['required', 'string', 'max:50'],
            'wage' => ['required', 'float'],
            'ssn' => ['required', 'string', 'unique:employees,ssn'],
            'user_id' => ['required', 'integer', 'unique:employees,user_id'],
            'password' => ['required', 'string']
        ])->validate();

        Employee::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'wage' => $request->wage,
            'ssn' => $request->ssn,
            'user_id' => $request->user_id,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('employees.index')->with('message', 'Employee Created Successfully.');
    }

    public function show($user_id)
    {
        $employee = Employee::with('timesheet')->where('user_id', $user_id)->first();
        return inertia('Employee/TimeSheet', [
            'employee' => $employee,
        ]);
    }
    public function showPaycheck(Request $request, $user_id)
    {
        $employee = Employee::with('timesheet')->where('user_id', $user_id)->first();
        return inertia('Employee/PayCheck', [
            'employee' => $employee
        ]);
    }
    public function paycheck(Request $request, Employee $employee)
    {
        $totalWorked = floatval($request->totalWorked);
        $pay = number_format($employee->wage * $totalWorked, 2);
        $employee->info()->create(['hours' => $totalWorked, 'pay' => $pay]);
        return redirect()->back()->with('message', 'Paycheck successfully.');
    }
}
