<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Get menus with modules for authenticated user
     */
    public function sidebar(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Get user group
        $userGroup = $user->userGroup;

        if (! $userGroup) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki group',
            ], 403);
        }

        // Get all menus with their modules
        $menus = Menu::where('status', true)
            ->with(['modules' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('row_order')
            ->get()
            ->map(function ($menu) use ($userGroup) {
                // Filter modules based on user permissions
                $menu->modules = $menu->modules->filter(function ($module) use ($userGroup) {
                    // Check if user has permission to view this module
                    return $userGroup->permissions()
                        ->whereHas('moduleAccess', function ($query) use ($module) {
                            $query->where('module_id', $module->id)
                                ->where('identifiers', 'LIKE', $module->identifiers.'-view');
                        })
                        ->where('status', true)
                        ->exists();
                })->values();

                return $menu;
            })
            ->filter(function ($menu) {
                // Only include menus that have modules
                return $menu->modules->count() > 0;
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }

    /**
     * Get all menus (admin only)
     */
    public function index()
    {
        $menus = Menu::where('status', true)
            ->with('modules')
            ->orderBy('row_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }

    /**
     * Get single menu
     */
    public function show($id)
    {
        $menu = Menu::with('modules')
            ->where('status', true)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $menu,
        ]);
    }
}
