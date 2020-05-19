<?php

namespace App;

use App\Traits\ApiTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
    public static function API_DECLARATION_URL() {
        return env('COVID19_DSP_API') . 'declaration';
    }

    /**
     * Get all Declarations
     *
     * @param string      $url
     * @param array       $params
     * @param string|null $format
     *
     * @return array|string
     */
    public static function all(string $url, array $params, string $format = null)
    {
        $user = (Auth::user()->username !== env('ADMIN_USER')) ? Auth::user()->username : 'admin' ;
        return Cache::untilUpdated('declarations-' . Auth::user()->username, env('CACHE_DECLARATIONS_PERSISTENCE'),
            function() use ($url, $params, $format, $user) {
                try {
                    $apiRequest = self::connectApi()
                        ->get($url, $params);

                    if (!$apiRequest->successful()) {
                        throw new Exception(self::returnStatus($apiRequest->status()));
                    }

                    if ($apiRequest['data']) {
                        if ($format === 'datatables') {
                            return self::dataTablesFormat($apiRequest['data'], $user);
                        }
                        return $apiRequest['data'];
                    } else {
                        return $apiRequest['message'];
                    }

                } catch(Exception $exception) {
                    return $exception->getMessage();
                }
            }
        );
    }

    /**
     * Get a specific Declaration
     *
     * @param string $url
     * @param string $code
     *
     * @return array|string
     */
    public static function find(string $url, string $code)
    {
        try {
            $apiRequest = self::connectApi()
                ->get($url . DIRECTORY_SEPARATOR . $code);

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {
                return $apiRequest['declaration'];
            } else {
                return $apiRequest['message'];
            }

        } catch(Exception $exception) {
            return $exception->getMessage();
        }
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

        } catch(Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Register a declaration with a specific DSP user
     *
     * @param string $url
     * @param string $code
     * @param string $username
     *
     * @return mixed|string
     */
    public static function registerDeclaration(string $url, string $code, string $username)
    {
        try {
            $apiRequest = self::connectApi()
                ->put(
                    $url . DIRECTORY_SEPARATOR . $code . DIRECTORY_SEPARATOR . 'dsp',
                    ['dsp_user_name' => $username ]
                );

            if (!$apiRequest->successful()) {
                throw new Exception(self::returnStatus($apiRequest->status()));
            }

            if ($apiRequest['status'] === 'success') {
                return 'success';
            } else {
                return $apiRequest['message'];
            }

        } catch(Exception $exception) {
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
    private static function dataTablesFormat(array $data, string $user = null) : array
    {
        $countries = CountriesFacade::lookup('ro_RO');
        $formattedDeclarations = [];

        foreach ($data as $key => $declaration) {
            if($user !== 'admin') {
                if ($user
                    && !empty($declaration['dsp_user_name'])
                    || $declaration['dsp_user_name'] === $user
                    || Auth::user()->checkpoint != $declaration['border_checkpoint']['id']
                    || empty($declaration['border_crossed_at'])
                    || empty($declaration['border_validated_at'])
                ) {
                    continue;
                }
            }

            $formattedDeclarations[$key]['code'] = $declaration['code'];
            $formattedDeclarations[$key]['name'] = $declaration['name'] . ' ' . $declaration['surname'];
            $formattedDeclarations[$key]['country'] = $countries[$declaration['travelling_from_country_code']];
            $formattedDeclarations[$key]['checkpoint'] = trim(str_replace('P.T.F.', '', $declaration['border_checkpoint']['name']));
            $formattedDeclarations[$key]['auto'] = $declaration['vehicle_registration_no'];
            $formattedDeclarations[$key]['signed'] = $declaration['signed'];
            $formattedDeclarations[$key]['app_status'] = is_null($declaration['created_at']) ? false : true;
            $formattedDeclarations[$key]['border_status'] = is_null($declaration['border_validated_at']) ? false : true;
            $formattedDeclarations[$key]['dsp_status'] = is_null($declaration['dsp_validated_at']) ? false : true;
            $formattedDeclarations[$key]['url'] = '/declaratie/' . $declaration['code'];
            $formattedDeclarations[$key]['phone'] = $declaration['phone'];
            $formattedDeclarations[$key]['travelling_from_date'] = Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
                ->format('d m Y');
            $formattedDeclarations[$key]['travelling_from_city'] = $declaration['travelling_from_city'] . ', ' . $countries[$declaration['travelling_from_country_code']];
            $formattedDeclarations[$key]['itinerary_country_list'] = '';
            if ($declaration['itinerary_country_list'] && count($declaration['itinerary_country_list']) > 0) {
                foreach ($declaration['itinerary_country_list'] as $country) {
                    $formattedDeclarations[$key]['itinerary_country_list'] .= $countries[$country] . ', ';
                }
                $formattedDeclarations[$key]['itinerary_country_list'] = substr(trim($formattedDeclarations[$key]['itinerary_country_list']), 0, -1);
            }
            $formattedDeclarations[$key]['created_at'] = is_null($declaration['created_at']) ?
                null : Carbon::parse($declaration['created_at'])->format('d m Y H:i:s');
            $formattedDeclarations[$key]['border_validated_at'] = is_null($declaration['border_validated_at']) ?
                null : Carbon::parse($declaration['border_validated_at'])->format('d m Y H:i:s');
            $formattedDeclarations[$key]['dsp_validated_at'] = is_null($declaration['dsp_validated_at']) ?
                null : Carbon::parse($declaration['dsp_validated_at'])->format('d m Y H:i:s');
            $formattedDeclarations[$key]['dsp_user_name'] = is_null($declaration['dsp_validated_at']) ?
                null : $declaration['dsp_user_name'];
        }

        return $formattedDeclarations;
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
    public static function getDeclationColectionFormated($declaration, $countries, $locale)
    {
        $formatedResult = [];
        $signature = '';
        $addresses = [];
        $visitedCountries = [];

        if($declaration['signed']) {
            $signature = self::getSignature(self::API_DECLARATION_URL(), $declaration['code']);

            if(is_array($signature)) {
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
        if ($locale === 'ro') {
            $declaration['travelling_from_date'] = Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
                ->format('d m Y');
        }
        if(!is_null($declaration['border_crossed_at'])) {
            $declaration['border_validated_at'] = ($locale === 'ro') ?
                Carbon::parse($declaration['border_validated_at'])->format('d m Y') :
                Carbon::parse($declaration['border_validated_at'])->format('Y-m-d');
        }
        $declaration['birth_date'] = Carbon::createFromFormat('Y-m-d', $declaration['birth_date'])
            ->format('d/m/Y');
        $formatedResult['qr_code'] = 'data:image/png;base64,' .
            base64_encode(QrCode::format('png')->size(100)->generate($declaration['code']));
        if (count($declaration['isolation_addresses']) > 0) {
            if ($locale === 'ro') {
                foreach ($declaration['isolation_addresses'] as $key => $address) {
                    $declaration['isolation_addresses'][$key]['city_arrival_date'] = Carbon::createFromFormat('Y-m-d', $address['city_arrival_date'])
                        ->format('d m Y');
                    $declaration['isolation_addresses'][$key]['city_departure_date'] = Carbon::createFromFormat('Y-m-d', $address['city_departure_date'])
                        ->format('d m Y');
                }
            }
            foreach ($declaration['isolation_addresses'] as $key => $address) {
                $addresses[$key]['locality'] = $address['city'] . (($address['county'] && strlen
                        ($address['county']) > 0) ? ', ' . $address['county'] : '');
                $addresses[$key]['dateArrival'] = $address['city_arrival_date'];
                $addresses[$key]['dateLeave'] = $address['city_departure_date'];
                $addresses[$key]['fullAddress'] = $address['city_full_address'];
            }
        }
        $declaration['fever'] = in_array('fever', $declaration['symptoms']) ?? true;
        $declaration['swallow'] = in_array('swallow', $declaration['symptoms']) ?? true;
        $declaration['breath'] = in_array('breath', $declaration['symptoms']) ?? true;
        $declaration['cough'] = in_array('cough', $declaration['symptoms']) ?? true;
        $declaration['itinerary'] = '';
        if (count($declaration['itinerary_country_list']) > 0) {
            foreach($declaration['itinerary_country_list'] as $country) {
                $visitedCountries[] = $countries[$country];
                $declaration['itinerary'] .= '<strong>' . $countries[$country] . '</strong>, ';
            }
            $declaration['itinerary'] = substr($declaration['itinerary'], 0, -2);
        }
        $declaration['border'] = '';
        if ($declaration['border_checkpoint'] && $declaration['border_checkpoint']['status'] === 'active') {
            $declaration['border'] = trim(str_replace('P.T.F.', '', $declaration['border_checkpoint']['name']));
        }
        $declaration['current_date'] = ($locale === 'ro') ? Carbon::now()->format('d m Y') :
            Carbon::now()->format('m/d/Y');
        $formatedResult['pdf_data'] = [
            'code' => $declaration['code'],
            'locale' => $locale,
            'lastName' => $declaration['name'],
            'firstName' => $declaration['surname'],
            'sex' => $declaration['sex'],
            'idCardSeries' => $declaration['document_series'],
            'idCardNumber' => $declaration['document_number'],
            'birthday' => $declaration['birth_date'],
            'dateArrival' => $declaration['border_validated_at'],
            'countryLeave' => $declaration['travelling_from_country'],
            'localityLeave' => $declaration['travelling_from_city'],
            'dateLeave' => $declaration['travelling_from_date'],
            'phoneNumber' => $declaration['phone'],
            'emailAddress' => $declaration['email'],
            'addresses' => $addresses,
            'answers' => [
                'hasVisited' => $declaration['q_visited'],
                'hasContacted' => $declaration['q_contacted'],
                'isHospitalized' => $declaration['q_hospitalized'],
                'hasFever' => $declaration['fever'],
                'hasDifficultySwallow' => $declaration['swallow'],
                'hasDifficultyBreath' => $declaration['breath'],
                'hasIntenseCough' => $declaration['cough'],
            ],
            'organization' => '',
            'visitedCountries' => $visitedCountries,
            'borderCrossingPoint' => $declaration['border'],
            'destination' => (count($addresses) > 0 ? $addresses[0]['fullAddress'] . ', ' . $addresses[0]['locality']
                : ''),
            'vehicle' => $declaration['vehicle_registration_no'],
            'route' => trim(str_replace("\n", ' ', $declaration['travel_route'])),
            'documentDate' => $declaration['current_date'],
            'documentLocality' => $declaration['border']
        ];

        $formatedResult['declaration'] = $declaration;

        return $formatedResult;
    }
}
