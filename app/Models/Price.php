<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Price extends Pivot implements AdminModelInterface
{
    use AdminModelTrait;

    protected $table = 'prices';

    public $incrementing = true; // Por tener ID autoincremental

    protected $fillable = [
        'product_id',
        'degree_id',
        'price',
        'discount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'integer',
    ];

    public function __construct( array $attributes = [] )
    {
        parent::__construct( $attributes );
        // Establece los valores por defecto si no están ya establecidos.
        $this->attributes = array_merge( [
            'discount' => 0,
        ], $this->attributes );
    }

    static function getIndexDefinitions(): array
    {
        return [
            'degree' => [
                'type' => 'relation',
                'label' => 'Titulación'
            ],
            'product' => [
                'type' => 'relation',
                'label' => 'Producto',
            ],
            'price' => [
                'label' => 'Precio',
                'format' => '%.2f€',
            ],
        ];
    }

    static function getCreateFormDefinitions(): array
    {
        return [
            'institution_id' => [
                'type' => 'select',
                'label' => 'Centro de estudios',
                'placeholder' => 'Elige el centro de estudios',
                'validation' => [ 'exists:institutions,id' ],
            ],
            'degree_id' => [
                'type' => 'select',
                'label' => 'Titulación',
                'placeholder' => 'Elige la titulación',
                'validation' => [ 'exists_with_foreign_keys:degrees,id,institution_id' ],
            ],
            'product_id' => [
                'label' => 'Producto',
                'type' => 'select',
                'placeholder' => 'Elige un producto',
                'options' => Product::all()->pluck( 'name', 'id' ),
            ],
            'price' => self::getFieldTemplates()['price'],
        ];
    }

    function getUpdateFormDefinitions(): array
    {
        return [
            'price' => self::getFieldTemplates()['price'],
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo( Product::class );
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo( Degree::class );
    }

    static function getSingularName(): string
    {
        return 'Precio';
    }

    static function getPluralName(): string
    {
        return 'Precios';
    }

    function __toString(): string
    {
        return sprintf( '%s de %s', $this->product, $this->degree );
    }
}
