<?php

namespace App\Http\Controllers\Admin;

use App\Models\Degree;
use App\Models\Institution;
use App\Models\Message;
use App\Models\Product;
use App\Models\Student;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class StudentCRUDController extends EntityCRUDController
{
    protected function filterShowCreateData( array $data ): array
    {
//        /** @var Student $student */
//        $student = $data['entity'];
        $institutions = Institution::all();
        $degrees = Degree::orderBy( 'name' )->get()->groupBy( 'institution_id' );
        $tickets = Message::ofType( Message::TICKET )->get();
        $workshops = Workshop::all();
//        $workshop_id = $student->appointment?->workshop_id;

        return array_merge( $data, compact( 'institutions', 'degrees', 'tickets', 'workshops' ) );
    }

    protected function filterShowUpdateData( array $data ): array
    {
        return array_merge( $this->filterShowCreateData( $data ), [
            'paid' => 0
        ] );
    }

    protected function creating( $entity, $validated )
    {
        // Creamos el usuario asociado al alumno
        $password = Str::random( 12 );
        $user = User::create( [
            'name' => explode( '@', $validated['email'] )[0],
            'email' => $validated['email'],
            'password' => Hash::make( $password ),
        ] );
        $entity->user_id = $user->id;
        // TODO: Habría que enviar un mensaje al alumno con la password generada?
    }

    protected function saved( $entity, $validated )
    {
        // Si ha cambiado el email, actualizamos el usuario
        $validatedEmail = $validated['email'];
        /** @var Student $entity */
        if ( $entity->email !== $validatedEmail ) {
            $entity->user->update( [ 'email' => $validatedEmail ] );
        }
        // Guardamos los datos del pago
        if ( ( $validated['product_id'] ?? null ) && abs( $validated['total'] ) >= 0.01 ) {
            $product = Product::find( $validated['product_id'] );
            $degreePrice = $product->prices()->where( 'degree_id', $validated['degree_id'] )->first();
            $price = $degreePrice?->price ?? $product->price;

            $entity->payments()->create( [
                'degree_id' => $validated['degree_id'],
                'product_id' => $product->id,
                'product_name' => $product->name,
                'concepts' => $product->concepts,
                'price' => $price,
                'amount' => $validated['total']
            ] );
        }
    }

    protected function saving( $entity, $validated )
    {
        // La primera vez que se escoge un producto para el alumno, imprimimos el ticket al volver al formulario
        if (
            in_array( request()->post( 'action' ), [ 'updateThenUpdate', 'createThenUpdate' ] ) &&
            !$entity->getOriginal( 'product_id' ) &&
            $entity->product_id
        ) {
            session()->flash( 'print_ticket' );
        }
    }
}
