<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return inertia("Menu/Index", [
            'menus' => Menu::with('stock')->get()->map(function ($menu) {
                return [
                    'food_id' => $menu->food_id,
                    'name' => $menu->name,
                    'type' => $menu->type,
                    'price' => $menu->price,
                    'stock' => $menu->stock
                ];
            }),
            'types' => Menu::select('type')->distinct()->pluck('type')->map(function ($type) {
                return [
                    'name' => $type
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
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'between:0,500'],
        ]);

        $menu = Menu::where('name', '=', $fields['name'])->first();
        if ($menu) {
            return redirect()->back()->with('message', 'Item existed.');
        } else {
            Menu::create([
                'name' => $fields['name'],
                'type' => $fields['type'],
                'price' => $fields['price'],
            ]);
            return redirect()->route('menus.index')->with('message', 'Item Created Successfully');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        $fields = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'between:0,500'],
        ]);

        $menu->update([
            'name' => $fields['name'],
            'type' => $fields['type'],
            'price' => $fields['price'],
        ]);

        return redirect()->route('menus.index')->with('message', 'Item Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('message', 'Item Deleted Successfully');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $menus = Menu::select();
        $name = $request->name ?? '';
        $type = $request->type ?? '';
        return inertia('Menu/Index', [
            'menus' => $menus->where('name', 'like', '%' . $name . '%')
                ->where('type', 'like', '%' . $type . '%')->get()->map(function ($menu) {
                    return [
                        'food_id' => $menu->food_id,
                        'name' => $menu->name,
                        'type' => $menu->type,
                        'price' => $menu->price,
                    ];
                }),
            'types' => Menu::select('type')->distinct()->pluck('type')->map(function ($type) {
                return [
                    'name' => $type
                ];
            })
        ]);
    }
}
