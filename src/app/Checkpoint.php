<?php

namespace App;

use App\Traits\ApiTrait;
use Exception;

class Checkpoint
{
    use ApiTrait;

    /**
     * Set a constant with expression in value, in a static content
     *
     * @return string
     */
    public static function API_BORDER_URL() {
        return env('COVID19_DSP_API') . 'border/checkpoint';
    }

    /**
     * Get all Border Checkpoints
     *
     * @param string $url
     * @param array  $params
     *
     * @return array|string
     */
    public static function all(string $url, array $params)
    {
        try {
            $apiRequest = self::connectApi()
                ->get($url, $params);

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {

                $bordersArray = $apiRequest['data'];
                usort($bordersArray, function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });
                $bordersArray = self::super_unique($bordersArray,'name');
//print_r(json_encode($bordersArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));die;
                return $bordersArray;
            } else {
                return $apiRequest['message'];
            }

        } catch(Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Get a specific Border Checkpoint
     *
     * @param string $url
     * @param int    $id
     *
     * @return string
     */
    public static function find(string $url, int $id)
    {
        try {
            $apiRequest = self::connectApi()
                ->get($url . DIRECTORY_SEPARATOR . $id);

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {
                return $apiRequest['data'];
            } else {
                return $apiRequest['message'];
            }

        } catch(Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Remove duplicates in multiarray
     *
     * @param array  $array
     * @param string $key
     *
     * @return array
     */
    private static function super_unique(array $array, string $key) :array
    {
        $temp_array = [];
        foreach ($array as &$v) {
            if (!isset($temp_array[$v[$key]]))
                $temp_array[$v[$key]] =& $v;
        }
        $array = array_values($temp_array);
        return $array;

    }
}
