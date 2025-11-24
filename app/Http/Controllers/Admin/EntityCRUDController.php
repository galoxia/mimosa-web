<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModelInterface;
use App\Models\Student;
use App\Services\Flash;
use Blade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Throwable;
use Validator;

class EntityCRUDController extends Controller
{
    public function index( Request $request )
    {
        /** @var class-string<AdminModelInterface> $model */
        $model = $request->query( 'model' );

        if ( !$model::getIndexDefinitions() ) {
            abort( 404, 'Página no encontrada' );
        }

        return view()->first( [ sprintf( 'admin.crud.%s.index', $model::getClassSlug() ), 'admin.crud.index' ], $this->filterIndexData( [
            'model' => $model,
            'pluralName' => $model::getPluralName(),
            'singularName' => $model::getSingularName(),
            'creatable' => $model::isCreatable(),
            'table' => $model::getIndexTable( session( "filters.$model.parsed", [] ) ),
        ] ) );
    }

    public function massiveUpdate( Request $request )
    {
        /** @var class-string<AdminModelInterface> $model */
        $model = $request->query( 'model' );
        $posted = $request->post();
        // De todos los campos de asignación masiva nos quedamos solo con el campo enviado
        $field = array_filter( $model::getMassiveAssignmentFields(), fn( $field ) => $field === $posted['field'], ARRAY_FILTER_USE_KEY );

        $validator = Validator::make( [ $posted['field'] => $posted['value'] ], $this->getValidationRules( $field ) );

        if ( $validator->fails() ) {
            Flash::error( $validator->errors()->first() );
        } else {
            $builder = $model::getIndexBuilder( session( "filters.$model.parsed", [] ) );
            $validated = $validator->validated();

            $builder->chunkById( 100, function ( $entities ) use ( $model, $validated ) {
                DB::transaction( function () use ( $entities, $model, $validated ) {
                    $locked = $model::whereIn( 'id', $entities->pluck( 'id' ) )
                        ->lockForUpdate()
                        ->get();

                    /** @var AdminModelInterface $entity */
                    foreach ( $locked as $entity ) {
                        $entity->update( $validated );
                    }
                } );
            } );
        }

        return redirect()->back();
    }

    public function filter( Request $request )
    {
        /** @var class-string<AdminModelInterface> $model */
        $model = $request->query( 'model' );

        $inputs = $request->except( [ '_token', 'model' ] );

        $parsed = [];
        foreach ( $inputs as $field => $config ) {
            // $parsed solo tienen en cuenta los filtros seleccionados y que tengan valores válidos (!= null)
            if ( isset( $config['enabled'] ) && array_filter( $config['values'] ) ) {
                // Transformamos los filtros a un formato de array [field, operator, values]
                $parsed[ $field ] = [ $field, $config['operator'], 1 === count( $config['values'] ) ? $config['values'][0] : $config['values'] ];
            }
        }

        session( [ "filters.$model.inputs" => $inputs, "filters.$model.parsed" => $parsed ] );

        return redirect()->back();
    }

    protected function onForeignKey( string $foreign_key, string $foreign_id, array &$fields )
    {
        $fields[ $foreign_key ]['value'] = $foreign_id;
    }

    public function showCreate( Request $request )
    {
        /** @var class-string<AdminModelInterface> $model */
        $model = $request->query( 'model' );

        if ( !$model::isCreatable() ) {
            abort( 404, 'Página no encontrada' );
        }

        $fields = $model::getCreateFormFields();
        // Si se pasa un foreign_key y un foreign_id, se establece el valor del campo correspondiente
        $foreign_key = $request->query( 'foreign_key' );
        if ( isset( $fields[ $foreign_key ] ) ) {
            $this->onForeignKey( $foreign_key, $request->query( 'foreign_id' ), $fields );
        }
        // Rellenamos un modelo nuevo con los valores de los campos que tengamos en este punto. Serán valores por defecto del modelo y los campos "foreign_key" si hemos llegado a la pantalla de creación desde una colección.
        $formFields = array_filter( $fields, fn( $field ) => !str_starts_with( $field, '_' ), ARRAY_FILTER_USE_KEY ); // Quitamos los campos que empiezan por '_'
        $values = array_combine( array_keys( $formFields ), array_column( $formFields, 'value' ) );
        $entity = ( new $model() )->forceFill( $values ); // Usamos forceFill porque puede haber campos que no estén en el "fillable" del modelo cuando venimos desde una colección (como en el modelo "Price").

        return view()->first( [ sprintf( 'admin.crud.%s.create', $model::getClassSlug() ), 'admin.crud.create' ], $this->filterShowCreateData( [
            'entity' => $entity,
            'singular_name' => $model::getSingularName(),
            'fields' => $fields,
        ] ) );
    }

