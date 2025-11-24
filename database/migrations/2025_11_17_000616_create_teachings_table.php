<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teachings', function (Blueprint $table) {
            $table->id();

            $table->foreignId( 'teacher_id' )->constrained()->onDelete( 'cascade' );
            $table->foreignId( 'degree_id' )->constrained()->onDelete( 'cascade' );
            $table->unique( [ 'teacher_id', 'degree_id' ] );
            $table->text( 'search_text' )->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachings');
    }
};
