<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'admin'], function () {
    Route::get('inventories', [InventoryController::class, 'index'])->name('inventories.index');
    Route::post('inventories', [InventoryController::class, 'store'])->name('inventories.store');
    Route::put('inventories/{food_id}', [InventoryController::class, 'update'])->name('inventories.update');
    Route::delete('inventories/{food_id}', [InventoryController::class, 'destroy'])->name('inventories.destroy');
});
