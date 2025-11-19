<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Blade;

class MessageController extends Controller
{
    /**
     * @throws BindingResolutionException
     */
    public function preview( Request $request )
    {
        $markdown = Container::getInstance()->make( Markdown::class );
        // Si recibimos un 'id', cargamos ese mensaje
        $id = $request->post( 'id' );
        $placeholders = [];
        if ( $id ) {
            $message = Message::findOrFail( $id );
            $postedPlaceholders = $request->post( 'placeholders', [] );
            foreach ( Message::TYPES[ $message->type ]['placeholders'] as $placeholder => $field ) {
                $placeholders[ ltrim( $placeholder, ':' ) ] = $postedPlaceholders[ $field ] ?? null;
            }
            $showBackground = $message->show_background;
        } else {
            $message = new Message();
            $message->body = $request->post( 'content' );
            $showBackground = $request->post( 'showBackground', false );
        }

        $html = $markdown
            ->theme( $markdown->getTheme() )
            ->render( 'emails.messages.default', [
                'content' => Blade::render(
                    $message->getParsedBody( new User(), $placeholders )
                ),
                'config' => [
                    'body_class' => 'type-' . $message->type,
                    'show_background' => $showBackground,
                ]
            ] );

        return response()->json( [ 'html' => $html->toHtml() ] );
    }
}
