<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Is Favorite to Albums Table
 * 
 * Adds an is_favorite boolean column to the albums table to enable
 * users to pin favorite albums at the top of the album index view.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds is_favorite boolean column after is_private, default false.
     */
    public function up(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->boolean('is_favorite')->default(false)->after('is_private');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Removes the is_favorite column from the albums table.
     */
    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('is_favorite');
        });
    }
};
