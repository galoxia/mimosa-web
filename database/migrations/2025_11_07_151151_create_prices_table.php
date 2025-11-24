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
        Schema::create( 'prices', function ( Blueprint $table ) {
            $table->id();

            $table->foreignId( 'product_id' )->constrained()->onDelete( 'cascade' );
            $table->foreignId( 'degree_id' )->constrained()->onDelete( 'cascade' );
            $table->unique( [ 'product_id', 'degree_id' ] );
            $table->decimal( 'price' );
            $table->tinyInteger( 'discount' )->default( 0 );
            $table->text( 'search_text' )->nullable();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists( 'prices' );
    }
};
