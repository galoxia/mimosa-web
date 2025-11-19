<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create( 'products', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'name' );
            $table->text( 'description' )->nullable();
//            $table->tinyInteger( 'priority' )->unsigned()->default( 0 );
            $table->decimal( 'price' );
            $table->tinyInteger( 'discount' )->default( 0 );
            $table->text( 'concepts' )->nullable();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists( 'products' );
    }
};
