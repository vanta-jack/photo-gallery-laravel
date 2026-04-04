<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum("role", ["guest", "user", "admin"])->default("guest");
            // Email is nullable and not enforced to be unique, as uniqueness is handle via logic controllers and is negligible at db level.
            $table->string('email')->nullable();
            $table->string("first_name")->nullable();
            $table->string("last_name")->nullable();
            // Similar to the case with email, hashing will be handed by the logic controller 
            $table->string('password')->nullable();

            // TODO: Once the photos table is created, we can add a foreign key constraint here to ensure referential integrity.
            $table->bigInteger("profile_photo_id")->nullable();
            // Timestamps handle created_at and updated_at.
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations. Essentially, up creates the users table, and down drops it, allowing for easy rollback of database changes.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
