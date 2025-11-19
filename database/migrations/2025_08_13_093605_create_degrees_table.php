<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create( 'degrees', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'name' );
            $table->tinyText( 'abbreviation' );
            $table->date( 'closing_date' )->nullable();
            $table->foreignId( 'institution_id' )->constrained();
            $table->json( 'workshop_ids' )->nullable();
            $table->timestamps();
        } );
    }

    public function down(): void
    {
        Schema::dropIfExists( 'degrees' );
    }
};
