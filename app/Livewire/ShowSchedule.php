<?php

namespace App\Livewire;

use App\Exceptions\DomainException;
use App\Models\Schedule;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class ShowSchedule extends Component
{
    public ?Schedule $schedule = null;

    /**
     * @throws Throwable
     */
    public function unbook( string $time ): void
    {
        try {
            DB::transaction( function () use ( $time ) {
                $user = auth()->user();

//                if ( $user->student?->canBook() === false ) {
                if ( $user->canBook() === false ) {
                    throw new DomainException( __( 'Tu cita es en breve y no es posible cambiarla. Si lo necesitas contacta con MIMOSA.' ) );
                }

                $appointments = $this->schedule?->appointments->keyBy( fn( $a ) => $a->appointment_time_formatted );
                // Por precaución, comprobamos que la cita a anular sea del usuario registrado.
                $appointment = $appointments[ $time ];
                if ( $appointment->user_id !== $user->id ) {
                    throw new DomainException( sprintf( __( 'La cita a las %s no pertenece a tu usuario.' ), $time ) );
                }

                $appointment->delete();
                // Recargamos la colección para que el JSONize a continuación refleje los cambios.
                $this->schedule->load( 'appointments' );

                $this->dispatch( 'status-message', status: sprintf( __( 'Tu cita a las %s fue anulada con éxito.' ), $time ) );
                $this->dispatch( 'unbook', calendar_id: $this->schedule->calendar_id, schedules: $this->schedule->calendar->JSONize()['schedules'] );
            } );
        } catch ( DomainException $e ) {
            $this->dispatch( 'status-message', status: $e->getMessage(), variant: 'danger' );
        } catch ( Throwable $e ) {
            $this->dispatch( 'status-message', status: __( 'Hubo un problema al anular tu cita. Inténtalo de nuevo en unos minutos.' ), variant: 'danger' );
        }
    }

    /**
     * @throws Throwable
     */
    public function book( string $time ): void
    {
        try {
            DB::transaction( function () use ( $time ) {
                // Bloqueamos el Schedule para evitar que otros lo actualicen mientras se genera la cita.
                $this->schedule = Schedule::with( 'appointments' )->lockForUpdate()->find( $this->schedule?->id );

                if ( !$this->schedule?->isBookable() ) {
                    throw new DomainException( sprintf( __( 'El día %s ya no está disponible. Por favor, actualiza la página.' ), $this->schedule->schedule_date_formatted_es ) );
                }

                $user = auth()->user();
                // Comprobamos que el alumno puede reservar la cita o mover la que ya tenía.
//                if ( $user->student?->canBook() === false ) {
                if ( $user->canBook() === false ) {
                    throw new DomainException( __( 'Tu cita es en breve y no es posible cambiarla. Si lo necesitas contacta con MIMOSA.' ) );
                }
                // No creo que sea necesario bloquear las citas, ya que el único punto de reserva es a través de este componente.
                $appointments = $this->schedule->generateAppointments();

                // Comprobamos que la cita no esté reservada por otro usuario.
                $appointment = $appointments[ $time ];
                if ( !$appointment['isEnabled'] ) { // ¿No está disponible por alguna razón?
                    if ( $appointment['user_id'] ) {
                        throw new DomainException( sprintf( __( 'La cita a las %s ya está reservada por otro usuario.' ), $time ) );
                    } else {
                        throw new DomainException( sprintf( __( 'La cita a las %s ya no está disponible.' ), $time ) );
                    }
                }

                $this->schedule->appointments()->create( [ 'appointment_time' => $time, 'user_id' => $user->id ] );
                // Recargamos la colección para que el JSONize a continuación refleje los cambios.
                $this->schedule->load( 'appointments' );

                $this->dispatch( 'status-message', status: sprintf( __( 'Tu cita a las %s fue reservada con éxito.' ), $time ) );
                $this->dispatch( 'book', calendar_id: $this->schedule->calendar_id, schedules: $this->schedule->calendar->JSONize()['schedules'] );
            } );
        } catch ( DomainException $e ) {
            $this->dispatch( 'status-message', status: $e->getMessage(), variant: 'danger' );
        } catch ( Throwable $e ) {
            $this->dispatch( 'status-message', status: __( 'Hubo un problema al reservar tu cita. Inténtalo de nuevo en unos minutos.' ), variant: 'danger' );
        }
    }

    public function render(): View
    {
        $appointments = $this->schedule?->generateAppointments() ?? [];
        $date = $this->schedule?->schedule_date_formatted_es;

        return view( 'livewire.show-schedule', [
            'date' => $date,
            'workshop_name' => $this->schedule?->calendar->workshop->name ?? '',
            'morning' => array_filter( $appointments, fn( $a ) => $a['group'] === 'morning' ),
            'afternoon' => array_filter( $appointments, fn( $a ) => $a['group'] === 'afternoon' ),
        ] );
    }

    #[On( 'date-click' )]
    public function onDateClick( int $calendar_id, string $date ): void
    {
        $this->schedule = Schedule::with( 'appointments' )
            ->where( [ 'schedule_date' => $date, 'calendar_id' => $calendar_id ] )
            ->first();
    }
}
