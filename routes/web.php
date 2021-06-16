<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UsersController;
use App\Models\Order;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

function random_color_part()
{
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color()
{
    return random_color_part() . random_color_part() . random_color_part();
}
function randomGradient()
{
    $color1 = sprintf("#%06x", rand(0, 16777215));
    $color2 = sprintf("#%06x", rand(0, 16777215));
    $angle = rand(1, 360);
    $gradient = "background: linear-gradient({$angle}deg, {$color1}, {$color2})";
    return $gradient;
}
function date_sort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function getEmployeeEfficientBar($from, $to)
{
    // Carbon::parse($from)->diffInDays(Carbon::parse($to))
    $data = DB::table('employee_efficients')
        ->leftJoin('employees as e', 'e.id', '=', 'employee_efficients.employee_id')
        ->select('employee_efficients.*', 'e.fname', 'e.lname')
        ->whereBetween('employee_efficients.created_at', [$from, $to])
        ->get()
        ->groupBy(['employee_id', function ($date) {
            return Carbon::parse($date->created_at)->format('d/m/Y');
        }])->toArray();
    $labels = [];
    foreach ($data as $values) {
        foreach ($values as $k => $v) {
            array_push($labels, $k);
        }
    }
    $response = [];
    $employeeEfficientBarData = [];

    $dataUnique = array_unique($labels);
    usort($dataUnique, "date_sort");
    $response['labels'] = $dataUnique;
    foreach ($data as $values) {
        $dataAttr = [];
        foreach ($response['labels'] as $label) {
            if (array_key_exists($label, $values)) {
                array_push($dataAttr, count($values[$label]));
            } else {
                array_push($dataAttr, 0);
            }
        }
        $firstItem = $values[array_key_first($values)][0];
        array_push($employeeEfficientBarData, [
            'label' => $firstItem->fname . ' ' . $firstItem->lname,
            'data' => $dataAttr,
            'backgroundColor' => '#' . random_color()
        ]);
    }
    $response['dataset'] = $employeeEfficientBarData;
    return $response;
}

function getRestaurantTraffic($from, $to)
{
    $orders = DB::table('orders')
        ->select('orders.*')
        ->whereBetween('orders.created_at', [$from, $to])
        ->get();
    $arrDate = array_unique($orders->pluck('created_at')->map(function ($value) {
        return Carbon::parse($value)->format('d/m/Y');
    })->toArray());
    $orderGrouped = $orders->groupBy(function ($value) {
        return Carbon::parse($value->created_at)->format('d/m/Y');
    })->toArray();
    $employeeEfficientTraffic = [];
    usort($arrDate, 'date_sort');
    foreach ($arrDate as $date) {

        if (array_key_exists($date, $orderGrouped)) {
            array_push($employeeEfficientTraffic, count($orderGrouped[$date]));
        } else {
            array_push($employeeEfficientTraffic, 0);
        }
    }
    return [
        'labels' => $arrDate,
        'dataset' => [
            [
                'label' => "Data",
                'borderColor' => "#FC2525",
                'pointBackgroundColor' => "white",
                'borderWidth' => 1,
                'pointBorderColor' => "white",
                'backgroundColor' => randomGradient(),
                'data' => $employeeEfficientTraffic
            ]
        ]
    ];
}
function getPopularItem()
{
    $employeeEfficientPopularPie = Order::all()->take(7)->pluck('data')->map(function ($item) {
        return json_decode($item);
    })->flatten()->groupBy('name');
    $labels = [];
    $backgroundColor = [];
    $data = [];
    foreach ($employeeEfficientPopularPie as $k => $values) {
        $total = 0;
        foreach ($values as $val) {
            $total += $val->stock->quantity;
        }
        array_push($labels, $k);
        array_push($backgroundColor, '#' . random_color());
        array_push($data, $total);
    }
    return ['labels' => $labels, 'backgroundColor' => $backgroundColor, 'data' => $data];
}

Route::get('/', function () {
    if (!request()->user()) {
        return Inertia::render('Auth/Login', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
    return redirect()->route('dashboard');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    if (request()->user()->hasRole('cook')) {
        return redirect()->route('chef.orders.index');
    } else if (request()->user()->hasRole('host') || request()->user()->hasRole('waiter') || request()->user()->hasRole('busboy')) {
        return redirect()->route('tables.index');
    } else {
        $from = request()->date_from ?? Carbon::now()->subMonth();
        $to = request()->date_to ?? Carbon::now();
        // BAR
        $response = [];
        $response['employeeEfficient'] = getEmployeeEfficientBar($from, $to);
        // LINE
        $response['restaurantTraffic'] = getRestaurantTraffic($from, $to);
        // PIE
        $response['popularItem'] = getPopularItem();
        return Inertia::render('Dashboard', [
            'graphs' => $response
        ]);
    }
})->name('dashboard');
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::apiResource('users', UsersController::class)->except(['show'])->middleware('admin');
    Route::apiResource('employees', EmployeeController::class)->except(['show'])->middleware('admin');
    Route::get('rules', function (Request $request) {
        return $request->user()->getPermissionsViaRoles()->pluck('name');
    })->name('rules.show');
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/show', [RoleController::class, 'show'])->name('roles.show');
    Route::apiResource('menus', MenuController::class)->except(['show'])->middleware('admin');
    Route::get('menus/q', [MenuController::class, 'search'])->name('menus.search');
    Route::apiResource('tables', TableController::class)->except(['show']);
    Route::get('f/employees', function () {
        return response()->json(
            \App\Models\User::role('waiter')->with('employee')->get()->map(function ($emp) {
                return [
                    'id' => $emp->employee->id,
                    'fname' => $emp->employee->fname,
                    'lname' => $emp->employee->lname,
                    'name' => $emp->employee->user->name,
                    'tableAssigned' => $emp->employee->tableAssigned->pluck('table_id')
                ];
            })
        );
    })->name('frontend.employees.index');
    Route::get('employees/{user_id}/timesheet', [EmployeeController::class, 'show'])->name('employees.show');
    Route::post('employees/{employee}/paycheck', [EmployeeController::class, 'paycheck'])->name('employees.paycheck');
    Route::get('employees/{user_id}/paycheck', [EmployeeController::class, 'showPayCheck'])->name('employees.paycheck.show');
});

Route::get('some', function () {
    return Order::all()->take(7)->pluck('data')->map(function ($item) {
        return json_decode($item);
    })->flatten()->groupBy('name');
});
