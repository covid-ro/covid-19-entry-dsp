<?php

namespace App;

use App\Traits\ApiTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use PeterColes\Countries\CountriesFacade;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Declaration
{
    use ApiTrait;

    /**
     * Set a constant with expression in value, in a static content
     *
     * @return string
     */
    public static function API_DECLARATION_URL()
    {
        return env('COVID19_DSP_API') . 'declaration';
    }

    /**
     * Get all Declarations
     *
     * @param string $url
     * @param array $params
     * @param string|null $format
     *
     * @return array|string
     */
    public static function all(string $url, array $params, string $format = null)
    {
        try {
            $params['dsp_user_name'] = (Auth::user()->username !== env('ADMIN_USER')) ? Auth::user()->username : null ;
            $apiRequest = self::connectApi()
                ->get($url, $params);

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['data']) {
                $user = (Auth::user()->username !== env('ADMIN_USER')) ? Auth::user()->username : 'admin' ;
                $data = self::dataTablesFormat($apiRequest['data'], $user);
                return new LengthAwarePaginator($data, $apiRequest['total'], $apiRequest['per_page'], $apiRequest['current_page']);
            } else {
                if(isset($apiRequest['message'])) {
                    return $apiRequest['message'];
                } else {
                    return null;
                }
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Format declarations collection for datatables
     *
     * @param array  $data
     * @param string $user
     *
     * @return array
     */
    private static function dataTablesFormat(array $data, string $user): array
    {
        $countries = CountriesFacade::lookup('ro_RO');

        $formattedDeclarations = [];
        foreach ($data as $key => $declaration) {
            $formattedDeclarations[$key]['code'] = $declaration['code'];
            $formattedDeclarations[$key]['cnp'] = $declaration['cnp'];
            $formattedDeclarations[$key]['name'] = $declaration['name'] . ' ' . $declaration['surname'];
            $formattedDeclarations[$key]['country'] = $countries[$declaration['travelling_from_country_code']];
            $formattedDeclarations[$key]['checkpoint'] = is_null($declaration['border_checkpoint']) ? null : trim(
                str_replace('P.T.F.', '', $declaration['border_checkpoint']['name'])
            );
            $formattedDeclarations[$key]['auto'] = $declaration['vehicle_registration_no'];
            $formattedDeclarations[$key]['signed'] = $declaration['signed'];
            $formattedDeclarations[$key]['app_status'] = is_null($declaration['created_at']) ? false : true;
            $formattedDeclarations[$key]['border_status'] = is_null($declaration['border_validated_at']) ? false : true;
            $formattedDeclarations[$key]['dsp_status'] = is_null($declaration['dsp_validated_at']) ? false : true;
            $formattedDeclarations[$key]['url'] = '/declaratie/' . $declaration['code'];
            $formattedDeclarations[$key]['phone'] = $declaration['phone'];
            $formattedDeclarations[$key]['travelling_from_date'] = is_null($declaration['travelling_from_date']) ? null : Carbon::createFromFormat(
                'Y-m-d',
                $declaration['travelling_from_date']
            )
                ->format('d-m-Y');
            $formattedDeclarations[$key]['travelling_from_city'] = $declaration['travelling_from_city'] . ', ' . $countries[$declaration['travelling_from_country_code']];
            $formattedDeclarations[$key]['itinerary_country_list'] = '';
            if ($declaration['itinerary_country_list'] && count($declaration['itinerary_country_list']) > 0) {
                foreach ($declaration['itinerary_country_list'] as $country) {
                    $formattedDeclarations[$key]['itinerary_country_list'] .= $countries[$country] . ', ';
                }
                $formattedDeclarations[$key]['itinerary_country_list'] = substr(
                    trim($formattedDeclarations[$key]['itinerary_country_list']),
                    0,
                    -1
                );
            }
            $formattedDeclarations[$key]['created_at'] = is_null($declaration['created_at']) ?
                null : Carbon::parse($declaration['created_at'])->format('d-m-Y H:i:s');
            $formattedDeclarations[$key]['border_validated_at'] = is_null($declaration['border_validated_at']) ?
                null : Carbon::parse($declaration['border_validated_at'])->format('d-m-Y H:i:s');
            $formattedDeclarations[$key]['dsp_validated_at'] = is_null($declaration['dsp_validated_at']) ?
                null : Carbon::parse($declaration['dsp_validated_at'])->format('d-m-Y H:i:s');
            $formattedDeclarations[$key]['dsp_user_name'] = is_null($declaration['dsp_validated_at']) ?
                null : $declaration['dsp_user_name'];

            // versiunea 1.1
            $formattedDeclarations[$key]['accept_personal_data'] = $declaration['accept_personal_data'];
            $formattedDeclarations[$key]['accept_read_law'] = $declaration['accept_read_law'];
            $formattedDeclarations[$key]['dsp_measure'] = is_null($declaration['dsp_measure']) ?
                null : $declaration['dsp_measure'];
        }

        return $formattedDeclarations;
    }

    /**
     * Get a specific Declaration
     *
     * @param string $url
     * @param string $code
     * @param bool   $search
     *
     * @return array|string
     */
    public static function find(string $url, string $code, bool $search = false)
    {
        try {
            $apiRequestUrl = $search
                ? $url . DIRECTORY_SEPARATOR . 'search' . DIRECTORY_SEPARATOR
                : $url . DIRECTORY_SEPARATOR;
            $apiRequest = self::connectApi()
                ->get($apiRequestUrl . $code);

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {
                return $search ? $apiRequest['declarations'] : $apiRequest['declaration'];
            } else {
                return $apiRequest['message'];
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Register a declaration with a specific DSP user
     *
     * @param string $url
     * @param string $code
     * @param string $username
     * @param string $measure
     * @param int    $isDspBeforeBorder
     *
     * @return mixed|string
     */
    public static function registerDeclaration(
        string $url,
        string $code,
        string $username,
        string $measure,
        int $isDspBeforeBorder = 0
    )
    {
        try {
            $apiRequest = self::connectApi()
                ->put(
                    $url . DIRECTORY_SEPARATOR . $code . DIRECTORY_SEPARATOR . 'dsp',
                    [
                        'dsp_user_name' => $username,
                        'dsp_measure' => $measure,
                        'is_dsp_before_border' => $isDspBeforeBorder === 1
                    ]
                );

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {
                return 'success';
            } else {
                return $apiRequest['message'];
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Format declaration for individual view
     *
     * @param $declaration
     * @param $countries
     * @param $locale
     *
     * @return array
     */
    public static function getDeclarationCollectionFormatted($declaration, $countries, $locale)
    {
        $formatedResult = [];
        $signature = '';
        $visitedCountries = [];

        if ($declaration['signed']) {
            $signature = self::getSignature(self::API_DECLARATION_URL(), $declaration['code']);

            if (is_array($signature)) {
                if ($signature['status'] === 'success') {
                    $signature = $signature['signature'];
                } else {
                    $signature = $signature['message'];
                }
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', $signature);
                $signature = '';
            }
        }
        $formatedResult['signature'] = $signature;
        $declaration['travelling_from_country'] = $countries[$declaration['travelling_from_country_code']];
        $declaration['travelling_date_year'] = is_null($declaration['travelling_from_date']) ? null : Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
            ->format('Y');
        $declaration['travelling_date_month'] = is_null($declaration['travelling_from_date']) ? null : Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
            ->format('m');
        $declaration['travelling_date_day'] = is_null($declaration['travelling_from_date']) ? null : Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
            ->format('d');
        if (!is_null($declaration['border_crossed_at'])) {
            $declaration['border_validated_at'] = ($locale === 'ro') ?
                Carbon::parse($declaration['border_validated_at'])->format('d-m-Y') :
                Carbon::parse($declaration['border_validated_at'])->format('Y-m-d');
        }
        if ($declaration['birth_date']) {
            $declaration['birth_date_year']  = Carbon::createFromFormat('Y-m-d', $declaration['birth_date'])
                ->format('Y');
            $declaration['birth_date_month'] = Carbon::createFromFormat('Y-m-d', $declaration['birth_date'])
                ->format('m');
            $declaration['birth_date_day']   = Carbon::createFromFormat('Y-m-d', $declaration['birth_date'])
                ->format('d');
        } else {
            $declaration['birth_date_year']  = Carbon::now()->format('Y');
            $declaration['birth_date_month'] = Carbon::now()->format('m');
            $declaration['birth_date_day']   = Carbon::now()->format('d');
        }
        $formatedResult['qr_code'] = 'data:image/png;base64,' .
            base64_encode(QrCode::format('png')->size(100)->generate($declaration['code'] . ' ' . $declaration['cnp']));
        $declaration['isolation_address'] = '';
        if ($declaration['home_isolated']) {
            $declaration['isolation_address'] = __('app.Main address');
        } else {
            if (count($declaration['isolation_addresses']) > 0) {
                $firstIsolationAddress = $declaration['isolation_addresses'][0];
                $declaration['isolation_address'] = __('app.City') . ' ' .
                    $firstIsolationAddress['city'] . ' ' .
                    __('app.Street') . ' ' .
                    $firstIsolationAddress['street'] . ' ' .
                    __('app.Number') . ' ' .
                    $firstIsolationAddress['number'] . ' ' .
                    __('app.Block') . ' ' .
                    $firstIsolationAddress['bloc'] . ' ' .
                    __('app.Entry') . ' ' .
                    $firstIsolationAddress['entry'] . ' ' .
                    __('app.Apartment') . ' ' .
                    $firstIsolationAddress['apartment'] . ' ' .
                    __('app.County') . ' ' .
                    $firstIsolationAddress['county'];
            }
        }
        $declaration['fever'] = in_array('fever', $declaration['symptoms']) ?? true;
        $declaration['swallow'] = in_array('swallow', $declaration['symptoms']) ?? true;
        $declaration['breath'] = in_array('breath', $declaration['symptoms']) ?? true;
        $declaration['cough'] = in_array('cough', $declaration['symptoms']) ?? true;
        $declaration['itinerary'] = '';
        if (count($declaration['itinerary_country_list']) > 0) {
            foreach ($declaration['itinerary_country_list'] as $country) {
                $visitedCountries[] = $countries[$country];
                $declaration['itinerary'] .= '<strong>' . $countries[$country] . '</strong>, ';
            }
            $declaration['itinerary'] = substr($declaration['itinerary'], 0, -2);
        }
        $declaration['border'] = '';
        if ($declaration['border_checkpoint'] && $declaration['border_checkpoint']['status'] === 'active') {
            $declaration['border'] = trim(str_replace('P.T.F.', '', $declaration['border_checkpoint']['name']));
        }
        $declaration['is_dsp_before_border'] = ($declaration['border_checkpoint'] &&
            $declaration['border_checkpoint']['is_dsp_before_border'] === true) ? 1 : 0;
        $declaration['current_date'] = ($locale === 'ro') ? Carbon::now()->format('d-m-Y') :
            Carbon::now()->format('m/d/Y');

        $formatedResult['pdf_data'] = [
            'locale' => $locale,
            'code' => $declaration['code'],
            'measure' => [
                'hospital' => false,
                'quarantine' => false,
                'isolation' => false
            ],
            'lastName' => $declaration['name'],
            'firstName' => $declaration['surname'],
            'idCardNumber' => $declaration['cnp'],
            'dateOfBirth' => [
                'year' => $declaration['birth_date_year'],
                'month' => $declaration['birth_date_month'],
                'day' => $declaration['birth_date_day']
            ],
            'countryDeparture' => $declaration['travelling_from_country'],
            'destinationAddress' => $declaration['isolation_address'],
            'phoneNumber' => $declaration['phone'],
            'documentDate' => $declaration['current_date']
        ];

        $formatedResult['declaration'] = $declaration;

        return $formatedResult;
    }

    /**
     * Get a specific signature from Declaration
     *
     * @param string $url
     * @param string $code
     *
     * @return array|string
     */
    public static function getSignature(string $url, string $code)
    {
        try {
            $apiRequest = self::connectApi()
                ->get($url . DIRECTORY_SEPARATOR . $code . DIRECTORY_SEPARATOR . 'signature');

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            return $apiRequest->json();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
