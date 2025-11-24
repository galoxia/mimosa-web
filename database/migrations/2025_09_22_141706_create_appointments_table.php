<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create( 'appointments', function ( Blueprint $table ) {
			$table->id();
            $table->time( 'appointment_time' );
            $table->foreignId( 'schedule_id' )->constrained( 'schedules' )->onDelete( 'cascade' );;
			$table->foreignId( 'user_id' )->nullable();
            $table->text( 'search_text' )->nullable()->nullable();
			$table->timestamps();
		} );
	}

	public function down(): void
	{
		Schema::dropIfExists( 'appointments' );
	}
};
