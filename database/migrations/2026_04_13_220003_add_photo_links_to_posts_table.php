<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('photo_id')->nullable()->after('description')->constrained()->nullOnDelete();
        });

        Schema::create('photo_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('photo_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['post_id', 'photo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_post');

        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('photo_id');
        });
    }
};
