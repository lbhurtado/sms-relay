<?php

use App\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();
        DB::table('roles')->delete();

        collect(config('sms-relay.permissions'))->each(function ($permissions, $role) {
            $guard_name = 'web';
            foreach (explode(':', $role) as $key => $value) {
                 switch ($key) {
                     case 0:
                         $name = $value;
                         break;
                      case 1:
                         $guard_name = $value;
                         break;

                     default:
                         $guard_name = 'web';
                         break;
                 }
             } ;
            // $role = Role::create(['name' => $name]);
             $role = Role::create(compact('guard_name', 'name'));
            foreach ($permissions as $permission) {
                $p = Permission::firstOrCreate(['name' => $permission]);
                $role->givePermissionTo($p);
              }
        });
    }
}
