<?php

namespace App\Http\Controllers\Admin\Lms;

use App\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('admin.lms.schedules.index');
    }
}
