<?php

namespace App\Http\Controllers\Admin;

use App\Mail\DefaultMessage;
use App\Models\Message;
use App\Models\User;
use App\Services\Flash;
use Illuminate\Http\Request;
use Mail;
use Throwable;

final class UserCRUDController extends EntityCRUDController
{
    public function send( User $user, Request $request )
    {
        try {
            $message = Message::findOrFail( $request->post( 'message_id' ) );

            Mail::send( new DefaultMessage( $user, $message ) );
        } catch ( Throwable $e ) {
            Flash::error( $e->getMessage() );
        }

        return redirect()->back();
    }

//    public function sendSMS( User $user, Request $request )
//    {
//        try {
//            $message = Message::findOrFail( $request->post( 'message_id' ) );
//
//        } catch ( Throwable $e ) {
//            Flash::error( $e->getMessage() );
//        }
//
//        return redirect()->back();
//    }
}
