<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guestbook_entry_photo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guestbook_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['guestbook_entry_id', 'photo_id']);
        });

        DB::table('guestbook_entries')
            ->select(['id', 'photo_id'])
            ->whereNotNull('photo_id')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $entry): void {
                DB::table('guestbook_entry_photo')->insert([
                    'guestbook_entry_id' => (int) $entry->id,
                    'photo_id' => (int) $entry->photo_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('guestbook_entry_photo');
    }
};
