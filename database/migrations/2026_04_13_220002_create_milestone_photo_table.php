<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_photo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['milestone_id', 'photo_id']);
        });

        DB::table('milestones')
            ->select(['id', 'photo_id'])
            ->whereNotNull('photo_id')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $milestone): void {
                DB::table('milestone_photo')->insert([
                    'milestone_id' => (int) $milestone->id,
                    'photo_id' => (int) $milestone->photo_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_photo');
    }
};
