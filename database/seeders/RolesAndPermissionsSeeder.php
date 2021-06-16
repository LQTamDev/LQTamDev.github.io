<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::updateOrCreate(['name' => 'create employee']);
        Permission::updateOrCreate(['name' => 'edit employee']);
        Permission::updateOrCreate(['name' => 'delete employee']);
        Permission::updateOrCreate(['name' => 'add menu item']);
        Permission::updateOrCreate(['name' => 'delete menu item']);
        Permission::updateOrCreate(['name' => 'manage payroll']);
        Permission::updateOrCreate(['name' => 'manage inventory']);
        Permission::updateOrCreate(['name' => 'graphical analysis']);
        Permission::updateOrCreate(['name' => 'seat customer']);
        Permission::updateOrCreate(['name' => 'assign waiter']);
        Permission::updateOrCreate(['name' => 'prepare and cook']);
        Permission::updateOrCreate(['name' => 'clean and prep']);
        Permission::updateOrCreate(['name' => 'take order']);
        Permission::updateOrCreate(['name' => 'deliver order']);
        Permission::updateOrCreate(['name' => 'collect payment']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::updateOrCreate(['name' => 'manager']);
        $role->givePermissionTo([
            'create employee',
            'edit employee',
            'delete employee',
            'add menu item',
            'delete menu item',
            'manage payroll',
            'graphical analysis',
            'manage inventory'
        ]);

        // or may be done by chaining
        $role = Role::updateOrCreate(['name' => 'host'])
            ->givePermissionTo(['seat customer', 'assign waiter']);

        $role = Role::updateOrCreate(['name' => 'cook'])
            ->givePermissionTo(['prepare and cook']);
        $role = Role::updateOrCreate(['name' => 'waiter'])
            ->givePermissionTo(['take order', 'deliver order', 'collect payment']);
        $role = Role::updateOrCreate(['name' => 'busboy'])
            ->givePermissionTo('clean and prep');
    }
}
