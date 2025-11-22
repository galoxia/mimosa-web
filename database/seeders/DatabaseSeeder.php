<?php

namespace Database\Seeders;

use App\Models\Calendar;
use App\Models\Degree;
use App\Models\Institution;
use App\Models\Product;
use App\Models\Student;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @throws Throwable
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->admin()->create( [
            'name' => 'admin',
            'email' => 'admin@example.com',
        ] );

        // Seed educational institutions
        $this->call( [
            InstitutionSeeder::class,
            MessageSeeder::class,
        ] );

        // Seed workshops and calendars
        $ws1 = Workshop::create( [
            'name' => 'Estudio de arriba',
            'code' => '#T1',
            'description' => 'Subiendo las escaleras'
        ] );
        $ws2 = Workshop::create( [
            'name' => 'Planta baja',
            'code' => '#T2',
            'description' => 'Bajando las escaleras',
            'priority' => 1
        ] );

        DB::transaction( function () use ( $ws1, $ws2 ) {
            Calendar::create( [
                'workshop_id' => $ws1->id,
                'start_date' => '2025-09-01',
                'end_date' => '2026-08-31',
                'closing_date' => '2025-12-27',
                'morning_start_time' => '09:30',
                'morning_end_time' => '13:45',
                'morning_slots' => 8,
                'afternoon_start_time' => '15:30',
                'afternoon_end_time' => '19:45',
                'afternoon_slots' => 8,
            ] );

            Calendar::create( [
                'workshop_id' => $ws2->id,
                'start_date' => '2025-09-01',
                'end_date' => '2026-08-31',
                'closing_date' => '2026-01-14',
                'morning_start_time' => '09:00',
                'morning_end_time' => '13:45',
                'morning_slots' => 6,
                'afternoon_start_time' => '16:15',
                'afternoon_end_time' => '20:15',
                'afternoon_slots' => 8,
            ] );

            // Seed degrees
            $institutions = Institution::all();
            Degree::factory()->count( 50 )->create( [
                'institution_id' => fn() => $institutions->random(),
            ] );

            Product::factory()->count( 5 )->create();

            // Seed students
            Student::factory( 50 )->make()->each( function ( $student ) use ( $institutions ) {
                $institution = $institutions->random();
                $degree = $institution->degrees->random();

                $student->institution_id = $institution->id;
                $student->degree_id = $degree->id;

                $student->save();
            } );
        } );
    }
}
