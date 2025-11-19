<?php

namespace App\Http\Controllers\Admin;

use App\Models\Degree;
use App\Models\Institution;

final class TeachingCRUDController extends EntityCRUDController
{
    protected function onForeignKey( string $foreign_key, string $foreign_id, array &$fields )
    {
        parent::onForeignKey( $foreign_key, $foreign_id, $fields );

        if ( $foreign_key === 'degree_id' ) {
            $fields['institution_id']['value'] = Degree::findOrFail( $foreign_id )->institution_id;
        }
    }

    protected function filterShowCreateData( array $data ): array
    {
        return array_merge( $data, [
            'institutions' => Institution::all(),
            'degrees' => Degree::orderBy( 'name' )->get()->groupBy( 'institution_id' ),
        ] );
    }
}
