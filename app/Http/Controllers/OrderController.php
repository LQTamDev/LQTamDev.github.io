<?php

namespace App\Http\Controllers;

use App\Events\ChefOrderDoneEvent;
use App\Models\Employee;
use App\Models\EmployeeEfficient;
use App\Models\Order;
use App\Models\Table;
use App\Notifications\ChefOrderDoneNotification;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\TableAssignedToWaiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->user()->hasRole('cook')) {
            $orders = Order::where('status', '<>', 0)->get();
            return inertia('Order/Chef', [
                'orders' => $orders
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $table_id)
    {
        $fields = $request->validate([
            'waiter_id' => 'required',
            'table_id' => 'required',
            'datas' => 'required|array',
        ], [
            'datas.required' => 'Please input valid quantity.Thanks!'
        ]);
        $order = Order::create([
            'waiter_id' => $fields['waiter_id'],
            'table_id' => $fields['table_id'],
            'status' => 2,
            'data' => json_encode($fields['datas']),
        ]);
        EmployeeEfficient::create([
            'table_id' => $fields['table_id'],
            'employee_id' => $fields['waiter_id'],
        ]);
        foreach ($fields['datas'] as $food) {
            $item = \App\Models\Inventory::find($food['stock']['food_id']);
            $item->decrement('quantity', $food['stock']['quantity']);
        }
        $users = \App\Models\User::role(['cook'])->get();
        $order->table->update(['table_status' => 2]);
        Notification::locale('vi_VN')->send($users, new OrderCreatedNotification("New order from table $order->table_id", $order));
        broadcast(new \App\Events\OrderCreatedEvent($order))->toOthers();
        return redirect()->back()->with('message', 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($table_id)
    {
        $orders = Order::with(['table', 'table.waiter'])->where('table_id', $table_id)->get();
        return inertia('Order/PayBill', [
            'orders' => $orders
        ]);
    }

    public function placeOrder(Request $request, $table_id)
    {
        $table = Table::with('orders')->find($table_id);
        $menus = \App\Models\Menu::with('stock')->whereHas('stock', function ($query) {
            $query->where('quantity', '>', 0);
        })->get();
        return inertia('Order/PlaceOrder', [
            'table' => $table,
            'menus' => $menus,
            'types' => array_unique($menus->pluck('type')->toArray())
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $table_id)
    {
        $status = $request->status;
        // if ($status === 1) {
        $order = Order::find($request->id);
        $order->update(['status' => $status]);
        if ($status === 1) {
            foreach ($request->datas as $food) {
                $item = \App\Models\Inventory::find($food['stock']['food_id']);
                if ($item->quantity <= 0) {
                    $item->delete();
                }
            }
            broadcast(new ChefOrderDoneEvent($order))->toOthers();
        }
        if ($status === 0) {
            $user = \App\Models\Employee::find($order->waiter_id)->user;
            $user->notify(new ChefOrderDoneNotification("Order of table $order->table_id ready to delivery.", $order));
        }
        if ($status === -2) {
            $orders = Order::where('table_id', $table_id)->get();
            foreach ($orders as $order) {
                $order->update(['status' => -2]);
            }

            $table = $order->table;
            $table->update(['table_status' => -1]);
            $users = \App\Models\User::role(['busboy'])->get();
            Notification::locale('vi_VN')->send($users, new TableAssignedToWaiter("Table $order->table_id is dirty.", $table));
            return redirect()->back()->with('message', 'Order has been pay.');
        }

        return redirect()->back()->with('message', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
