<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create( 'calendars', function ( Blueprint $table ) {
			$table->id();
			$table->date( 'start_date' );
			$table->date( 'end_date' );
            $table->date( 'closing_date' );
            $table->time( 'morning_start_time' );
            $table->time( 'morning_end_time' );
            $table->integer( 'morning_slots' );
            $table->time( 'afternoon_start_time' );
            $table->time( 'afternoon_end_time' );
            $table->integer( 'afternoon_slots' );
			$table->foreignId( 'workshop_id' )->constrained( 'workshops' );
            $table->text( 'search_text' )->nullable();
			$table->timestamps();
		} );
	}

	public function down(): void
	{
		Schema::dropIfExists( 'calendars' );
	}
};
