<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandlerTaskRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->hasRole('cook')) {
            return redirect()->route('chef.orders.index');
        } else if ($request->user()->hasRole('host') || $request->user()->hasRole('waiter') || $request->user()->hasRole('busboy')) {
            return redirect()->route('tables.index');
        } else
            return inertia('Dashboard');
    }
}
