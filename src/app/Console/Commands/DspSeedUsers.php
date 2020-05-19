<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DspSeedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsp:users:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DSP - truncate and re-seed User table';

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
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->warn('Attention! Table Users will be truncated.');
            Artisan::call('db:seed', [
                    '--class' => 'DspUsersSeeder',
                    '--force' => true]
            );
            $this->info('The table Users was successfully re-seed.');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
