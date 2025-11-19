<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Institution extends Model implements AdminModelInterface
{
    use AdminModelTrait;

    protected $fillable = [
        'name',
    ];

    protected static array $collections = [
        Degree::class
    ];

    function degrees(): HasMany
    {
        return $this->hasMany( Degree::class );
    }

    function students(): HasMany
    {
        return $this->hasMany( Student::class );
    }

    static function getIndexDefinitions(): array
    {
        return [
//            'id' => [
//                'label' => '#ID',
//            ],
            'name' => [
                'label' => 'Nombre'
            ],
            'degrees_count' => [
                'type' => 'collection',
                'label' => '#Carreras'
            ],
            'created_at_formatted_es' => [
                'type' => 'date',
                'label' => 'Creada el',
            ],
        ];
    }

    static function getCreateFormDefinitions(): array
    {
        return [
            'name' => [
                'label' => 'Nombre',
                'autocomplete' => 'organization',
            ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Centro';
    }

    static function getPluralName(): string
    {
        return 'Centros';
    }

    function __toString(): string
    {
        return $this->name;
    }
}
