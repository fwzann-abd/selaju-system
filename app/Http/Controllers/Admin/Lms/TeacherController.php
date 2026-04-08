<?php

namespace App\Http\Controllers\Admin\Lms;

use App\Http\Controllers\Controller;

class TeacherController extends Controller
{
    public function index()
    {
        return view('admin.lms.teachers.index');
    }
}
