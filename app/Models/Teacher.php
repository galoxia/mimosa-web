<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class Teacher extends Model implements AdminModelInterface
{
    use AdminModelTrait;

    protected $fillable = [
        'name',
        'surname1',
        'surname2',
        'teacher_number',
        'observations',
    ];

    protected $casts = [
        'teacher_number' => 'integer',
    ];

    protected $appends = [
        'degrees',
//        'oldest_degree'
    ];

    protected static array $collections = [
        Teaching::class
    ];

    function getFullNameAttribute(): string
    {
        return implode( ' ', array_filter( [ $this->name, $this->surname1, $this->surname2 ] ) );
    }

    public function teachings(): HasMany
    {
        return $this->hasMany( Teaching::class );
    }

//    public function getOldestDegreeAttribute(): ?Degree
//    {
//        return $this->teachings()->oldest()?->degree;
//    }

    static function getIndexDefinitions(): array
    {
        return [
            'teacher_number' => [ 'label' => 'Número' ],
            'name' => [ 'label' => 'Nombre' ],
            'surname1' => [ 'label' => 'Apellido1' ],
            'surname2' => [ 'label' => 'Apellido2' ],
            'degrees' => [
                'label' => 'Titulaciones',
                'getter' => fn( Teacher $teacher ) => $teacher->degrees->map( fn( $degree ) => $degree->name )->implode( ', ' ),
            ],
        ];
    }

    function __toString(): string
    {
        return $this->full_name;
    }

    static function getCreateFormDefinitions(): array
    {
        return [
            '_all' => [
                'sections' => 3,
                'cols' => 2,
                'alpine' => 'teacherForm',
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
                'footer' => '<p class="crud-field-footer text-muted text-sm mt-2" x-ref="degree_id_footer"></p>',
            ],
            'teacher_number' => [
                'type' => 'number',
                'label' => 'Número',
                'section' => 1,
                'attributes' => [ 'min' => 1, 'inputmode' => 'numeric', 'x-ref' => 'teacher_number', 'x-on:input' => 'refreshPreviewDelayed()' ],
                'validation' => [ 'integer', 'gte:1', 'unique:teachers,teacher_number' ],
                'footer' => '<p class="crud-field-footer text-muted text-sm mt-2" x-ref="teacher_number_footer"></p>',
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
            'observations' => [
                'type' => 'textarea',
                'label' => 'Observaciones',
                'required' => false,
                'attributes' => [ 'rows' => 4, 'x-ref' => 'observations', 'x-on:input' => 'refreshPreviewDelayed()' ],
            ],
        ];
    }

    function getUpdateFormDefinitions(): array
    {
        $definitions = self::getCreateFormDefinitions();

        $definitions['teacher_number']['validation'] = [ 'integer', 'gte:1', 'unique:teachers,teacher_number,' . $this->id ];
        $definitions['institution_id']['required'] = false;
        $definitions['degree_id']['attributes']['x-on:change'] = '"await loadDegree(); refreshPreviewDelayed()"';
        $definitions['degree_id']['required'] = false;
        $definitions['degree_id']['validation'][] = Rule::unique( 'teachings' )->where( 'teacher_id', $this->id );

        return $definitions;
    }

    public function getDegreesAttribute(): Collection
    {
        return $this->teachings()->get()->pluck( 'degree' );
    }

//    public function getDegreeIdsAttribute(): array
//    {
//        return $this->teachings()->pluck( 'degree_id' )->all();
//    }

    static function getFilterFields(): array
    {
        return [
            'name' => [ 'label' => 'Nombre' ],
            'surname1' => [ 'label' => 'Apellido1' ],
            'surname2' => [ 'label' => 'Apellido2' ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Profesor';
    }

    static function getPluralName(): string
    {
        return 'Profesores';
    }
}
