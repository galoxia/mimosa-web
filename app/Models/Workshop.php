<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Workshop extends Model implements AdminModelInterface
{
    use AdminModelTrait;

    protected $fillable = [
        'name',
        'description',
        'code',
        'priority'
    ];

    protected static array $collections = [
        Calendar::class
    ];

    public function __construct( array $attributes = [] )
    {
        parent::__construct( $attributes );
        // Establece los valores por defecto si no están ya establecidos.
        $this->attributes = array_merge( [
            'priority' => 0,
        ], $this->attributes );
    }

    static function getIndexDefinitions(): array
    {
        return [
            'name' => [
                'label' => 'Nombre'
            ],
            'description' => [
                'label' => 'Descripción'
            ],
            'code' => [
                'label' => 'Código'
            ],
            'priority' => [
                'label' => 'Prioridad'
            ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Taller';
    }

    static function getPluralName(): string
    {
        return 'Talleres';
    }

    static function getCreateFormDefinitions(): array
    {
        return [
            'name' => [
                'label' => 'Nombre',
            ],
            'description' => [
                'label' => 'Descripción',
                'type' => 'textarea',
                'required' => false,
                'attributes' => [
                    'rows' => 4,
                ]
            ],
            'code' => [
                'label' => 'Código',
                'required' => false,
            ],
            'priority' => self::getFieldTemplates( max: 255 )['range'],
        ];
    }

    function calendars(): HasMany
    {
        return $this->hasMany( Calendar::class )->orderBy( 'created_at', 'desc' );
    }

    function scopeNewest( $query )
    {
        return $query->whereHas( 'calendars', fn( $q ) => $q->newest() )
            ->with( 'calendars', fn( $q ) => $q->newest() )
            ->orderBy( 'priority', 'desc' );
    }

    function __toString(): string
    {
        return $this->name;
    }
}