    /**
     * @param AdminModelInterface $entity
     * @param UploadedFile[] $uploadedFiles
     * @param bool $deleteExistent
     * @return void
     */
    protected function saveUploadedFiles( AdminModelInterface $entity, array $uploadedFiles, bool $deleteExistent = false ): void
    {
        foreach ( $uploadedFiles as $field => $files ) {
            $path = sprintf( '%s/%d/%s',
                Str::plural( $entity::getClassSlug() ), // 'messages'
                $entity->id ?? 0, // 'messages/1'
                $field, // 'messages/1/attachments'
            );

            if ( $deleteExistent ) {
                Storage::deleteDirectory( $path );
            }

            $files = is_array( $files ) ? $files : [ $files ];
            foreach ( $files as $uploadedFile ) {
                $filename = sprintf( '%s.%s',
                    Str::slug( pathinfo( $uploadedFile->getClientOriginalName(), PATHINFO_FILENAME ) ),
                    $uploadedFile->getClientOriginalExtension()
                );

                $uploadedFile->storeAs( $path, $filename );
            }
        }
    }

    private function translateInputs( array $fields, array $validated )
    {
        $translated = [];
        foreach ( $validated as $field => $value ) {
            $translatedValue = isset( $fields[ $field ]['setter'] ) ? $fields[ $field ]['setter']( $value ) : $value;

            if ( is_null( $translatedValue ) && !( $fields[ $field ]['set_if_null'] ?? true ) ) {
                continue;
            }

            $translated[ $field ] = $translatedValue;
        }

        return $translated;
    }

    /**
     * @throws Throwable
     */
    public function create( Request $request )
    {
        /** @var class-string<AdminModelInterface> $model */
        $model = $request->post( 'model' );

        $fields = [];
        // Para todos los campos que tengan en su config una prop "name", intercambiamos esa prop con la clave del campo.
        foreach ( $model::getCreateFormDefinitions() as $field => $config ) $fields[ $config['name'] ?? $field ] = $config;
        unset( $fields['_all'] );

        $validator = Validator::make( $request->all(), $this->getValidationRules( $fields ) );
        if ( $validator->fails() ) {
            return redirect()->back()->withErrors( $validator )->withInput()->with( 'redirect_url', $request->post( 'redirect_url' ) );
        }

        $validated = $this->translateInputs( $fields, $validator->validated() );
        /** @var AdminModelInterface $entity */
        $entity = new $model();
        try {
            DB::transaction( function () use ( $model, $validated, $fields, $request, $entity ) {
                $entity->fill( $validated );

                $this->creating( $entity, $validated );
                $this->saving( $entity, $validated );
                $entity->save();
                $this->saved( $entity, $validated );
                $this->created( $entity, $validated );

                $this->saveUploadedFiles( $entity, $request->allFiles() );
            } );
        } catch ( Throwable $e ) {
            Flash::error( $e->getMessage() );
        }

//        return $this->getResponse( $request );
        $redirect_url = $request->post( 'redirect_url' );
        if ( 'createThenCreate' === $request->post( 'action' ) ) {
            $response = redirect()->back()->with( compact( 'redirect_url' ) );
        } elseif ( 'createThenUpdate' === $request->post( 'action' ) ) {
            $response = redirect()->route( 'admin.crud.get', [ 'action' => 'update', 'model' => $request->post( 'model' ), 'id' => $entity->id ] )->with( compact( 'redirect_url' ) );
        } elseif ( $redirect_url ) {
            $response = redirect( $redirect_url );
        } else { // Mostramos el índice
            $response = redirect()->route( 'admin.crud.get', [ 'action' => 'index', 'model' => $request->post( 'model' ) ] );
        }

        return $response;
    }

    private function getValidationRules( array $fields ): array
    {
        $rules = [];
        foreach ( $fields as $field => $config ) {
            $type = $config['type'] ?? 'text';
//            $field = $config['name'] ?? $field;

            switch ( $type ) {
                case 'text':
                    $rules[ $field ] = [ 'string', 'max:255' ];
                    break;
                case 'number':
                    $rules[ $field ] = [ 'numeric' ];
                    break;
                case 'email':
                    $rules[ $field ] = [ 'email', 'string', 'max:255' ];
                    break;
                case 'tel':
                    $rules[ $field ] = [ 'string', 'max:15' ];
                    break;
                case 'date':
                    $rules[ $field ] = [ 'date' ];
                    break;
                case 'time':
                    $rules[ $field ] = [ 'date_format:H:i' ];
                    break;
                case 'textarea':
                    $rules[ $field ] = [ 'string', 'max:10000' ];
                    break;
                case 'password':
                    $rules[ $field ] = [ Password::defaults() ];
                    break;
                case 'boolean':
                    $rules[ $field ] = [ 'boolean' ];
                    break;
            }

            if ( $config['required'] ?? true ) {
                $rules[ $field ][] = 'required';
            } else {
                $rules[ $field ][] = 'nullable';
            }

            if ( isset( $config['attributes']['multiple'] ) ) {
                $rules[ $field ][] = 'array';
                // ¿Existe validación a nivel de "elemento de un array"?
                if ( array_key_exists( '*', $config['validation'] ) ) {
                    $rules["$field.*"] = $config['validation']['*'];
                    unset( $config['validation']['*'] );
                }
            }

            if ( $config['validation'] ?? null ) {
                $rules[ $field ] = array_merge( $rules[ $field ], $config['validation'] );
            }
        }
        return $rules;
    }

