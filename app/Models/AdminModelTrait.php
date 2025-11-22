<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;

/**
 * @mixin Model
 * @mixin AdminModelInterface
 * @property int $id
 */
trait AdminModelTrait
{
    protected static function getFieldTemplates(
        string $label = null,
        bool   $required = true,
        int    $min = 0,
        int    $max = 100
    ): array
    {
        return [
            'price' => [
                'type' => 'number',
                'required' => $required,
                'label' => $label ?? 'Precio',
                'attributes' => [
                    'step' => '0.01',
                    'min' => 0,
                    'inputmode' => 'decimal',
                ],
                'validation' => [ 'decimal:0,2', 'gte:0' ],
                'setter' => fn( $value ) => str_replace( ',', '.', $value ),
            ],
            'range' => [
                'type' => 'number',
                'required' => $required,
                'label' => $label ?? '',
                'validation' => [ 'integer', "gte:$min", "lte:$max" ],
                'attributes' => [ 'min' => $min, 'max' => $max, 'inputmode' => 'numeric' ],
            ],
            'bool' => [
                'type' => 'select',
                'required' => $required,
                'label' => $label ?? '¿Aceptar?',
                'options' => [ 1 => 'Sí', 0 => 'No' ],
                'validation' => [ 'in:0,1' ],
            ],
        ];
    }

    function getCollections(): array
    {
        $collections = [];
        /** @var class-string<AdminModelInterface> $model */
        foreach ( self::$collections ?? [] as $model ) {
            $collections[] = [
                'model' => $model,
                'singularName' => $model::getSingularName(),
                'pluralName' => $model::getPluralName(),
                'creatable' => $model::isCreatable(),
                // Filtramos la tabla para que solo muestre los registros de la colección.
                'table' => $model::getIndexTable( [ [ $this->getForeignKey(), '=', $this->id ] ] ),
            ];
        }
        return $collections;
    }

    static function getClassSlug(): string
    {
        return Str::of( class_basename( self::class ) )->lower();
    }

    static function getFilterFields(): array
    {
        return [];
    }

    static function getMassiveAssignmentFields(): array
    {
        return [];
    }

    private static function andWhere( Builder $builder, string $field, string $operator, array $values ): void
    {
        $parts = explode( '#', $field );

        if ( count( $parts ) > 1 ) { // ¿Campo anidado? (ej. 'user#name' o 'product#brand#name')
            $relation = array_shift( $parts );
            $builder->whereHas( $relation, fn( $query ) => self::andWhere( $query, implode( '#', $parts ), $operator, $values ) );
        } else {
            // ¿El campo es de tipo fecha/hora?
            $parts = explode( '_', $field );
            $suffix = array_pop( $parts );
            $isDate = in_array( $suffix, [ 'date', 'at' ] );
            $isTime = $suffix === 'time';

            if ( $isDate ) {
                $values = array_map( fn( $value ) => Carbon::parse( $value )->format( 'Y-m-d' ), $values );
            } elseif ( $isTime ) {
                $values = array_map( fn( $value ) => Carbon::parse( $value )->format( 'H:i:s' ), $values );
            }

            switch ( $operator ) {
                case 'like':
                case 'not like':
                    $builder->where( $field, $operator, "%$values[0]%" );
                    break;
                case 'starts with':
                    $builder->where( $field, 'like', "$values[0]%" );
                    break;
                case 'not starts with':
                    $builder->where( $field, 'not like', "$values[0]%" );
                    break;
                case 'ends with':
                    $builder->where( $field, 'like', "%$values[0]" );
                    break;
                case 'not ends with':
                    $builder->where( $field, 'not like', "%$values[0]" );
                    break;
                case 'in':
                    $builder->whereIn( $field, $values );
                    break;
                case 'not in':
                    $builder->whereNotIn( $field, $values );
                    break;
                case 'any':
                    $builder->whereHas( $field, fn( $query ) => $query->whereIn( 'id', $values ) );
                    break;
                case 'every':
                    foreach ( $values as $value ) {
                        $builder->whereHas( $field, fn( $query ) => $query->where( 'id', $value ) );
                    }
                    break;
                case 'none':
                    $builder->whereDoesntHave( $field, fn( $query ) => $query->whereIn( 'id', $values ) );
                    break;
                case 'between':
                    if ( $isDate ) {
                        $builder->whereDate( $field, '>=', $values[0] )->whereDate( $field, '<=', $values[1] );
                    } elseif ( $isTime ) {
                        $builder->whereTime( $field, '>=', $values[0] )->whereTime( $field, '<=', $values[1] );
                    } else {
                        $builder->whereBetween( $field, $values );
                    }
                    break;
                case 'not between':
                    if ( $isDate ) {
                        $builder->where( function ( $query ) use ( $field, $values ) {
                            $query->whereDate( $field, '<', $values[0] )->orWhereDate( $field, '>', $values[1] );
                        } );
                    } elseif ( $isTime ) {
                        $builder->where( function ( $query ) use ( $field, $values ) {
                            $query->whereTime( $field, '<', $values[0] )->orWhereTime( $field, '>', $values[1] );
                        } );
                    } else {
                        $builder->whereNotBetween( $field, $values );
                    }
                    break;
                default:
                    if ( $isDate ) {
                        $builder->whereDate( $field, $operator, $values[0] );
                    } elseif ( $isTime ) {
                        $builder->whereTime( $field, $operator, $values[0] );
                    } else {
                        $builder->where( $field, $operator, $values[0] );
                    }
            }
        }
    }

