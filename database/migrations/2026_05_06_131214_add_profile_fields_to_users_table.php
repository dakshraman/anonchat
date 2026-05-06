<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('target_gender', ['male', 'female', 'both', 'any'])->default('any');
            $table->string('location')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_guest')->default(false);
            $table->boolean('is_online')->default(false);
            $table->string('session_token')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['age', 'gender', 'target_gender', 'location', 'ip_address', 'is_guest', 'is_online', 'session_token']);
        });
    }
};