<?php

namespace App\View\Components;

use App\Models\AdminModelInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class MassiveAssignment extends Component
{
    public array $fields = [];

//    private function getOptions( string $model, string $field, array $config ): array
//    {
//        $options = [];
//
//        $type = $config['type'] ?? 'text';
//        $ns = app()->getNamespace() . 'Models\\';
//
//        switch ( $type ) {
//            case 'boolean':
//                $options = [
//                    1 => 'Sí',
//                    0 => 'No',
//                ];
//                break;
//            case 'relation':
//                /** @var class-string<AdminModelInterface> $relatedModel */
//                $relatedModel = $ns . ucfirst( $field );
//                $relation = Str::plural( strtolower( str_replace( $ns, '', $model ) ) );
//
//                $options = $relatedModel::has( $relation )->get()->pluck( 'name', 'id' )->toArray();
//        }
//
//        return $options;
//    }

    /**
     * @param class-string<AdminModelInterface> $model
     */
    public function __construct( string $model )
    {
        $fields = $model::getMassiveAssignmentFields();

        foreach ( $fields as $field => $config ) {
            $options = ( $config['options'] ?? null )?->get()->keyBy( 'id' )->all();
//            if ( is_callable( $options ) ) {
//                $options = $options()->get()->keyBy( 'id' )->all();
//            }

            $this->fields[ $field ] = [
                ...$config,
                'options' => $options,
                'isSelect' => count( $options ?? [] ) > 0,
            ];
        }
    }

    public function render(): View
    {
        return view( 'components.massive-assignment' );
    }
}
