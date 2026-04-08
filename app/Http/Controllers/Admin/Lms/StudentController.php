<?php

namespace App\Http\Controllers\Admin\Lms;

use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    public function index()
    {
        return view('admin.lms.students.index');
    }
}
