<?php

use App\Http\Controllers\EmployeeWeeklyTimeSheetController;
use Illuminate\Support\Facades\Route;

Route::get('employee/timesheet', [EmployeeWeeklyTimeSheetController::class, 'index'])->name('employee.timesheet.index')->middleware('admin');
Route::post('employee/timesheet', [EmployeeWeeklyTimeSheetController::class, 'store'])->name('employee.timesheet.store');
Route::get('employee/timesheet/{employee_id}', [EmployeeWeeklyTimeSheetController::class, 'show'])->name('employee.timesheet.show');
Route::put('employee/timesheet/{employeeWeeklyTimeSheet}', [EmployeeWeeklyTimeSheetController::class, 'update'])->name('employee.timesheet.update');
Route::get('payroll', function () {
    return inertia('Payroll/Index', []);
})->name('payroll.index');
