<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeWeeklyTimeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeeWeeklyTimeSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return inertia('Payroll/Index', [
            'employees' => Employee::with('timesheet', 'user')->get()->map(function ($emp) {
                return [
                    'id' =>  $emp->id,
                    'name' => $emp->full_name,
                    'wage' => $emp->wage,
                    'user_id' => $emp->user_id,
                    'role' => $emp->user->roles()->pluck('name'),
                    'timesheet' => $emp->timesheet,
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
        $fields = $request->validate([
            'date' => 'required|date',
            'time_from' => 'required',
            'time_to' => 'required',
            'comments' => 'required',
        ]);
        $dateInterval = Carbon::parse($fields['time_from'])->diff(Carbon::parse($fields['time_to']));
        $fields['hours'] = $dateInterval->format('%H:%I');
        $timesheet = Employee::find($request->employee_id)->timesheet()->create($fields);
        // return redirect()->back()->with('data', ['message' => 'Time record created successfully.', 'timesheet' => $timesheet]);
        return redirect()->back()->with('message', 'Time record created successfully.');
        // return redirect()->route('employee.timesheet.index')->with('message', 'Time record created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $employee_id
     * @return \Illuminate\Http\Response
     */
    public function show($employee_id)
    {
        $employee = Employee::with('timesheet')->find($employee_id);
        return response()->json(['employee_timesheets' => $employee]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmployeeWeeklyTimeSheet  $employeeWeeklyTimeSheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeWeeklyTimeSheet $employeeWeeklyTimeSheet)
    {

        $fields = $request->validate([
            'date' => 'required|date',
            'time_from' => 'required|date_format:H:i',
            'time_to' => 'required|date_format:H:i',
            'comments' => 'required',
        ]);
        $dateInterval = Carbon::parse($fields['time_from'])->diff(Carbon::parse($fields['time_to']));
        $fields['hours'] = $dateInterval->format('%H:%I');
        $employeeWeeklyTimeSheet->update($fields);
        return redirect()->back()->with('message', 'Time record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmployeeWeeklyTimeSheet  $employeeWeeklyTimeSheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeWeeklyTimeSheet $employeeWeeklyTimeSheet)
    {
        //
    }
}
