<?php

namespace App\Http\Controllers;

use App\Events\TableAssignedEvent;
use App\Models\EmployeeEfficient;
use App\Models\Order;
use App\Models\Table;
use App\Notifications\TableAssignedToWaiter;
use App\Notifications\TableDirtyCleanedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->user()->hasRole('manager')) {
            return redirect()->back();
        }
        return inertia('Table/Index', [
            'tables' => Table::all()->map(function ($t) {
                return [
                    'table_id' => $t->table_id,
                    'employee_id' => $t->employee_id,
                    'table_status' => $t->table_status
                ];
            }),
            'orders' => Order::all()
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
        Table::create(['table_status' => 0]);
        return redirect()->route('tables.index')->with('message', 'Table Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function show(Table $table)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Table $table)
    {
        if (Gate::allows('seat customer')) {
            $table->update(['table_status' => 1, 'employee_id' => $request->employee_id]);
            $user = $table->waiter->user;
            $user->notify(new TableAssignedToWaiter("You have been assigned to table $table->table_id", $table));
            EmployeeEfficient::create([
                'table_id' => $table->table_id,
                'employee_id' => $request->employee_id,
            ]);
            return redirect()->route('tables.index')->with('message', 'Dat ban thanh cong.');
        } else if (Gate::allows('clean and prep')) {
            $table->update(['table_status' => 0, 'employee_id' => null]);
            $users = \App\Models\User::role(['host'])->get();
            EmployeeEfficient::create([
                'table_id' => $table->table_id,
                'employee_id' => $request->user()->id,
            ]);
            Notification::locale('vi_VN')->send($users, new TableDirtyCleanedNotification("Table $table->table_id is cleaned", $table));
        }
        broadcast(new TableAssignedEvent($table))->toOthers();
        return redirect()->route('tables.index')->with('message', 'Table Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function destroy(Table $table)
    {
        //
    }
}
