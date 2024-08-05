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
        Schema::create('oauth_clients', function (Blueprint $table) {
<<<<<<<< HEAD:database/migrations/2024_07_31_102323_create_oauth_clients_table.php
            $table->uuid('id');
            $table->uuid('user_id')->nullable()->index();
========
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
>>>>>>>> 50e8bc4700753af9a99de0f111a2ff8ba59afb48:database/migrations/2024_08_01_092427_create_oauth_clients_table.php
            $table->string('name');
            $table->string('secret', 100)->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect');
            $table->boolean('personal_access_client');
            $table->boolean('password_client');
            $table->boolean('revoked');
            $table->timestamps();
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->index('user_id', 'oauth_clients_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropIndex('oauth_clients_user_id_index');
        });

        Schema::dropIfExists('oauth_clients');
    }
};
