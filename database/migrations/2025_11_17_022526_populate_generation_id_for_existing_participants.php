<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the first active generation, if not exists create one
        $activeGeneration = DB::table('generations')->where('is_active', true)->first();

        if (!$activeGeneration) {
            // Create a default generation if none exists
            DB::table('generations')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => 'Generasi Saat Ini',
                'start_years' => date('Y'),
                'end_years' => date('Y'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $activeGeneration = DB::table('generations')->where('is_active', true)->first();
        }

        // Update all participants without generation_id to use the active one
        DB::table('participants')
            ->whereNull('generation_id')
            ->update(['generation_id' => $activeGeneration->id]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be safely reversed
    }
};
