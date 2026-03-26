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
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');

            $table->date('birthdate');

            $table->enum('country', ['USA', 'Canada', 'Mexico'])->default('USA');
            $table->enum('language', ['English', 'French', 'Spanish'])->default('English');

            $table->enum('country_code', ['+1', '+44', '+52'])->default('+1');
            $table->string('phone');

            $table->enum('role', ['super_admin', 'educator', 'regular']);
            $table->string('referral_code', 12)->unique();
            // $table->unsignedBigInteger('referral_code')->unique();
            // $table->primary('referral_code');
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // $table->boolean('terms_accepted');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
