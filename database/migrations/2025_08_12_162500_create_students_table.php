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
        Schema::create( 'students', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( 'user_id' )->unique()->constrained()->cascadeOnDelete();
            $table->string( 'name' );
            $table->string( 'surname1' );
            $table->string( 'surname2' )->nullable();
            $table->foreignId( 'institution_id' )->nullable()->constrained()->nullOnDelete();
            $table->foreignId( 'degree_id' )->nullable()->constrained()->nullOnDelete();
            $table->foreignId( 'product_id' )->nullable()->constrained()->nullOnDelete();
            $table->integer( 'student_number' )->nullable();
            $table->string( 'identification_number' );
            $table->string( 'phone' );
            $table->string( 'alt_phone' )->nullable();
            $table->text( 'observations' )->nullable();
            $table->boolean( 'single_marketing_consent' )->default( false );
            $table->boolean( 'group_marketing_consent' )->default( false );
            $table->boolean( 'is_delegate' )->default( false );
            $table->boolean( 'wants_photo_files' )->default( false );
            $table->boolean( 'wants_group_photos' )->default( false );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists( 'students' );
    }
};
