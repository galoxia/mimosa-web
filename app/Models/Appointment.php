<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


//class Appointment extends AdminModelInterface
class Appointment extends Model implements AdminModelInterface
{
    use AdminModelTrait;

    protected $fillable = [
        'appointment_time',
        'schedule_id',
        'user_id',
    ];

    protected $casts = [
        'appointment_time' => 'datetime:H:i',
    ];

    function schedule(): BelongsTo
    {
        return $this->belongsTo( Schedule::class );
    }

    protected function appointmentDate(): Attribute
    {
        return Attribute::make(
            get: fn(): Carbon => $this->schedule->schedule_date,
        );
    }

    protected function student(): Attribute
    {
        return Attribute::make(
            get: fn(): ?Student => $this->user->student,
        );
    }

    protected function studentDegree(): Attribute
    {
        return Attribute::make(
            get: fn(): ?Degree => $this->user->student?->degree,
        );
    }

    protected function userEmail(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->user->email,
        );
    }

    protected function studentPhone(): Attribute
    {
        return Attribute::make(
            get: fn(): ?string => $this->user->student?->phone,
        );
    }

    function user(): BelongsTo
    {
        return $this->belongsTo( User::class );
    }

//    function scopeOfCalendar( $query, $calendar_id )
//    {
//        return $query->whereHas( 'schedule.calendar', fn( $q ) => $q->where( 'id', $calendar_id ) );
//    }

    function getScheduleDateAttribute(): Carbon
    {
        return $this->schedule->schedule_date;
    }

    function getCalendarIdAttribute(): int
    {
        return $this->schedule->calendar_id;
    }

    function getWorkshopIdAttribute(): int
    {
        return $this->schedule->calendar->workshop_id;
    }

    function getWorkshopCodeAttribute(): string
    {
        return $this->schedule->calendar->workshop->code;
    }

    function scopeNewest( $query )
    {
        return $query->whereHas( 'schedule.calendar', fn( $q ) => $q->newest() );
    }

    static function getIndexRelations(): array
    {
        return [ 'schedule', 'user.student.degree' ];
    }

    static function getIndexDefinitions(): array
    {
        return [
            'appointment_time_formatted' => [
                'label' => 'Hora',
            ],
            'appointment_date_formatted_es' => [
                'label' => 'Día',
            ],
            'student' => [
                'type' => 'relation',
                'label' => 'Alumno',
            ],
            'user_email' => [
                'label' => 'Email',
                'orderable' => false,
            ],
            'student_phone' => [
                'label' => 'Teléfono',
                'orderable' => false,
            ],
            'student_degree' => [
                'type' => 'relation',
                'label' => 'Titulación',
                'orderable' => false,
            ],
        ];
    }

//    static function getDefaultFilters(): array
    static function filterIndexBuilder( array &$filters, $builder ): Builder
    {
        // Por defecto seleccionamos las citas del día de hoy
        if ( !$filters ) {
            $builder->whereHas( 'schedule', fn( $q ) => $q->whereDate( 'schedule_date', now() ) );
        }
        return $builder;
    }

    static function getFilterFields(): array
    {
        return [
            'schedule#schedule_date' => [
                'type' => 'date',
                'label' => 'Día'
            ],
            'user#student#degree_id' => [
                'type' => 'relation',
                'label' => 'Titulación',
                'options' => fn() => Degree::whereHas( 'students.user.appointments' )->orderBy( 'name' ),
            ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Cita';
    }

    static function getPluralName(): string
    {
        return 'Citas';
    }

    protected static function booted(): void
    {
        parent::booted();

        static::creating( function ( Appointment $appointment ) {
            // Si el usuario es Admin, puede generar todas las citas que quiera. Los estudiantes solo pueden generar una.
            $user = $appointment->user;
            if ( !$user->is_admin ) {
                $user->appointments()->delete();
            }
        } );
    }

    function __toString(): string
    {
        return sprintf(
            'día %s a las %s en %s',
            $this->schedule->schedule_date_formatted_es,
            $this->appointment_time_formatted,
            $this->schedule->calendar->workshop->name,
        );
    }

}