    static function filterIndexBuilder( array &$filters, $builder ): Builder {
        return $builder;
    }

    static function getIndexBuilder( array $filters = [] ): Builder
    {
        $model = self::class;

        $builder = $model::with( $model::getIndexRelations() )->withCount( $model::getIndexCollections() );
        // Permite a las clases hijas redefinir $filters o añadir condiciones directamente al $builder.
        self::filterIndexBuilder( $filters, $builder );
        // Apply filters
        foreach ( $filters as $filter ) {
            list( $field, $operator, $values ) = $filter;
            self::andWhere( $builder, $field, $operator, is_array( $values ) ? $values : [ $values ] );
        }

        return $builder;
    }

//    static function getDefaultFilters(): array
//    {
//        return [];
//    }

    static function getIndexOrderBy(): array
    {
        return [ [ 'created_at', 'desc' ] ];
    }

    static function getIndexTable( array $filters = [] ): array
    {
        $model = self::class;
        $definitions = $model::getIndexDefinitions();
        $table = [
            'model' => $model,
            'headers' => array_combine( array_keys( $definitions ), array_column( $definitions, 'label' ) ),
            'slug' => $model::getClassSlug(),
            'rows' => []
        ];

        $builder = $model::getIndexBuilder( $filters /*?: self::getDefaultFilters()*/ );
        foreach ( $model::getIndexOrderBy() as $entry ) {
            $builder->orderBy( ...$entry );
        }
        /** @var Collection<int, AdminModelInterface> $entities */
        $entities = $builder
            ->get()
            ->filter( fn( $entity ) => $entity->isIndexable() );

        foreach ( $entities as $entity ) {
            $indexFields = $entity->getIndexFields();
            foreach ( $indexFields as &$config ) {
                // Aplicar formato si se ha definido en el campo. Solo tiene sentido en los campos del índice, por eso se hace aquí.
                if ( isset( $config['format'] ) ) {
                    $config['value'] = sprintf( $config['format'], $config['value'] );
                }
            }

            $table['rows'][] = [ 'entity' => $entity, 'fields' => $indexFields ];
        }

        return $table;
    }

    static function getCreateFormDefinitions(): array
    {
        return [];
    }

