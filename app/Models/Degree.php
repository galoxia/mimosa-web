<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;


class Degree extends Model implements AdminModelInterface
{
    use AdminModelTrait;
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
        'closing_date',
        'institution_id',
        'workshop_ids',
        'min_teacher_number',
        'max_teacher_number',
    ];

    protected $casts = [
        'closing_date' => 'datetime:Y-m-d',
        'workshop_ids' => 'array',
        'min_teacher_number' => 'integer',
        'max_teacher_number' => 'integer',
    ];

    protected $appends = [ 'next_teacher_number' ];

    protected static array $collections = [
        Price::class
    ];

    function institution(): BelongsTo
    {
        return $this->belongsTo( Institution::class );
    }

    function students(): HasMany
    {
        return $this->hasMany( Student::class );
    }

    function teachings(): HasMany
    {
        return $this->hasMany( Teaching::class );
    }

    function getNextTeacherNumberAttribute(): int {
        $max = $this->teachers()->max( 'teacher_number' );
        return max( $max, $this->min_teacher_number - 1 ) + 1;
    }

    function teachers(): Collection
    {
        return $this->teachings()->get()->pluck( 'teacher' );
    }

    public function prices(): HasMany
    {
        return $this->hasMany( Price::class );
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
            'abbreviation' => [
                'label' => 'Abreviatura'
            ],
            'institution' => [
                'type' => 'relation',
                'label' => 'Centro',
            ],
            'min_teacher_number' => [
                'label' => 'Mínimo profesores'
            ],
            'max_teacher_number' => [
                'label' => 'Máximo profesores'
            ],
            'closing_date_formatted_es' => [
                'type' => 'date',
                'label' => 'Cierre',
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
            ],
            'abbreviation' => [
                'label' => 'Abreviatura',
            ],
            'institution_id' => [
                'label' => 'Centro',
                'type' => 'select',
                'placeholder' => 'Elige un centro de estudios',
                'options' => Institution::all()->pluck( 'name', 'id' ),
            ],
            'workshop_ids' => [
                'label' => 'Talleres',
                'type' => 'select',
                'attributes' => [
                    'multiple' => true,
                ],
                'required' => false,
                'options' => Workshop::all()->pluck( 'name', 'id' ),
                'validation' => [ '*' => [ 'integer', 'exists:workshops,id' ] ],
            ],
            'min_teacher_number' => [
                'type' => 'number',
                'required' => false,
                'label' => 'Mínimo profesores',
                'validation' => [ 'integer', 'gte:1' ],
                'attributes' => [ 'min' => 1, 'inputmode' => 'numeric' ],
            ],
            'max_teacher_number' => [
                'type' => 'number',
                'required' => false,
                'label' => 'Mínimo profesores',
                'validation' => [ 'integer', 'gt:min_teacher_number' ],
                'attributes' => [ 'min' => 2, 'inputmode' => 'numeric' ],
            ],
            'closing_date_formatted' => [
                'type' => 'date',
                'label' => 'Cierre',
                'name' => 'closing_date',
                'required' => false,
            ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Carrera';
    }

    static function getPluralName(): string
    {
        return 'Carreras';
    }

    function __toString(): string
    {
        return $this->name;
    }
}
