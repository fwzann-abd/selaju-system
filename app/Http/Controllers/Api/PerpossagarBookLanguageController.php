<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerpossagarBookLanguage;

class PerpossagarBookLanguageController extends Controller
{
    public function index()
    {
        $languages = PerpossagarBookLanguage::orderBy('name')
            ->get(['uuid', 'name', 'slug']);

        return response()->json([
            'data' => $languages,
        ]);
    }
}
