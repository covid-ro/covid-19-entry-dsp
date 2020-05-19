<?php

namespace App\Traits;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use function Psy\debug;

trait ApiTrait {

    /**
     * Connect to API
     *
     * @return PendingRequest
     */
    public static function connectApi()
    {
        return Http::withHeaders([
            'X-API-KEY' => env('COVID19_DSP_API_KEY')
        ]);
    }

    /**
     * Handle API errors
     *
     * @param int $statusCode
     *
     * @return array|string|null
     */
    public static function returnStatus(int $statusCode)
    {
        $result = __('app.Unknown error');

        switch ($statusCode) {
            case 401:
                $result = __('app.Unauthorized');
                break;
            case 404:
                $result = __('app.Not Found');
                break;
            case 500:
                $result = __('app.An unexpected condition was encountered');
                break;
        }

        return $result;
    }
}
