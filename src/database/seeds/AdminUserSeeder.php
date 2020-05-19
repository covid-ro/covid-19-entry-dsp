<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'admin_dsp',
            'name' => 'Adminstrator DSP',
            'password' => Hash::make('admin321'),
            'generic_password' => 'admin321'
        ]);
    }
}
