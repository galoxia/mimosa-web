<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teaching extends Model implements AdminModelInterface
{
    use AdminModelTrait;

    protected $table = 'teachings';

    public $incrementing = true; // Por tener ID autoincremental

    protected $fillable = [
        'teacher_id',
        'degree_id',
    ];

    static function getIndexDefinitions(): array
    {
        return [
            'degree' => [
                'type' => 'relation',
                'label' => 'Titulación'
            ],
            'teacher' => [
                'type' => 'relation',
                'label' => 'Profesor',
            ],
        ];
    }

//    static function getCreateFormDefinitions(): array
//    {
//        return [
//            'institution_id' => [
//                'type' => 'select',
//                'label' => 'Centro de estudios',
//                'placeholder' => 'Elige el centro de estudios',
//                'validation' => [ 'exists:institutions,id' ],
//            ],
//            'degree_id' => [
//                'type' => 'select',
//                'label' => 'Titulación',
//                'placeholder' => 'Elige la titulación',
//                'validation' => [ 'exists_with_foreign_keys:degrees,id,institution_id' ],
//            ],
//            'teacher_id' => [
//                'label' => 'Profesor',
//                'type' => 'select',
//                'placeholder' => 'Elige un profesor',
//                'options' => Teacher::all()->pluck( 'name', 'id' ),
//            ],
//        ];
//    }

//    function isUpdatable(): bool
//    {
//        return false;
//    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo( Teacher::class );
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo( Degree::class );
    }

    static function getSingularName(): string
    {
        return 'Docencia';
    }

    static function getPluralName(): string
    {
        return 'Docencias';
    }
}
