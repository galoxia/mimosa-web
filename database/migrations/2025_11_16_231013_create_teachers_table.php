<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create( 'teachers', function ( Blueprint $table ) {
			$table->id();
            $table->string( 'name' );
            $table->string( 'surname1' );
            $table->string( 'surname2' )->nullable();
            $table->integer( 'teacher_number' )->nullable();
            $table->text( 'observations' )->nullable();
			$table->timestamps();
		} );
	}

	public function down(): void
	{
		Schema::dropIfExists( 'teachers' );
	}
};
