<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Models\Degree;
use App\Models\Product;
use App\Models\Student;

class DegreeController extends Controller
{
    public function show( Degree $degree )
    {
        return response()->json( compact( 'degree' ) );
    }

    public function nextStudentNumber( Degree $degree )
    {
        return response()->json( [
            'number' => $degree->students()->max( 'student_number' ) + 1
        ] );
    }

}
