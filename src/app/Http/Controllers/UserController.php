<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
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
     * Display Reset Password view.
     *
     * @return Application|Factory|View
     */
    public function resetUserPassword()
    {
        if(Auth::user()->username != env('ADMIN_USER')) {
            return redirect()->route('home');
        }

        return view('reset_user_password', ['users' => User::where('username', '<>', env('ADMIN_USER'))->get()]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function postResetPassword(Request $request)
    {
        if(Auth::user()->username != env('ADMIN_USER')) {
            return redirect()->route('home');
        }

        try {
            if($request->input('user')) {
                $user = User::find($request->input('user'));
                if (is_null($user)) {
                    throw new Exception(__('app.There is no user in DB.'));
                }
                $user->password = Hash::make($user->generic_password);
                $user->save();

                session()->flash('type', 'success');
                session()->flash('message', __('app.Password for user :username successfully reset.',
                    ['username' => ucwords(str_replace('-', ' ', trim($user->username)))]
                ));
            } else {
                throw new Exception(__('app.There is no user selected.'));
            }
        } catch (Exception $exception) {
            session()->flash('type', 'danger');
            session()->flash('message', $exception->getMessage());
        }

        return redirect()->route('reset-password-user');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     */
    public function postResetAllPasswords(Request $request)
    {
        if(Auth::user()->username != env('ADMIN_USER')) {
            return redirect()->route('home');
        }

        try {
            Artisan::call( 'db:seed', [
                    '--class' => 'DspUsersSeeder',
                    '--force' => true ]
            );

            return response()->json([
                'success' => __('app.All passwords were reset.')
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        }
    }
}
