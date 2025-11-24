<?php

namespace App\Models;

use App\Services\DateUtils;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Calendar extends Model implements AdminModelInterface
{
    use AdminModelTrait;

    protected $fillable = [
        'start_date',
        'end_date',
        'closing_date',
        'morning_start_time',
        'morning_end_time',
        'morning_slots',
        'afternoon_start_time',
        'afternoon_end_time',
        'afternoon_slots',
        'workshop_id',
    ];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d',
        'end_date' => 'datetime:Y-m-d',
        'closing_date' => 'datetime:Y-m-d',
        'morning_start_time' => 'datetime:H:i',
        'morning_end_time' => 'datetime:H:i',
        'afternoon_start_time' => 'datetime:H:i',
        'afternoon_end_time' => 'datetime:H:i',
    ];

    public function __construct( array $attributes = [] )
    {
        parent::__construct( $attributes );
        // Establece los valores por defecto si no están ya establecidos.
        $this->attributes = array_merge( [
            'start_date' => Carbon::createFromTimestamp( strtotime( 'first day of September this year' ), config( 'app.timezone' ) ),
            'end_date' => Carbon::createFromTimestamp( strtotime( 'last day of August next year' ), config( 'app.timezone' ) ),
            'closing_date' => Carbon::createFromTimestamp( strtotime( 'last day of September this year' ), config( 'app.timezone' ) ),
            'morning_start_time' => Carbon::createFromTimeString( '09:45' ),
            'morning_end_time' => Carbon::createFromTimeString( '13:30' ),
            'morning_slots' => 8,
            'afternoon_start_time' => Carbon::createFromTimeString( '16:45' ),
            'afternoon_end_time' => Carbon::createFromTimeString( '20:30' ),
            'afternoon_slots' => 8,
        ], $this->attributes );
    }

    function workshop(): BelongsTo
    {
        return $this->belongsTo( Workshop::class );
    }

    static function getIndexDefinitions(): array
    {
        return [
            'start_date_formatted_es' => [
                'label' => 'Desde',
            ],
            'end_date_formatted_es' => [
                'label' => 'Hasta',
            ],
            'closing_date_formatted_es' => [
                'label' => 'Cierre',
            ],
            'morning_slots' => [
                'label' => 'Huecos mañanas'
            ],
            'afternoon_slots' => [
                'label' => 'Huecos tardes'
            ],
            'workshop' => [
                'type' => 'relation',
                'label' => 'Taller',
            ],
        ];
    }

    static function getCreateFormDefinitions(): array
    {
        return [
            'workshop_id' => [
                'label' => 'Taller',
                'type' => 'select',
                'placeholder' => 'Elige un taller',
                'options' => fn() => Workshop::all()->pluck( 'name', 'id' ),
            ],
            'start_date_formatted' => [
                'type' => 'date',
                'label' => 'Desde',
                'name' => 'start_date',
                'validation' => [ 'before:end_date' ],
                'cols' => 1,
            ],
            'end_date_formatted' => [
                'type' => 'date',
                'label' => 'Hasta',
                'name' => 'end_date',
                'validation' => [ 'after:start_date' ],
                'cols' => 1,
            ],
            'closing_date_formatted' => [
                'type' => 'date',
                'label' => 'Cierre',
                'name' => 'closing_date',
                'validation' => [ 'after_or_equal:start_date', 'before_or_equal:end_date' ],
                'cols' => 1,
            ],
            ...Schedule::getCreateFormDefinitions(),
        ];
    }

    static function getFilterFields(): array
    {
        return [
            'start_date' => [
                'type' => 'date',
                'label' => 'Fecha de apertura',
            ],
            'workshop_id' => [
                'type' => 'relation',
                'label' => 'Taller',
                'options' => fn() => Workshop::has( 'calendars' ),
            ],
            'created_at' => [
                'type' => 'date',
                'label' => 'Creado el'
            ],
        ];
    }

    static function getMassiveAssignmentFields(): array
    {
        return [
            'closing_date' => [
                'type' => 'date',
                'label' => 'Cierre'
            ],
            'morning_slots' => [
                'type' => 'number',
                'label' => 'Huecos mañanas',
                'validation' => [ 'integer', 'gte:2' ],
            ],
            'afternoon_slots' => [
                'type' => 'number',
                'label' => 'Huecos tardes',
                'validation' => [ 'integer', 'gte:2' ],
            ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Calendario';
    }

    static function getPluralName(): string
    {
        return 'Calendarios';
    }

    function schedules(): HasMany
    {
        return $this->hasMany( Schedule::class );
    }

    function scopeNewest( $query )
    {
        return $query->whereYear( 'start_date', '<=', now()->year )->whereYear( 'end_date', '>=', now()->year );
    }

    private function updateSchedules(): void
    {
        $allSchedules = $this->schedules()
            ->withCount( 'appointments' )
            ->get()
            ->keyBy( fn( $s ) => $s->schedule_date_formatted );

        // Obtenemos los valores del calendario para los campos en común con el horario.
        $commonFields = array_fill_keys( array_intersect( $this->getFillable(), ( new Schedule() )->getFillable() ), null );
        array_walk( $commonFields, fn( &$value, $key ) => $value = $this->{$key} );
        // Para el rango de fechas, creamos o actualizamos los días.
        foreach ( CarbonPeriod::create( $this->start_date, $this->end_date ) as $date ) {
            $date = $date->format( 'Y-m-d' );
            // Si no existe para ese día, creamos el schedule. Si ya existía y no tiene citas aún, podemos actualizarlo.
            /** @var Schedule|null $schedule */
            if ( !( $schedule = $allSchedules->get( $date ) ) ) {
                $this->schedules()->create( [ 'schedule_date' => $date, ...$commonFields ] );
            } else {
                // Solo tocamos los campos del horario si realmente han cambiado en el formulario de edición del calendario.
                if ( $changes = array_intersect_key( $commonFields, $this->getChanges() ) ) {
                    $schedule->update( $changes ); // La validación de appointments_count se realiza en el modelo Schedule.
                }
            }
        }
    }

    protected static function booted(): void
    {
        parent::booted();

        static::saved( function ( Calendar $calendar ) {
            $calendar->updateSchedules();
        } );
    }

//    function resolveRouteBinding( $value, $field = null )
//    {
//        return $this->with( 'schedules.appointments' )
//            ->where( $field ?? $this->getRouteKeyName(), $value )
//            ->firstOrFail();
//    }

    function getActualClosingDateAttribute(): Carbon
    {
        $degreeClosingDate = Student::getCurrent()?->degree?->closing_date;
        // La ajustamos para que no se pueda elegir una fecha anterior a la fecha de apertura o posterior a la fecha de cierre del calendario.
        if ( $degreeClosingDate ) {
            $degreeClosingDate = $this->start_date->max( $degreeClosingDate )->min( $this->end_date );
        }

        return $degreeClosingDate ?? $this->closing_date;
    }

    function JSONize(): array
    {
        $schedules = $this->schedules()->chaperone( 'calendar' )->with( 'appointments.user' )->get()->keyBy( fn( $s ) => $s->schedule_date_formatted );

        $json = [
            'id' => $this->id,
            'start_date' => $this->start_date_formatted,
            'end_date' => $this->end_date_formatted,
            'closing_date' => $this->closing_date_formatted,
            'actual_closing_date' => $this->actual_closing_date_formatted,
        ];

        /** @var Schedule $schedule */
        foreach ( $schedules as $day => $schedule ) {
            $appointments = $schedule->generateAppointments();

            $json['schedules'][ $day ] = [
                'id' => $schedule->id,
                'schedule_date' => $schedule->schedule_date_formatted,
                'isBookable' => $schedule->isBookable(),
                'isHoliday' => DateUtils::isHoliday( $schedule->schedule_date ),
                'hasOwn' => array_filter( $appointments, fn( $a ) => $a['isOwn'] ) !== [],
                'appointments' => $appointments
            ];

            foreach ( [ 'morning', 'afternoon' ] as $group ) {
                $json['schedules'][ $day ]["is{$group}Full"] = array_filter( $appointments, fn( $a ) => $a['group'] === $group && $a['user_id'] ) === $schedule->{$group . '_slots'};
            }
        }

        return $json;
    }

    function __toString(): string
    {
        return sprintf( '%s/%s (#%s)', $this->start_date->format( 'y' ), $this->end_date->format( 'y' ), $this->workshop->code ?? $this->workshop_id );
    }
}
