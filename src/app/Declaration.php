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

            // versiunea 1.1
            $formattedDeclarations[$key]['accept_personal_data'] = $declaration['accept_personal_data'];
            $formattedDeclarations[$key]['accept_read_law'] = $declaration['accept_read_law'];
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
        $declaration['travelling_date_year'] = Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
            ->format('Y');
        $declaration['travelling_date_month'] = Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
            ->format('m');
        $declaration['travelling_date_day'] = Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
            ->format('d');
        if(!is_null($declaration['border_crossed_at'])) {
            $declaration['border_validated_at'] = ($locale === 'ro') ?
                Carbon::parse($declaration['border_validated_at'])->format('d m Y') :
                Carbon::parse($declaration['border_validated_at'])->format('Y-m-d');
        }
        $declaration['birth_date_year'] = Carbon::createFromFormat('Y-m-d', $declaration['birth_date'])
            ->format('Y');
        $declaration['birth_date_month'] = Carbon::createFromFormat('Y-m-d', $declaration['birth_date'])
            ->format('m');
        $declaration['birth_date_day'] = Carbon::createFromFormat('Y-m-d', $declaration['birth_date'])
            ->format('d');
        $formatedResult['qr_code'] = 'data:image/png;base64,' .
            base64_encode(QrCode::format('png')->size(100)->generate($declaration['code']));
        $declaration['isolation_address'] = '';
        if($declaration['home_isolated']) {
            $declaration['isolation_address'] = $declaration['home_address'];
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
            'birthYear' => $declaration['birth_date_year'],
            'birthMonth' => $declaration['birth_date_month'],
            'birthDay' => $declaration['birth_date_day'],
            'dateArrival' => $declaration['border_validated_at'],
            'countryLeave' => $declaration['travelling_from_country'],
            'localityLeave' => $declaration['travelling_from_city'],
            'travellingYear' => $declaration['travelling_date_year'],
            'travellingMonth' => $declaration['travelling_date_month'],
            'travellingDay' => $declaration['travelling_date_day'],
            'phoneNumber' => $declaration['phone'],
            'emailAddress' => $declaration['email'],
            'addresses' => $declaration['isolation_address'],
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
            'destination' => $declaration['isolation_address'],
            'vehicle' => $declaration['vehicle_registration_no'],
            'route' => trim(str_replace("\n", ' ', $declaration['travel_route'])),
            'documentDate' => $declaration['current_date'],
            'documentLocality' => $declaration['border']
        ];

        $formatedResult['declaration'] = $declaration;

        return $formatedResult;
    }
}
