<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create( 'messages', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'name' );
            $table->unsignedTinyInteger( 'type' )->default( 0 );
            $table->string( 'subject' );
            $table->string( 'from' );
            $table->text( 'cc' )->nullable();
            $table->longText( 'body' );
            $table->boolean( 'show_background' )->default( false );
//            $table->json( 'attachments' )->nullable();;
            $table->tinyInteger( 'priority' )->unsigned()->default( 0 );
            $table->timestamps();
        } );
    }

    public function down(): void
    {
        Schema::dropIfExists( 'messages' );
    }
};