    protected function filterIndexData( array $data ): array
    {
        return $data;
    }

    protected function filterShowUpdateData( array $data ): array
    {
        return $data;
    }

    protected function filterShowCreateData( array $data ): array
    {
        return $data;
    }

    public function showUpdate( Request $request )
    {
        /** @var class-string<AdminModelInterface> $model */
        $model = $request->query( 'model' );
        /** @var AdminModelInterface $entity */
        $entity = $model::findOrFail( $request->query( 'id' ) );

        if ( !$entity->isUpdatable() ) {
            abort( 404, 'Página no encontrada' );
        }

        return view()->first( [ sprintf( 'admin.crud.%s.update', $model::getClassSlug() ), 'admin.crud.update' ], $this->filterShowUpdateData( [
            'entity' => $entity,
            'singular_name' => $model::getSingularName(),
            'fields' => $entity->getUpdateFormFields(),
            'collections' => $entity->getCollections(),
        ] ) );
    }

    protected function creating( $entity, $validated )
    {
    }

    protected function updating( $entity, $validated )
    {
    }

    protected function created( $entity, $validated )
    {
    }

    protected function updated( $entity, $validated )
    {
    }

    protected function saving( $entity, $validated )
    {
    }

    protected function saved( $entity, $validated )
    {
    }

    /**
     * @throws Throwable
     */
    public function update( Request $request )
    {
        /** @var class-string<AdminModelInterface> $model */
        $model = $request->post( 'model' );
        $id = $request->post( 'id' );

        try {
            /** @var AdminModelInterface $entity */
            $entity = $model::findOrFail( $id );
            $fields = [];
            // Para todos los campos que tengan en su config una prop "name", intercambiamos esa prop con la clave del campo.
            foreach ( $entity->getUpdateFormDefinitions() as $field => $config ) $fields[ $config['name'] ?? $field ] = $config;
            unset( $fields['_all'] );

            $validator = Validator::make( $request->all(), $this->getValidationRules( $fields ) );
            if ( $validator->fails() ) {
                return redirect()->back()->withErrors( $validator )->withInput()->with( 'redirect_url', $request->post( 'redirect_url' ) );
            }
            // Filtramos los valores validados a traves de los 'setters' (teniendo en cuenta parámetros como 'set_if_null').
            $validated = $this->translateInputs( $fields, $validator->validated() );
            DB::transaction( function () use ( $model, $validated, $request, $id ) {
                $entity = $model::lockForUpdate()->findOrFail( $id );
                $entity->fill( $validated );

                $this->updating( $entity, $validated );
                $this->saving( $entity, $validated );
                $entity->save();
                $this->saved( $entity, $validated );
                $this->updated( $entity, $validated );

                $this->saveUploadedFiles( $entity, $request->allFiles(), true );
            } );
        } catch ( ModelNotFoundException ) {
            Flash::error( sprintf( 'No se encontró %s con id %d en la base de datos.', strtolower( $model::getSingularName() ), $id ) );
        } catch ( Throwable $e ) {
            Flash::error( $e->getMessage() );
        }

//        return $this->getResponse( $request );
        $redirect_url = $request->post( 'redirect_url' );
        if ( 'updateThenUpdate' === $request->post( 'action' ) ) {
            $response = redirect()->back()->with( compact( 'redirect_url' ) );
        } elseif ( $redirect_url ) {
            $response = redirect( $redirect_url );
        } else {
            $response = redirect()->route( 'admin.crud.get', [ 'action' => 'index', 'model' => $request->post( 'model' ) ] );
        }

        return $response;
    }

    public function delete( Request $request )
    {
        /** @var class-string<AdminModelInterface> $model */
        $model = $request->post( 'model' );
        $id = $request->post( 'id' );

        try {
            /** @var AdminModelInterface $entity */
            $entity = $model::findOrFail( $id );

            DB::transaction( function () use ( $entity ) {
                $entity->delete();
            } );
        } catch ( ModelNotFoundException ) {
            Flash::error( sprintf( 'No se encontró %s con id %d en la base de datos.', strtolower( $model::getSingularName() ), $id ) );
        } catch ( Throwable $e ) {
            Flash::error( $e->getMessage() );
        }

        return redirect()->back();
    }

    /**
     * @throws Throwable
     */
    public function datatable( Request $request )
    {
        $posted = $request->post();
        /** @var class-string<AdminModelInterface> $model */
        $model = $posted['model'];

        $table = $model::getIndexTable(
            session( "filters.$model.parsed", [] ),
            $posted['start'],
            $posted['length'],
        );

        return response()->json( [
            'draw' => (int)$posted['draw'],
            'recordsTotal' => $table['totalCount'],
            'recordsFiltered' => $table['filteredCount'],
            'data' => $table['rows'],
//            'error' => '',
        ] );
    }

}

