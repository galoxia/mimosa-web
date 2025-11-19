<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Models\Degree;
use App\Models\Student;

class DegreeController extends Controller
{
    public function nextStudentNumber( Degree $degree )
    {
        return response()->json( [
            'number' => $degree->students()->max( 'student_number' ) + 1
        ] );
    }

    public function nextTeacherNumber( Degree $degree )
    {
        return response()->json( [
            'number' => $degree->teachers()->max( 'teacher_number' ) + 1
        ] );
    }
}
