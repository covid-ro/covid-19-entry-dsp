<?php

namespace App\Http\Controllers;

use App\Declaration;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use PeterColes\Countries\CountriesFacade;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if (Auth::user()->username === env('ADMIN_USER')) {
            $declarations = Declaration::all(
                Declaration::API_DECLARATION_URL(),
                ['page' => $request->query('page'), 'per_page' => 30] //TODO paginate dinamically
            );

            return view('home')->with(['declarations' => $declarations]);
        }
        return view('home');
//        TODO handle case for DSP user
    }

    /**
     * Show the declaration.
     *
     * @param string $code
     * @param Request $request
     *
     * @return Factory|View
     */
    public function show(string $code, Request $request)
    {
        if ($request->session()->has('language')) {
            app()->setLocale($request->session()->get('language'));
            $countries = CountriesFacade::lookup($request->session()->get('language'));
        } else {
            $countries = CountriesFacade::lookup('ro_RO');
        }

        $declaration = Declaration::find(Declaration::API_DECLARATION_URL(), $code);

        if (!is_array($declaration)) {
            session()->flash('type', 'danger');
            session()->flash('message', $declaration);
            $formatedDeclaration['declaration'] = [];
        } else {
            $formatedDeclaration = Declaration::getDeclarationCollectionFormatted(
                $declaration,
                $countries,
                app()->getLocale()
            );
        }

        return view(
            'declaration',
            [
                'declaration' => $formatedDeclaration['declaration'],
                'pdfData' => json_encode($formatedDeclaration['pdf_data']),
                'signature' => $formatedDeclaration['signature'],
                'qrCode' => $formatedDeclaration['qr_code']
            ]
        );
    }

    /**
     * Change language
     *
     * @param Request $request
     *
     * @return RedirectResponse|void
     */
    public function postChangeLanguage(Request $request)
    {
        if ($request->input('lang')) {
            $request->session()->put('language', $request->input('lang'));
            app()->setLocale($request->input('lang'));
            return back();
        }

        return;
    }

    /**
     * Refresh list of declarations
     *
     * @param Request $request
     *
     * @return RedirectResponse|void
     */
    public function postRefreshList(Request $request)
    {
        if ($request->input('refresh')) {
            Cache::forget('declarations-' . Auth::user()->username);
            return back();
        }

        return;
    }

    /**
     * Search and return a declaration
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postSearchDeclaration(Request $request)
    {
        try {
            if ($request->input('code')) {
                $code = $this->sanitizeInput($request->input('code'), ' ');
                $errorsMessage = '';

                if (substr($code, 0, 2) === 'Er') {
                    $errorsMessage .= $code . ' ';
                }

                $declaration = Declaration::search(Declaration::API_DECLARATION_URL(), $code);

//                if (!is_array($declaration)) {
//                    throw new Exception($declaration);
//                }
//
//                if (Auth::user()->checkpoint != $declaration['border_checkpoint']['id']) {
//                    $errorsMessage .= __('app.The person chose another border checkpoint.') . ' ';
//                }
//
//                if (!$declaration['border_crossed_at']) {
//                    $errorsMessage .= __('app.The person did not arrived at any border checkpoint.') . ' ';
//                }
//
//                if ($declaration['border_crossed_at'] && !$declaration['border_validated_at']) {
//                    $crossedAt = Carbon::parse($declaration['border_crossed_at'])->format('d m Y H:i:s');
//                    $errorsMessage .= __(
//                            'app.The person crossed border checkpoint at :crossedAt but was not validated yet.',
//                            ['crossedAt' => $crossedAt]
//                        ) . ' ';
//                }
//
//                if ($declaration['dsp_validated_at'] && $declaration['dsp_user_name'] !== Auth::user()->username) {
//                    $dspValidatedAt = Carbon::parse($declaration['border_validated_at'])->format('d m Y H:i:s');
//                    $errorsMessage .= __(
//                            'app.The declaration was validated at :dspValidatedAt by another DSP user [:userName].',
//                            [
//                                'dspValidatedAt' => $dspValidatedAt,
//                                'userName' => $declaration['dsp_user_name']
//                            ]
//                        ) . ' ';
//                }

                if (strlen($errorsMessage) < 1) {
                    return response()->json(
                        [
                            'success' => $declaration
                        ]
                    );
                } else {
                    throw new Exception(trim($errorsMessage));
                }
            } else {
                throw new Exception(__('app.There is no code sent.'));
            }
        } catch (Exception $exception) {
            return response()->json(
                [
                    'error' => $exception->getMessage()
                ]
            );
        }
    }

    /**
     * Sanitize declaration code input
     *
     * @param string      $inputValue
     * @param string|null $splitter
     *
     * @return string
     */
    public function sanitizeInput(string $inputValue, string $splitter = null): string {
        try {
            if(strlen($inputValue) < 1) {
                throw new Exception(__('app.No input for sanitize provided'));
            }

            if ($splitter) {
                $tmpArray = explode($splitter, filter_var(trim($inputValue), FILTER_SANITIZE_STRING));
                return $tmpArray[0];
            } else {
                return filter_var(trim($inputValue), FILTER_SANITIZE_STRING);
            }

        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Register declaration to the authenticated user
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postRegisterDeclaration(Request $request)
    {
        try {
            if ($request->input('code')) {
                $code = $request->input('code');
                $dspMeasure = $request->input('measure');
                $userName = Auth::user()->username;
                $errorsMessage = '';

                if (!$dspMeasure) {
                    throw new Exception(__('app.DSP measure error'));
                }

                $registerDeclaration = Declaration::registerDeclaration(
                    Declaration::API_DECLARATION_URL(),
                    $code,
                    $userName,
                    $dspMeasure
                );

                if ($registerDeclaration !== 'success') {
                    $errorsMessage .= $registerDeclaration;
                }

                if (strlen($errorsMessage) < 1) {
                    return response()->json(
                        [
                            'success' => $registerDeclaration,
                            'measure' => $dspMeasure
                        ]
                    );
                } else {
                    throw new Exception(trim($errorsMessage));
                }
            } else {
                throw new Exception(__('app.There is no code sent.'));
            }
        } catch (Exception $exception) {
            return response()->json(
                [
                    'error' => $exception->getMessage()
                ]
            );
        }
    }
}
