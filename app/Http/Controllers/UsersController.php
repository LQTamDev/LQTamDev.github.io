<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Events\UserCreatedEvent;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    use PasswordValidationRules;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return inertia('Users/Index', [
            'users' => User::all()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'show_url' => URL::route('users.show', $user),
                    'update_url' => URL::route('users.update', $user)
                ];
            }),
            'store_url' => URL::route('users.store')
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
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:50', 'unique:users,email'],
            'password' => $this->passwordRules(),
            'fname' => ['required', 'string', 'max:50'],
            'lname' => ['required', 'string', 'max:50'],
            'wage' => ['required', 'numeric', 'between:0,999999.99'],
            'ssn' => ['required', 'numeric', 'between:0,999999999', 'unique:employees,ssn'],
            'role' => ['required']
        ])->validate();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $user->assignRole($request->role);
        $data = $request->only(['fname', 'lname', 'wage', 'ssn']);
        $data['user_id'] = $user->id;
        $data['password'] = Hash::make($request->password);
        Employee::create($data);
        // event(new UserCreatedEvent($data));
        return redirect()->route('employees.index')->with('message', 'Account Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:50', 'unique:users,email,' . $user->id],
            'fname' => ['required', 'string', 'max:50'],
            'lname' => ['required', 'string', 'max:50'],
            'wage' => ['required', 'numeric', 'between:0,999999.99'],
            'ssn' => ['required', 'numeric', 'between:0,999999999.99', 'unique:employees,ssn,' . $user->employee->id],
            'role' => ['required']
        ])->validate();
        $user->update(
            $request->only(['name', 'email'])
        );
        $user->syncRoles($request->role);
        $user->employee->update($request->only(['fname', 'lname', 'wage', 'ssn']));

        return redirect()->route('employees.index')
            ->with('message', 'Account Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('message', 'User Deleted Successfully.');
    }
}
