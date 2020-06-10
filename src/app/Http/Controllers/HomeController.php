<?php

namespace App\Http\Controllers;

use App\Checkpoint;
use App\Declaration;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use PeterColes\Countries\CountriesFacade;
use RuntimeException;

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
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $perPage = ($request->session()->get('per-page')) ?:
            env('DECLARATIONS_PER_PAGE');

        $declarations = Declaration::all(
            Declaration::API_DECLARATION_URL(),
            ['page' => $request->query('page'), 'per_page' => $perPage]
        );

        if (!is_object($declarations) || count($declarations->items()) < 1) {
            session()->flash('type', 'danger');
            session()->flash('message', $declarations);
            $declarations = [];
        }

        return view('home')->with(
            [
                'declarations'  => $declarations,
                'perPageValues' => explode(',', env('DECLARATIONS_PER_PAGE_VALUES')),
                'perPage'       => $perPage
            ]);
    }

    /**
     * Show the declaration.
     *
     * @param string  $code
     * @param Request $request
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function show(string $code, Request $request)
    {
        if ($request->session()->has('language')) {
            app()->setLocale($request->session()->get('language'));
            $countries = CountriesFacade::lookup($request->session()->get('language'));
        } else {
            $countries = CountriesFacade::lookup('ro_RO');
        }

        if ($code && $code !== 'undefined') {
            $declaration = Declaration::find(Declaration::API_DECLARATION_URL(), $code);

            if (!is_array($declaration)) {
                session()->flash('type', 'danger');
                session()->flash('message', $declaration);
                $formattedDeclaration['declaration'] = [];
            } else {
                $formattedDeclaration = Declaration::getDeclarationCollectionFormatted(
                    $declaration,
                    $countries,
                    app()->getLocale()
                );
            }

            if ($request->ajax()) { // Used for AJAX calls
                return response()->json([
                    'declaration' => $formattedDeclaration['declaration'],
                    'pdfData'     => json_encode($formattedDeclaration['pdf_data']),
                    'signature'   => $formattedDeclaration['signature'],
                    'qrCode'      => $formattedDeclaration['qr_code']
                ]);
            }

            return view(
                'declaration',
                [
                    'declaration' => $formattedDeclaration['declaration'],
                    'pdfData'     => json_encode($formattedDeclaration['pdf_data']),
                    'signature'   => $formattedDeclaration['signature'],
                    'qrCode'      => $formattedDeclaration['qr_code']
                ]
            );

        }

        session()->flash('type', 'danger');
        session()->flash('message', __('app.Declaration code is not defined'));
        return redirect()->back();
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
     * Change number of elements per page
     *
     * @param Request $request
     *
     * @return RedirectResponse|void
     */
    public function postElementsPerPage(Request $request)
    {
        if ($request->input('per-page')) {
            $request->session()->put('per-page', $request->input('per-page'));
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
    public function postSearchDeclaration(Request $request): ?JsonResponse
    {
        try {
            if ($request->input('code')) {
                $code = $this->sanitizeInput($request->input('code'), ' ');

                if (strpos($code, 'Er') === 0) {
                    throw new RuntimeException($code);
                }

                $declarations = Declaration::find(Declaration::API_DECLARATION_URL(), $code, true);

                if (!is_array($declarations)) {
                    throw new RuntimeException($declarations);
                }

                if (count($declarations) < 1) {
                    throw new RuntimeException(__('app.No declaration with this code'));
                }

                $errorsMessage = '';
                if (Auth::user()->username !== env('ADMIN_USER')) {
                    $checkpoint = Checkpoint::find(Checkpoint::API_BORDER_URL(), Auth::user()->checkpoint);
                    if ($checkpoint['is_dsp_before_border']) {
                        foreach ($declarations as $declaration) {
                            if ($declaration['dsp_validated_at'] && $declaration['dsp_user_name'] !== Auth::user()->username) {
                                $dspValidatedAt = Carbon::parse($declaration['border_validated_at'])->format('d-m-Y H:i:s');
                                $errorsMessage  .= __(
                                        'app.The declaration was validated at :dspValidatedAt by another DSP user [:userName].',
                                        [
                                            'dspValidatedAt' => $dspValidatedAt,
                                            'userName'       => $declaration['dsp_user_name']
                                        ]
                                    ) . '<br/ >';
                            }
                        }
                    } else {
                        foreach ($declarations as $declaration) {
                            if ($declaration['border_checkpoint']) {
                                if (Auth::user()->checkpoint !== $declaration['border_checkpoint']['id']) {
                                    $errorsMessage .= __('app.The person chose another border checkpoint.') . '<br/ >';
                                } else {
                                    if ($declaration['border_crossed_at'] && !$declaration['border_validated_at']) {
                                        $crossedAt     = Carbon::parse($declaration['border_crossed_at'])->format('d-m-Y H:i:s');
                                        $errorsMessage .= __(
                                                'app.The person crossed border checkpoint at :crossedAt but was not validated yet.',
                                                ['crossedAt' => $crossedAt]
                                            ) . '<br/ >';
                                    } else {
                                        if ($declaration['dsp_validated_at'] && $declaration['dsp_user_name'] !== Auth::user()->username) {
                                            $dspValidatedAt = Carbon::parse($declaration['border_validated_at'])->format('d-m-Y H:i:s');
                                            $errorsMessage  .= __(
                                                    'app.The declaration was validated at :dspValidatedAt by another DSP user [:userName].',
                                                    [
                                                        'dspValidatedAt' => $dspValidatedAt,
                                                        'userName'       => $declaration['dsp_user_name']
                                                    ]
                                                ) . '<br/ >';
                                        }
                                    }
                                }
                            } else {
                                $errorsMessage .=
                                    __('app.Declaration [:declarationCode] has no border', ['declarationCode' => $declaration['code']]) .
                                    '. ' . __('app.The person did not arrived at any border checkpoint.') . '<br/ >';

                            }
                        }
                    }
                }

                if ($errorsMessage === '') {
                    return response()->json(
                        [
                            'success' => $declarations
                        ]
                    );
                }

                throw new RuntimeException(trim($errorsMessage));
            }

            throw new RuntimeException(__('app.There is no code sent.'));
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
    public function sanitizeInput(string $inputValue, string $splitter = null): string
    {
        try {
            if ($inputValue === '') {
                throw new RuntimeException(__('app.No input for sanitize provided'));
            }

            if ($splitter) {
                $tmpArray = explode($splitter, filter_var(trim($inputValue), FILTER_SANITIZE_STRING));
                return $tmpArray[0];
            }

            return filter_var(trim($inputValue), FILTER_SANITIZE_STRING);

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
    public function postRegisterDeclaration(Request $request): ?JsonResponse
    {
        try {
            if ($request->input('code')) {
                $code              = $request->input('code');
                $dspMeasure        = $request->input('measure');
                $isDspBeforeBorder = $request->input('is_dsp');
                $userName          = Auth::user()->username;
                $errorsMessage     = '';

                if (!$dspMeasure) {
                    throw new RuntimeException(__('app.DSP measure error'));
                }

                $registerDeclaration = Declaration::registerDeclaration(
                    Declaration::API_DECLARATION_URL(),
                    $code,
                    $userName,
                    $dspMeasure,
                    $isDspBeforeBorder
                );

                if ($registerDeclaration !== 'success') {
                    $errorsMessage .= $registerDeclaration;
                }

                if ($errorsMessage === '') {
                    return response()->json(
                        [
                            'success' => $registerDeclaration,
                            'measure' => $dspMeasure
                        ]
                    );
                }

                throw new RuntimeException(trim($errorsMessage));
            }

            throw new RuntimeException(__('app.There is no code sent.'));
        } catch (Exception $exception) {
            return response()->json(
                [
                    'error' => $exception->getMessage()
                ]
            );
        }
    }
}
