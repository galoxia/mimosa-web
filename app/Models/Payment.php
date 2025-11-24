<?php

namespace App\Models;

use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model implements AdminModelInterface
{
    use AdminModelTrait;

    protected $fillable = [
        'student_id',
        'degree_id',
        'product_id',
        'product_name',
        'concepts',
        'price',
        'discount',
        'amount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

//    public function __construct( array $attributes = [] )
//    {
//        parent::__construct( $attributes );
//        // Establece los valores por defecto si no están ya establecidos.
//        $this->attributes = array_merge( [
//        ], $this->attributes );
//    }

    static function getIndexDefinitions(): array
    {
        return [
            'student' => [
                'type' => 'relation',
                'label' => 'Alumno'
            ],
            'degree' => [
                'type' => 'relation',
                'label' => 'Titulación'
            ],
            'product' => [
                'type' => 'relation',
                'label' => 'Producto',
            ],
            'price_formatted' => [
                'label' => 'Precio',
            ],
            'amount_formatted' => [
                'label' => 'Pagado',
            ],
            'created_at_formatted_es' => [
                'label' => 'Creado el',
            ],
        ];
    }

    public function getPriceFormattedAttribute(): string
    {
        return sprintf( '%.2f€', $this->price );
    }

    public function getAmountFormattedAttribute(): string
    {
        return sprintf( '%.2f€', $this->amount );
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo( Student::class );
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo( Degree::class );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo( Product::class );
    }

//    /**
//     * @throws Exception
//     */
//    function scopeNewest( $query )
//    {
//        $today = new DateTime( 'now', new DateTimeZone( 'Europe/Madrid' ) );
//        $year = $today->format( 'Y' );
//        $month = $today->format( 'n' );
//
//        return $query->whereYear( 'created_at', '=', $month >= 9 ? $year : $year - 1 );
//    }

    function scopeOfDegree( $query, ?int $degree_id )
    {
        return $query->where( 'degree_id', $degree_id );
    }

    static function getSingularName(): string
    {
        return 'Pago';
    }

    static function getPluralName(): string
    {
        return 'Pagos';
    }
}
