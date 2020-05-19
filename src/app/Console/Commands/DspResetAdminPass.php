<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DspResetAdminPass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsp:reset_admin_pass';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DSP - reset the existing password for admin user to the generic password in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminDspUsername = env('ADMIN_USER');
        $this->info('Check if requested user exist in database.');
        $adminUser = DB::table('users')
            ->where('username', $adminDspUsername)
            ->first();
        try {
            if ($adminUser) {
                $this->info('Reset password for user ' . $adminDspUsername . '.');
                DB::table('users')
                    ->where('username', $adminDspUsername)
                    ->update(
                        [
                            'password' => Hash::make($adminUser->generic_password)
                        ]);
            } else {
                $this->warn('User ' . $adminDspUsername . ' does not exist in DB.');
                $this->info('Create new user ' . $adminDspUsername . ' in DB.');
                Artisan::call('db:seed', [
                        '--class' => 'AdminUserSeeder',
                        '--force' => true]
                );
            }
            $this->info('The password for user ' . $adminDspUsername . ' was successfully reset.');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
