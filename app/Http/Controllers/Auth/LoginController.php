<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view( 'auth.login' );
    }

    /**
     * Handle a login request to the application.
     */
    public function login( Request $request )
    {
        $credentials = $request->validate( [
            'email' => [ 'required', 'email' ],
            'password' => [ 'required' ],
        ] );

        $remember = $request->boolean( 'remember' );

        if ( Auth::attempt( $credentials, $remember ) ) {
            $request->session()->regenerate();

//            return redirect()->intended(route('account.dashboard'));
            return redirect()->route( 'account.dashboard' );
        }

        return back()->withErrors( [
            'email' => 'El email o la contraseña no son correctos.',
        ] )->onlyInput( 'email' );
    }

    /**
     * Log the user out of the application.
     */
    public function logout( Request $request )
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect( route( 'login' ) );
    }
}
