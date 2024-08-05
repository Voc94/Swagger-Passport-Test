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
        Schema::create('conversations_roles_users', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignId('conversation_id')->constrained('conversations');
            $table->foreignId('role_id')->constrained('roles');
            $table->primary(['user_id', 'conversation_id', 'role_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations_roles_users');
    }
};
