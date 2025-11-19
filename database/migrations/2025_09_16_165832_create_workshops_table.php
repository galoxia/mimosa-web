<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create( 'workshops', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'name' );
            $table->tinyText( 'code' )->nullable();
            $table->text( 'description' )->nullable();
            $table->tinyInteger( 'priority' )->unsigned()->default( 0 );
            $table->timestamps();
        } );
    }

    public function down(): void
    {
        Schema::dropIfExists( 'workshops' );
    }
};
