<?php

namespace App\Models;

use App\Services\DateUtils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Student extends Model implements AdminModelInterface
{
    use AdminModelTrait;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'surname1',
        'surname2',
        'institution_id',
        'degree_id',
        'product_id',
        'student_number',
        'identification_number',
        'phone',
        'alt_phone',
        'observations',
        'single_marketing_consent',
        'group_marketing_consent',
        'is_delegate',
        'wants_photo_files',
        'wants_group_photos',
    ];

    protected $casts = [
        'single_marketing_consent' => 'boolean',
        'group_marketing_consent' => 'boolean',
        'is_delegate' => 'boolean',
        'wants_photo_files' => 'boolean',
        'wants_group_photos' => 'boolean',
        'student_number' => 'integer',
    ];

    protected static array $collections = [
        Payment::class
    ];

    public function __construct( array $attributes = [] )
    {
        parent::__construct( $attributes );
        // Establece los valores por defecto si no están ya establecidos.
        $this->attributes = array_merge( [
            'single_marketing_consent' => true,
            'group_marketing_consent' => true,
            'is_delegate' => false,
            'wants_photo_files' => false,
            'wants_group_photos' => false,
        ], $this->attributes );
    }

    function user(): BelongsTo
    {
        return $this->belongsTo( User::class );
    }

    function institution(): BelongsTo
    {
        return $this->belongsTo( Institution::class );
    }

    function degree(): BelongsTo
    {
        return $this->belongsTo( Degree::class );
    }

    function product(): BelongsTo
    {
        return $this->belongsTo( Product::class );
    }

    function payments(): HasMany
    {
        return $this->hasMany( Payment::class );
    }

    function getPaidAttribute(): float
    {
//        return $this->payments()->newest()->sum( 'amount' );
        return $this->payments()->ofDegree( $this->degree_id )->sum( 'amount' );
    }

//    function getProductIdAttribute(): ?int
//    {
////        return $this->payments()->newest()->sum( 'amount' );
//        return $this->payments()->ofDegree( $this->degree_id )->latest()->first()?->product_id;
//    }

    public function getCurrentPaymentsAttribute(): Collection
    {
        return $this->payments()->where( 'degree_id', $this->degree_id )->get();
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->user?->email
        );
    }

    function appointment(): HasOne
    {
        return $this->hasOne( Appointment::class, 'user_id', 'user_id' )->newest();
    }

    function getAppointmentAttribute(): ?Appointment
    {
//        return $this->user?->appointment;
        return $this->getRelationValue( 'appointment' );
    }

    function scopeCurrent( $query )
    {
        return $query->where( 'user_id', auth()->user()->id );
    }

    static function getCurrent(): ?Student
    {
        static $current = null;

        return $current ??= ( Student::current()->first() ?? new Student() );
    }

    function canBook(): bool
    {
        return $this->user->canBook();
    }

    function getFullNameAttribute(): string
    {
        return implode( ' ', array_filter( [ $this->name, $this->surname1, $this->surname2 ] ) );
    }

    function scopeNewest( $query )
    {
        return $query->where( function ( $query ) {
            $query
                ->whereYear( 'created_at', DateUtils::getAcademicYear() )
                ->orWhereHas( 'user.appointments', function ( $q ) {
                    $q->newest();
                } );
        } );
    }


