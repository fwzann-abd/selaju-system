<?php

namespace App\Http\Controllers\Admin\Lms;

use App\Http\Controllers\Controller;

class ClassroomController extends Controller
{
    /**
     * Display the classroom listing page.
     */
    public function index()
    {
        return view('admin.lms.classrooms.index');
    }
}
