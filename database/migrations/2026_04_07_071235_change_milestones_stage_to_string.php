<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('milestones', function (Blueprint $table): void {
            $table->string('stage')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('milestones')
            ->whereNotIn('stage', ['baby', 'grade_school', 'highschool_college'])
            ->update(['stage' => 'highschool_college']);

        Schema::table('milestones', function (Blueprint $table): void {
            $table->enum('stage', ['baby', 'grade_school', 'highschool_college'])->change();
        });
    }
};