//    static function filterIndexBuilder( array &$filters, $builder ): Builder
//    {
//        // Si estamos en una ventana de edición, entonces la tabla es un listado "hijo".
//        // En ese caso sí filtraremos (además) por los alumnos más recientes.
////        $editing = request( 'foreign_key' ) && request( 'foreign_id' );
//
////        if ( /*!$filters ||*/ $editing ) {
//        if ( !$filters ) {
//            $builder->newest();
//        }
//
//        return $builder;
//    }

    protected static function getIndexRelations(): array
    {
        return [ /*'institution',*/ 'degree', 'user', /*'user.appointments.schedule',*/ 'product' ];
    }

    static function getIndexDefinitions(): array
    {
        return [
            'student_number' => [ 'label' => 'Número' ],
            'name' => [ 'label' => 'Nombre' ],
            'surname1' => [ 'label' => 'Apellido1' ],
            'surname2' => [ 'label' => 'Apellido2' ],
            'degree' => [
                'type' => 'relation',
                'label' => 'Titulación',
            ],
            'product' => [
                'type' => 'relation',
                'label' => 'Producto',
            ],
            'appointment_date_formatted_es' => [
                'label' => 'Día',
                'getter' => fn( Student $student ) => $student->appointment?->schedule_date_formatted_es,
            ],
            'appointment_time_formatted' => [
                'label' => 'Hora',
                'getter' => fn( Student $student ) => $student->appointment?->appointment_time_formatted,
            ],
            'email' => [
                'label' => 'Email',
                'getter' => fn( Student $student ) => $student->email,
            ],
            'user' => [
                'type' => 'relation',
                'label' => 'Usuario',
            ],
            'is_delegate' => [
                'type' => 'bool',
                'label' => '¿Delegado?',
            ],
            'wants_photo_files' => [
                'type' => 'bool',
                'label' => '¿Archivos?',
            ],
            'wants_group_photos' => [
                'type' => 'bool',
                'label' => '¿Grupales?',
            ],
        ];
    }

    function __toString(): string
    {
        return $this->full_name;
    }

    static function getCreateFormDefinitions(): array
    {
        // El total del pago puede ser negativo (devoluciones).
        $total = [
            ...self::getFieldTemplates( 'Total' )['price'],
            'label' => '',
            'section' => -1, // Para que no se muestre en el form, pero sí quede definida la configuración.
            'attributes' => [ 'x-model' => 'total', 'step' => '0.01', 'inputmode' => 'decimal' ],
            'validation' => [ 'decimal:0,2' ],
        ];

        return [
            '_all' => [
                'sections' => 3, // 2 secciones diferenciadas de contenido
                'cols' => 2, // 2 columnas en la primera sección, 1 (por defecto) en la segunda. Sería equivalente a cols => '2,1'
                'alpine' => 'payment',
            ],
            'name' => [
                'label' => 'Nombre',
                'attributes' => [ 'x-ref' => 'name', 'x-on:input' => 'refreshPreviewDelayed()' ],
            ],
            'surname1' => [
                'label' => 'Apellido1',
                'cols' => 1,
                'attributes' => [ 'x-ref' => 'surname1', 'x-on:input' => 'refreshPreviewDelayed()' ],
            ],
            'surname2' => [
                'label' => 'Apellido2',
                'required' => false,
                'cols' => 1,
                'attributes' => [ 'x-ref' => 'surname2', 'x-on:input' => 'refreshPreviewDelayed()' ],
            ],
            'institution_id' => [
                'type' => 'select',
                'label' => 'Centro de estudios',
                'section' => 1,
                'placeholder' => 'Elige el centro de estudios',
                'validation' => [ 'exists:institutions,id' ],
            ],
            'degree_id' => [
                'type' => 'select',
                'label' => 'Titulación',
                'section' => 1,
                'placeholder' => 'Elige la titulación',
                'validation' => [ 'exists_with_foreign_keys:degrees,id,institution_id' ],
                'attributes' => [ 'x-ref' => 'degree_id', 'x-on:change' => 'onChangeDegree()' ],
            ],
            'student_number' => [
                'type' => 'number',
                'label' => 'Número',
                'section' => 1,
                'attributes' => [ 'min' => 1, 'inputmode' => 'numeric', 'x-ref' => 'student_number', 'x-on:input' => 'refreshPreviewDelayed()' ],
                'validation' => [ 'integer', 'gte:1', 'not_exists_with_foreign_keys:students,student_number,degree_id' ],
            ],
            'email' => [
                'type' => 'email',
                'label' => 'Email',
                'attributes' => [
                    'autocomplete' => 'email',
                ],
                'validation' => [ 'unique:users,email' ]
            ],
            'identification_number' => [
                'label' => 'DNI/NIF',
                'footer' => [
                    'class' => 'validation-error mt-2',
                ],
                'attributes' => [ 'x-ref' => 'identification_number', 'x-on:input' => 'refreshPreviewDelayed()' ],
            ],
            'phone' => [
                'type' => 'tel',
                'autocomplete' => 'tel',
                'label' => 'Teléfono/Móvil',
            ],
            'observations' => [
                'type' => 'textarea',
                'label' => 'Observaciones',
                'required' => false,
                'attributes' => [
                    'rows' => 4,
                ]
            ],
            'single_marketing_consent' => [
                ...self::getFieldTemplates( '¿Uso de fotos individuales?' )['bool'],
                'attributes' => [ 'x-ref' => 'single_marketing_consent', 'x-on:change' => 'refreshPreviewDelayed()' ],
            ],
            'is_delegate' => self::getFieldTemplates( '¿Delegado?' )['bool'],
            'wants_photo_files' => [
                ...self::getFieldTemplates( '¿Archivos digitales?' )['bool'],
                'attributes' => [ 'x-ref' => 'wants_photo_files', 'x-on:change' => 'refreshPreviewDelayed()' ],
            ],
            'wants_group_photos' => [
                ...self::getFieldTemplates( '¿Fotos grupales?' )['bool'],
                'attributes' => [ 'x-ref' => 'wants_group_photos', 'x-on:change' => 'refreshPreviewDelayed()' ],
            ],
            'product_id' => [
                'type' => 'select',
                'label' => 'Producto',
                'required' => false,
                'section' => 1,
                'placeholder' => 'Elige un producto',
                'validation' => [ 'exists:products,id' ],
                'options' => fn() => Product::all()->pluck( 'name', 'id' ),
                'attributes' => [ 'x-ref' => 'product_id', 'x-model' => 'product_id', 'x-on:change' => 'onChangeProduct()' ],
            ],
            'total' => $total,
        ];
    }

    function getUpdateFormDefinitions(): array
    {
        $definitions = self::getCreateFormDefinitions();

        $definitions['email']['validation'] = [ 'unique:users,email,' . $this->user_id ];
        $definitions['email']['component'] = 'shielded-input';
        $definitions['student_number']['validation'] = [
            'integer',
            'gte:1',
            'not_exists_with_foreign_keys:students,student_number,degree_id,' . $this->id,
        ];

        return $definitions;
    }

    static function getFilterFields(): array
    {
        return [
            'name' => [ 'label' => 'Nombre' ],
            'surname1' => [ 'label' => 'Apellido1' ],
            'surname2' => [ 'label' => 'Apellido2' ],
            'user#email' => [ 'label' => 'Email' ],
            'institution_id' => [
                'type' => 'relation',
                'label' => 'Centro de estudios',
                'options' => fn() => Institution::has( 'students' ),
            ],
            'degree_id' => [
                'type' => 'relation',
                'label' => 'Titulación',
                'options' => fn() => Degree::has( 'students' )->orderBy( 'name' ),
            ],
            'created_at' => [
                'type' => 'date',
                'label' => 'Creado el',
            ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Alumno';
    }

    static function getPluralName(): string
    {
        return 'Alumnos';
    }

//    protected function updateSearchText(): void
//    {
//        $tokens = array_unique( array_filter( [
//            $this->name,
//            $this->surname1,
//            $this->surname2,
//            $this->degree,
//            $this->product,
//            $this->email,
//            $this->user,
//        ] ) );
//
//        $this->search_text = implode( ' ', $tokens );
//    }
}
