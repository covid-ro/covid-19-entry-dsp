<?php

namespace App\Providers;

use Closure;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class CacheUntilUpdatedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Cache::macro('untilUpdated', function ($key, $date, Closure $callback)
        {
            if ( ! $date instanceof DateTime) {
                $date = Carbon::now()->addMinutes($date);
            }

            if (is_null($value = Cache::get($key)))
            {
                $data = $callback();
                Cache::forever($key, compact('date', 'data'));
                return $data;
            }

            if ($value['date'] >= $date && ($data = $callback()) !== false)
            {
                Cache::forever($key, compact('date', 'data'));
                return $data;
            }

            return $value['data'];
        });
    }
}
