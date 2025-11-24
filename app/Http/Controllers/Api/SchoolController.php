<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Get all schools.
     */
    public function index(Request $request)
    {
        $schools = School::query()
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $schools,
        ], 200);
    }
}
