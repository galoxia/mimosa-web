<?php

namespace App\Models;

use App\Services\DateUtils;
use App\Services\Flash;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Schedule extends Model implements AdminModelInterface
{
    use AdminModelTrait;

    protected $fillable = [
        'schedule_date',
        'morning_start_time',
        'morning_end_time',
        'morning_slots',
        'afternoon_start_time',
        'afternoon_end_time',
        'afternoon_slots',
        'calendar_id',
    ];

    protected $casts = [
        'schedule_date' => 'datetime:Y-m-d',
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
            'morning_start_time' => Carbon::createFromTimeString( '09:45' ),
            'morning_end_time' => Carbon::createFromTimeString( '13:30' ),
            'morning_slots' => 8,
            'afternoon_start_time' => Carbon::createFromTimeString( '16:45' ),
            'afternoon_end_time' => Carbon::createFromTimeString( '20:30' ),
            'afternoon_slots' => 8,
        ], $this->attributes );
    }

    function calendar(): BelongsTo
    {
        return $this->belongsTo( Calendar::class );
    }

    function appointments(): HasMany
    {
        return $this->hasMany( Appointment::class );
    }

    static function getIndexDefinitions(): array
    {
        return [
            'id' => [
                'label' => '#ID',
            ],
            'schedule_date_formatted_es' => [
                'type' => 'date',
                'label' => 'Día',
            ],
            'morning_start_time_formatted' => [
                'type' => 'time',
                'label' => 'Mañanas de',
            ],
            'morning_end_time_formatted' => [
                'type' => 'time',
                'label' => 'Mañanas a',
            ],
            'morning_slots' => [
                'label' => 'Huecos'
            ],
            'afternoon_start_time_formatted' => [
                'type' => 'time',
                'label' => 'Tardes de',
            ],
            'afternoon_end_time_formatted' => [
                'type' => 'time',
                'label' => 'Tardes a',
            ],
            'afternoon_slots' => [
                'label' => 'Huecos'
            ],
            'appointments_count' => [
                'type' => 'collection',
                'label' => '#Reservas'
            ],
            'calendar' => [
                'type' => 'relation',
                'label' => 'Calendario',
            ],
//            'created_at_formatted_es' => [
//                'type' => 'date',
//                'label' => 'Creado el',
//            ],
        ];
    }

    function isDeletable(): bool
    {
        return false;
    }

    static function isCreatable(): bool
    {
        return false;
    }

    static function getCreateFormDefinitions(): array
    {
        return [
            '_all' => [
                'cols' => 3,
            ],
            'morning_start_time_formatted' => [
                'type' => 'time',
                'label' => 'Hora inicio mañanas',
                'name' => 'morning_start_time',
                'validation' => [ 'before:morning_end_time' ],
                'cols' => 1,
            ],
            'morning_end_time_formatted' => [
                'type' => 'time',
                'label' => 'Hora fin mañanas',
                'name' => 'morning_end_time',
                'validation' => [ 'after:morning_start_time', 'before:afternoon_start_time' ],
                'cols' => 1,
            ],
            'morning_slots' => [
                'type' => 'number',
                'label' => 'Huecos mañanas',
                'validation' => [ 'integer', 'gte:2' ],
                'attributes' => [ 'min' => 2, 'inputmode' => 'numeric' ],
                'cols' => 1,
            ],
            'afternoon_start_time_formatted' => [
                'type' => 'time',
                'label' => 'Hora inicio tardes',
                'name' => 'afternoon_start_time',
                'validation' => [ 'after:morning_end_time', 'before:afternoon_end_time' ],
                'cols' => 1,
            ],
            'afternoon_end_time_formatted' => [
                'type' => 'time',
                'label' => 'Hora fin tardes',
                'name' => 'afternoon_end_time',
                'validation' => [ 'after:afternoon_start_time' ],
                'cols' => 1,
            ],
            'afternoon_slots' => [
                'type' => 'number',
                'label' => 'Huecos tardes',
                'validation' => [ 'integer', 'gte:2' ],
                'attributes' => [ 'min' => 2, 'inputmode' => 'numeric' ],
                'cols' => 1,
            ],
        ];
    }

    static function getMassiveAssignmentFields(): array
    {
        return [
            'morning_start_time' => [
                'type' => 'time',
                'label' => 'Mañanas de',
            ],
            'morning_end_time' => [
                'type' => 'time',
                'label' => 'Mañanas a',
            ],
            'morning_slots' => [
                'type' => 'number',
                'label' => 'Huecos mañanas',
                'validation' => [ 'integer', 'gte:2' ],
            ],
            'afternoon_start_time' => [
                'type' => 'time',
                'label' => 'Tardes de',
            ],
            'afternoon_end_time' => [
                'type' => 'time',
                'label' => 'Tardes a',
            ],
            'afternoon_slots' => [
                'type' => 'number',
                'label' => 'Huecos tardes',
                'validation' => [ 'integer', 'gte:2' ],
            ],
        ];
    }

    static function getFilterFields(): array
    {
        return [
            'schedule_date' => [
                'type' => 'date',
                'label' => 'Día',
            ],
            'calendar_id' => [
                'type' => 'relation',
                'label' => 'Calendario',
                'options' => Calendar::has( 'schedules' ),
            ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Horario';
    }

    static function getPluralName(): string
    {
        return 'Horarios';
    }

//    static function getDefaultFilters(): array
    static function filterIndexBuilder( array &$filters, $builder ): Builder
    {
        // Por defecto seleccionamos los horarios de los calendarios más recientes (deberían tener la misma fecha de inicio)
//        $calendars = Calendar::newest()->pluck( 'id' )->toArray();
//
//        return $calendars ? [ [ 'calendar_id', 'in', $calendars ] ] : [];
        if ( !$filters && ( $calendars = Calendar::newest()->pluck( 'id' )->toArray() ) ) {
            $builder->whereIn( 'calendar_id', $calendars );
        }
        return $builder;
    }

//    public function getIsOpenAttribute(): bool
//    {
//        return $this->schedule_date->betweenIncluded( $this->calendar->start_date, $this->calendar->closing_date );
//    }
//
//    public function getIsActuallyOpenAttribute(): bool
//    {
//        return $this->schedule_date->betweenIncluded( $this->calendar->start_date, $this->calendar->actual_closing_date );
//    }

    function isBookable(): bool
    {
//        $user = auth()->user();

        return
            $this->schedule_date->betweenIncluded( $this->calendar->start_date, $this->calendar->actual_closing_date ) &&
            $this->schedule_date->toDateString() >= now()->toDateString() && // No podemos reservar fechas pasadas
            !$this->schedule_date->isSunday() &&
            !DateUtils::isHoliday( $this->schedule_date )
//            &&
//            (
//                $user->is_admin || !$user->getAppointment() || ( !$this->schedule_date->isToday() & !$this->schedule_date->isTomorrow() )
//            )
            ;
    }

    function generateAppointments(): array
    {
        $current = $this->appointments->keyBy( fn( $a ) => $a->appointment_time_formatted );

        $appointments = [];
        foreach ( [ 'morning', 'afternoon' ] as $group ) {
            $slotCount = $this->{$group . '_slots'};
            $from = $this->schedule_date->copy()->setTimeFrom( $this->{$group . '_start_time'} );
            $to = $this->schedule_date->copy()->setTimeFrom( $this->{$group . '_end_time'} );
            $totalMinutes = $from->diffInMinutes( $to );
            $intervalMinutes = $totalMinutes / ( $slotCount - 1 );

            for ( $i = 0; $i < $slotCount; $i++ ) {
                // El último slot del grupo será el de la fecha $to
                $time = ( $i === $slotCount - 1 ) ? $to : $from;
                $timeHi = $time->format( 'H:i' );
                $user_id = $current->get( $timeHi )?->user->id;

                $appointments[ $timeHi ] = [
                    'isPast' => $time->isPast(),
                    'isOwn' => auth()->user()->id === $user_id,
                    'group' => $group,
                    'user_id' => $user_id,
                    'isEnabled' => !$time->isPast() && !$user_id
                ];

                $from = $from->addMinutes( $intervalMinutes );
            }
        }

        return $appointments;
    }

    protected static function booted(): void
    {
        parent::booted();

        static::updating( function ( Schedule $schedule ) {
//            if ( !isset( $schedule->appointments_count ) ) {
//                $schedule->loadCount( 'appointments' );
//            }
            $schedule->loadMissingCount( 'appointments' );

            if ( $schedule->appointments_count > 0 ) {
                Flash::warning( __( 'Algunos horarios tienen citas reservadas y no se pudieron modificar.' ) );
                return false;
            }

            return true;
        } );
    }

    function __toString(): string
    {
        return sprintf(
            'día %s en %s',
            $this->schedule_date_formatted_es,
            $this->calendar->workshop->name,
        );
    }
}
