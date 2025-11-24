<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model implements AdminModelInterface
{
    use AdminModelTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount',
//        'priority',
        'concepts',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'integer',
//        'priority' => 'integer',
    ];

    protected static array $collections = [
        Price::class
    ];

    public function __construct( array $attributes = [] )
    {
        parent::__construct( $attributes );
        // Establece los valores por defecto si no están ya establecidos.
        $this->attributes = array_merge( [
//            'priority' => 0,
            'discount' => 0,
        ], $this->attributes );
    }

    public function prices(): HasMany
    {
        return $this->hasMany( Price::class );
    }

    static function getIndexDefinitions(): array
    {
        return [
            'name' => [
                'label' => 'Nombre'
            ],
            'description' => [
                'label' => 'Descripción',
            ],
            'price_formatted' => [
                'label' => 'Precio por defecto',
            ],
            'concept_count' => [
                'label' => '#Conceptos',
                'getter' => fn( Product $product ) => count( array_filter( preg_split( '/\r\n|\r|\n/', $product->concepts ?? '' ) ) ),
            ],
        ];
    }

    public function getPriceFormattedAttribute(): string
    {
        return sprintf( '%.2f€', $this->price );
    }

    protected function filterSearchTokens( array $tokens ): array
    {
        $tokens[] = implode( ' ', array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $this->concepts ?? '' ) ) ) );
        return $tokens;
    }

    static function getCreateFormDefinitions(): array
    {
        return [
            'name' => [
                'label' => 'Nombre'
            ],
            'description' => [
                'type' => 'textarea',
                'required' => false,
                'label' => 'Descripción',
                'attributes' => [
                    'rows' => 4,
                ],
            ],
            'price' => self::getFieldTemplates( label: 'Precio por defecto' )['price'],
//            'priority' => self::getFieldTemplates( max: 255 )['range'],
            'concepts' => [
                'type' => 'textarea',
                'label' => 'Conceptos',
                'footer' => [
                    'text' => 'Escribe cada concepto en una línea'
                ],
                'required' => false,
                'attributes' => [
                    'rows' => 8,
                ],
            ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Producto';
    }

    static function getPluralName(): string
    {
        return 'Productos';
    }

    function __toString(): string
    {
        return $this->name;
    }
}
