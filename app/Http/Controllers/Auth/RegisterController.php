<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Degree;
use App\Models\Institution;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        $institutions = Institution::all();
        $degrees = Degree::orderBy( 'name' )->get()->groupBy( 'institution_id' );

        return view( 'auth.register', compact( 'institutions', 'degrees' ) );
//        return view( 'auth.register' );
    }

    /**
     * Handle a registration request for the application.
     */
    public function register( Request $request )
    {
        $validated = $request->validate( [
            'name' => [ 'required', 'string', 'max:255' ],
            'surname1' => [ 'required', 'string', 'max:255' ],
            'surname2' => [ 'string', 'nullable', 'max:255' ],
            'email' => [ 'required', 'string', 'email', 'max:255', 'unique:users' ],
            'institution_id' => [ 'required', 'exists:institutions,id' ],
            'degree_id' => [ 'required', 'exists_with_foreign_keys:degrees,id,institution_id' ],
            'identification_number' => [ 'required', 'string', 'max:255' ],
            'phone' => [ 'required', 'string', 'max:255' ],
            'password' => [ 'required', 'confirmed', Password::defaults() ],
            'single_marketing_consent' => [ 'required', 'in:0,1' ],
            'group_marketing_consent' => 'accepted',
        ] );

        $user = User::create( [
            'name' => explode( '@', $validated['email'] )[0],
            'email' => $validated['email'],
            'password' => Hash::make( $validated['password'] ),
        ] );

        Student::create( [
            ...$validated,
            'user_id' => $user->id,
            'student_number' => Student::where( 'degree_id', $validated['degree_id'] )->max( 'student_number' ) + 1,
        ] );

        Auth::login( $user );

        return redirect()->route( 'account.dashboard' );
    }
}
