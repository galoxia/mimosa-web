<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Institution;
use App\Models\Student;
use App\Models\Workshop;
use App\Services\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Throwable;
use function auth;

class AccountController extends Controller
{
    public function index( Request $request )
    {
        $workshops = Workshop::newest()->get()->pluck( null, 'id' );
        $student = Student::getCurrent();
//        $appointment = $student?->appointment;

        // Tenemos en cuenta si la carrera del estudiante filtra algún taller.
        if ( $workshop_ids = $student?->degree?->workshop_ids ) {
            $workshops = $workshops->filter( fn( $workshop ) => in_array( $workshop->id, $workshop_ids ) );
        }

//        $workshop = $workshops->get( $request->query( 'workshop_id', $appointment?->workshop_id ) ) ?? $workshops->first();
//        $calendar = $workshop?->calendars()->latest()->first();
        $calendars = [];
        foreach ( $workshops as $workshop ) {
            $calendars[] = $workshop->calendars->first();
        }

//        $options = $workshops->pluck( 'name', 'id' );

//        return view( 'account.dashboard', compact( 'calendar', 'options' ) );
        return view( 'account.dashboard', compact( 'calendars' ) );
    }

    public function showHelp()
    {
        return view( 'account.help' );
    }

    public function editProfile()
    {
        $institutions = Institution::all();
        $degrees = Degree::orderBy( 'name' )->get()->groupBy( 'institution_id' );

        return view( 'account.profile', compact( 'institutions', 'degrees' ) );
    }

    public function updateProfile( Request $request )
    {
        // Aquí siempre existirá un estudiante asociado al usuario.
        $student = auth()->user()->student;
        $degree_id = $request->post( 'degree_id' );
        $isImpersonating = app( 'impersonate' )->isImpersonating();

        $validated = $request->validate( [
            'name' => [ 'required', 'string', 'max:255' ],
            'surname1' => [ 'required', 'string', 'max:255' ],
            'surname2' => [ 'nullable', 'string', 'max:255' ],
            'institution_id' => [ 'required', 'exists:institutions,id' ],
            'degree_id' => [
                'required',
                'exists_with_foreign_keys:degrees,id,institution_id',
                function ( $attribute, $value, $fail ) use ( $student, $degree_id, $isImpersonating ) {
                    if ( !$isImpersonating && $student->degree_id !== (int)$degree_id && !$student->canBook() ) {
                        $fail( "Tu cita es en breve y no es posible cambiar la titulación." );
                    }
                },
            ],
            'identification_number' => [ 'required', 'string', 'max:255' ],
            'phone' => [ 'required', 'string', 'max:255' ],
            'single_marketing_consent' => [ 'required', 'in:0,1' ],
        ] );

        try {
            DB::transaction( function () use ( $student, $validated ) {
                $student = Student::lockForUpdate()->findOrFail( $student->id );
                // Si ha cambiado la titulación le asignamos el número de estudiante correspondiente.
                if ( (int)$validated['degree_id'] !== $student->degree_id ) {
                    $validated['student_number'] = Student::where( 'degree_id', $validated['degree_id'] )->max( 'student_number' ) + 1;
                    // Eliminamos la cita del estudiante para el año actual.
                    $student->appointment?->delete();
                }
                // Actualizamos con los datos validados.
                $student->update( $validated );
            } );

            Flash::success( __( 'Tus datos se han actualizado correctamente.' ) );
        } catch ( Throwable $e ) {
            Flash::error( __( 'Ocurrió un error al actualizar tu perfil. Por favor, inténtalo de nuevo en unos minutos.' ) );
        }

        return redirect()->route( 'account.profile.edit' );
    }

    public function updatePassword( Request $request )
    {
        $user = auth()->user();

        $validated = $request->validate( [
            'current_password' => [ 'required', 'current_password' ],
            'password' => [ 'required', 'confirmed', Password::defaults() ],
        ] );

        $user->password = Hash::make( $validated['password'] );
        $user->save();

        return redirect()->route( 'account.profile.edit' )->with( 'status', __( 'Tu contraseña se actualizó correctamente.' ) );
    }
}
