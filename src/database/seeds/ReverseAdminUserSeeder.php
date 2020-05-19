<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReverseAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')
            ->where('username', '=', 'admin_dsp')
            ->delete();
    }
}
