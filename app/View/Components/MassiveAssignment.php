<?php

namespace App\View\Components;

use App\Models\AdminModelInterface;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MassiveAssignment extends Component
{
    public array $fields = [];

    /**
     * @param class-string<AdminModelInterface> $model
     */
    public function __construct( string $model )
    {
        $fields = $model::getMassiveAssignmentFields();

        foreach ( $fields as $field => $config ) {
            $options = $config['options'] ?? null;
            if ( is_callable( $options ) ) {
                $options = $options()->get()->keyBy( 'id' )->all();
            }

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