    static function getIndexDefinitions(): array
    {
        return [];
    }

    function getUpdateFormDefinitions(): array
    {
        return self::getCreateFormDefinitions();
    }

    function getUpdateFormFields(): array
    {
        $definitions = $this->getUpdateFormDefinitions();
        foreach ( $definitions as $field => &$config ) {
            if ( $field !== '_all' ) {
                $config['value'] = isset( $config['getter'] ) ? $config['getter']( $this->{$field} ) : $this->{$field};
            }
        }
        return $definitions;
    }

    static function getCreateFormFields(): array
    {
        $definitions = self::getCreateFormDefinitions();
        $entity = new self();
        foreach ( $definitions as $field => &$config ) {
            if ( $field !== '_all' ) {
                $config['value'] = isset( $config['getter'] ) ? $config['getter']( $entity->{$field} ) : $entity->{$field};
            }
        }
        return $definitions;
    }

    function getIndexFields(): array
    {
        $definitions = self::getIndexDefinitions();
        foreach ( $definitions as $field => &$config ) {
            if ( $field !== '_all' ) {
                $config['value'] = isset( $config['getter'] ) ? $config['getter']( $this ) : $this->{$field};
            }
        }
        return $definitions;
    }

    static protected function getIndexRelations(): array
    {
        return array_keys( array_filter( self::getIndexDefinitions(), fn( $field ) => 'relation' === ( $field['type'] ?? 'text' ) ) );
    }

    static protected function getIndexCollections(): array
    {
        $fields = array_keys( array_filter( self::getIndexDefinitions(), fn( $field ) => 'collection' === ( $field['type'] ?? 'text' ) ) );
        // Las colecciones terminan en '_count' (ej. 'students_count') -> se lo quitamos.
        return array_map( fn( $field ) => preg_replace( '/_count$/', '', $field ), $fields );
    }

    function isIndexable(): bool
    {
        return !empty( self::getIndexDefinitions() );
    }

    static function isCreatable(): bool
    {
        return !empty( self::getCreateFormDefinitions() );
    }

    function isUpdatable(): bool
    {
        return !empty( self::getUpdateFormDefinitions() );
    }

    function isDeletable(): bool
    {
        return true;
    }

    function __get( $key )
    {
        $parts = explode( '_', $key );

        // Procesamos los campos que tengan '_formatted' o '_formatted_xx' al final.
        $locale = null;
        if ( array_slice( $parts, -2 )[0] === 'formatted' ) {
            $locale = array_pop( $parts );
        }

        if ( 'formatted' === array_pop( $parts ) ) {
            $type = array_pop( $parts );

            $field = implode( '_', $parts ) . "_$type";

            if ( $this->{$field} instanceof Carbon ) {
                if ( in_array( $type, [ 'date', 'at' ] ) ) {
                    if ( 'es' === $locale ) {
                        return $this->{$field}->format( 'd/m/Y' );
                    } else {
                        return $this->{$field}->format( 'Y-m-d' );
                    }
                } elseif ( 'time' === $type ) {
                    return $this->{$field}->format( 'H:i' );
                }
            }
        }

        return $this->getAttribute( $key );
    }

    function loadMissingCount( array|string $relations ): static
    {
        $relations = is_array( $relations ) ? $relations : [ $relations ];
        // Filtra las relaciones que no tienen la cuenta ya cargada.
        $relations = array_filter( $relations, fn( $relation ) => !isset( $this->{"{$relation}_count"} ) );

        return $this->loadCount( $relations );
    }

    protected static function booted(): void
    {
        static::deleted( function ( $entity ) {
            $path = sprintf( '%s/%d', Str::plural( self::getClassSlug() ), $entity->id );
            Storage::deleteDirectory( $path );
        } );
    }

    /**
     * Método de conveniencia para convertir un modelo en un array serializable.
     *
     * @return array
     */
    function JSONize(): array
    {
        return [];
    }
}
