<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Flash;
use Closure;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view( 'auth.passwords.email' );
    }

    public function sendResetLinkEmail( Request $request )
    {
        $request->validate( [ 'email' => 'required|email' ] );
        // Let's try to send the reset link email
        try {
            $status = Password::sendResetLink(
                $request->only( 'email' )
            );

//        $message = ( $status === Password::RESET_LINK_SENT ) ?
//            "Si $request->email existe, recibirás un correo con instrucciones para restablecer tu contraseña." :
//            "No se pudo enviar el correo. Por favor, inténtalo de nuevo pasados unos minutos.";
//            $message = "Si $request->email existe, recibirás un correo con instrucciones para restablecer tu contraseña.";
            Flash::success( "Si $request->email existe, recibirás un correo con instrucciones para restablecer tu contraseña." );
        } catch ( \Exception $e ) {
//            $message = "No se pudo enviar el correo. Por favor, inténtalo de nuevo pasados unos minutos.";
            Flash::error( "No se pudo enviar el correo. Por favor, inténtalo de nuevo pasados unos minutos." );
        }

//        return redirect()->route( 'login' )->with( 'status', $message );
        return redirect()->route( 'login' );
    }

    public function showResetForm( Request $request, $token = null )
    {
        return view( 'auth.passwords.reset' )->with(
            [ 'token' => $token, 'email' => $request->email ]
        );
    }

    public function reset( Request $request )
    {
        $request->validate( [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                // Validamos que la contraseña introducida sea distinta de la actual
                function ( string $attribute, mixed $value, Closure $fail ) use ( $request ) {
                    $user = User::where( 'email', $request->email )->first();

                    if ( $user && Hash::check( $value, $user->password ) ) {
                        $fail( __( 'validation.different_from_current_password', [ 'attribute' => $attribute ] ) );
                    }
                },
                \Illuminate\Validation\Rules\Password::defaults()
            ],
        ] );

        $status = Password::reset(
            $request->only( 'email', 'password', 'password_confirmation', 'token' ),
            function ( $user, $password ) {
                $user->forceFill( [
                    'password' => Hash::make( $password )
                ] )->setRememberToken( Str::random( 60 ) );

                $user->save();

                event( new PasswordReset( $user ) );
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route( 'login' )->with( 'status', __( $status ) )
            : back()->withErrors( [ 'email' => [ __( $status ) ] ] );
    }
}
