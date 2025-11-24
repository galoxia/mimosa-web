<?php

namespace App\Http\Controllers\Admin;

use App\Models\Degree;
use App\Models\Institution;
use App\Models\Message;
use App\Models\Teacher;
use App\Models\Teaching;

final class TeacherCRUDController extends EntityCRUDController
{
    protected function filterShowCreateData( array $data ): array
    {
        $institutions = Institution::all();
        $degrees = Degree::orderBy( 'name' )->get()->groupBy( 'institution_id' );
        $tickets = Message::ofType( Message::TEACHER_TICKET )->get();

        return array_merge( $data, compact( 'tickets', 'degrees', 'institutions' ) );
    }

    protected function filterShowUpdateData( array $data ): array
    {
        return array_merge( $this->filterShowCreateData( $data ), [
            //...
        ] );
    }

    /**
     * @param Teacher $entity
     */
    protected function saved( $entity, $validated )
    {
        // Guardamos los datos de la docencia
        if ( $validated['degree_id'] ?? null ) {
            $entity->teachings()->create( [
                'degree_id' => $validated['degree_id'],
            ] );
        }
    }
}
