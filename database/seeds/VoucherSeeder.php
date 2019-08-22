<?php

use App\Role;
use Illuminate\Database\Seeder;
use BeyondCode\Vouchers\Facades\Vouchers;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name','listener')->first();
        Vouchers::create($role, 3);
    }
}
