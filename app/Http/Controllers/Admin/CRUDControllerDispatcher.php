<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminModelInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class CRUDControllerDispatcher
{
    private function getResponse( string $action, Request $request )
    {
        /** @var class-string<AdminModelInterface> $model */
        $model = $request->input( 'model' );
        if ( !$model ) {
            abort( 404, 'Página no encontrada' );
        }

        // Vemos si existe un controlador específico para el modelo
        $modelController = sprintf( "App\\Http\\Controllers\\Admin\\%sCRUDController", ucfirst( $model::getClassSlug() ) );
        $controllerClass = class_exists( $modelController ) ? $modelController : EntityCRUDController::class;

        $method = Str::camel( $action );

        return app( $controllerClass )->$method( $request );
    }

    public function get( Request $request, string $action )
    {
        if ( in_array( $action, [ 'update', 'create' ] ) ) {
            $action = "show-$action";
        }

        return $this->getResponse( $action, $request );
    }

    public function post( Request $request, string $action )
    {
        return $this->getResponse( $action, $request );
    }

    public function delete( Request $request )
    {
        return $this->getResponse( 'delete', $request );
    }
}
