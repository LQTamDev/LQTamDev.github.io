<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return inertia('Inventory/Index', [
            'inventories' => Inventory::with('food')->get(),
            'menus' => Menu::all(),
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
            'quantity' => ['required', 'between:1,1000', 'numeric'],
            'name' => ['required'],
            'menu_id' => 'required'
        ], ['menu_id.required' => 'The food field is required.']);
        $inventory = Inventory::where('name', '=', $fields['name'])->first();
        if ($inventory) {
            $name = $fields['name'];
            return redirect()->back()->withErrors("$name existed.", 'item_existed');
        } else {
            Inventory::create($fields);
            return redirect()->route('inventories.index')->with('message', 'Item created successfully.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $food_id)
    {
        $fields = $request->validateWithBag('updateItem', [
            'quantity' => 'numeric|required|between:0,1000'
        ]);
        Inventory::find($food_id)->update(['quantity' => $fields['quantity']]);
        return redirect()->route('inventories.index')->with('message', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $food_id)
    {

        Inventory::find($food_id)->delete();
        return redirect()->route('inventories.index')->with('message', 'Item deleted successfully.');
    }
}
