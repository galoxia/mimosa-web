<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create( 'payments', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( 'student_id' )->constrained( 'students' )->cascadeOnDelete();
            $table->foreignId( 'degree_id' )->constrained( 'degrees' )->cascadeOnDelete();
            $table->foreignId( 'product_id' )->constrained( 'products' )->nullOnDelete();
            $table->string( 'product_name' );
            $table->text( 'concepts' )->nullable();
            $table->decimal( 'price' );
            $table->decimal( 'discount' )->nullable();
            $table->decimal( 'amount' );
            $table->text( 'search_text' )->nullable();
            $table->timestamps();
        } );
    }

    public function down(): void
    {
        Schema::dropIfExists( 'payments' );
    }
};
