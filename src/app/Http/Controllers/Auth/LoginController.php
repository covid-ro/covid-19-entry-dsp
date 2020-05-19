<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Overwrite login
     *
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'username' => 'required'
        ]);

        if(auth()->attempt(array('username' => $input['username'], 'password' => $input['password']))) {
            Cache::forget('declarations-' . Auth::user()->username);
            return redirect()->route('home');
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', __('auth.Username and/or Password are wrong.'));
            return redirect()->route('login')
                ->with('error', __('auth.Username and Password are wrong.'));
        }
    }
}
