<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Universidad de Salamanca',
            'Universidad Pontificia',
            'Masters, Cursos, Otros',
        ];

        foreach ( $names as $name ) {
            Institution::create( [ 'name' => $name ] );
        }
    }
}
