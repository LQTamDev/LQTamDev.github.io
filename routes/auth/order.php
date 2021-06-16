<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('table/{table_id}/place_order', [OrderController::class, 'placeOrder'])->name('tables.order.place');
Route::post('table/{table_id}/place_order', [OrderController::class, 'store'])->name('tables.order.store');
Route::get('chef/orders', [OrderController::class, 'index'])->name('chef.orders.index');
Route::put('chef/orders/{table_id}/update', [OrderController::class, 'update'])->name('chef.orders.update');
Route::get('orders/{table_id}/paybill', [OrderController::class, 'show'])->name('orders.show.paybill');
