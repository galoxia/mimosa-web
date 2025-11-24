<?php

namespace App\View\Components;

use App\Models\AdminModelInterface;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Filters extends Component
{
    public array $filters = [];

    private function getOperators( string $type ): array
    {
        $operators = [
            '=' => 'Igual a',
            '!=' => 'Distinto de',
            '>' => 'Mayor que',
            '<' => 'Menor que',
            '>=' => 'Mayor o igual que',
            '<=' => 'Menor o igual que',
            'between' => 'Entre 2 valores',
            'not between' => 'Fuera de 2 valores',
        ];

        switch ( $type ) {
            case 'text':
                $operators = [
                    '=' => 'Igual a',
                    '!=' => 'Distinto de',
                    'like' => 'Contiene',
                    'not like' => 'No contiene',
                    'starts with' => 'Comienza con',
                    'not starts with' => 'No comienza con',
                    'ends with' => 'Termina con',
                    'not ends with' => 'No termina con',
                ];
                break;
            case 'relation':
                $operators = [
                    '=' => 'Igual a',
                    '!=' => 'Distinto de',
                ];
                break;
            case 'collection':
                $operators = [
                    'any' => 'Contiene alguno de',
                    'none' => 'No contiene ninguno de',
                    'every' => 'Contiene todos',
                ];
                break;
        }

        return $operators;
    }

    /**
     * @param class-string<AdminModelInterface> $model
     */
    public function __construct( string $model )
    {
        $current = session( "filters.$model.inputs", [] );

        $fields = $model::getFilterFields();

        foreach ( $fields as $field => $config ) {
            $type = $config['type'] ?? 'text';
            // $options = null indica un campo sin opciones, es decir un input en vez de un select.
            $options = $config['options'] ?? null;
            if ( is_callable( $options ) ) {
                $options = $options()->get()->keyBy( 'id' )->all();
            }

            $this->filters[ $field ] = [
                ...$config,
                'enabled' => isset( $current[ $field ]['enabled'] ),
                'operators' => [
                    'options' => $this->getOperators( $type ),
                    'selected' => $current[ $field ]['operator'] ?? null
                ],
                'values' => [
                    'options' => $options,
                    'current' => $current[ $field ]['values'] ?? [ null ],
                ]
            ];
        }
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function render(): View
    {
        return view( 'components.filters' );
    }
}
