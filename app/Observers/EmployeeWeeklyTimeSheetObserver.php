<?php

namespace App\Observers;

use App\Models\EmployeeInfo;
use App\Models\EmployeeWeeklyTimeSheet;

class EmployeeWeeklyTimeSheetObserver
{
    /**
     * Handle the EmployeeWeeklyTimeSheet "created" event.
     *
     * @param  \App\Models\EmployeeWeeklyTimeSheet  $employeeWeeklyTimeSheet
     * @return void
     */
    public function created(EmployeeWeeklyTimeSheet $employeeWeeklyTimeSheet)
    {
        EmployeeInfo::create(['hours' => $employeeWeeklyTimeSheet->hours, 'employee_id' => $employeeWeeklyTimeSheet->employee_id]);
    }

    /**
     * Handle the EmployeeWeeklyTimeSheet "updated" event.
     *
     * @param  \App\Models\EmployeeWeeklyTimeSheet  $employeeWeeklyTimeSheet
     * @return void
     */
    public function updated(EmployeeWeeklyTimeSheet $employeeWeeklyTimeSheet)
    {
        EmployeeInfo::where('employee_id', $employeeWeeklyTimeSheet->employee_id)->first()->update(['hours' => $employeeWeeklyTimeSheet->hours]);
    }

    /**
     * Handle the EmployeeWeeklyTimeSheet "deleted" event.
     *
     * @param  \App\Models\EmployeeWeeklyTimeSheet  $employeeWeeklyTimeSheet
     * @return void
     */
    public function deleted(EmployeeWeeklyTimeSheet $employeeWeeklyTimeSheet)
    {
        //
    }

    /**
     * Handle the EmployeeWeeklyTimeSheet "restored" event.
     *
     * @param  \App\Models\EmployeeWeeklyTimeSheet  $employeeWeeklyTimeSheet
     * @return void
     */
    public function restored(EmployeeWeeklyTimeSheet $employeeWeeklyTimeSheet)
    {
        //
    }

    /**
     * Handle the EmployeeWeeklyTimeSheet "force deleted" event.
     *
     * @param  \App\Models\EmployeeWeeklyTimeSheet  $employeeWeeklyTimeSheet
     * @return void
     */
    public function forceDeleted(EmployeeWeeklyTimeSheet $employeeWeeklyTimeSheet)
    {
        //
    }
}
