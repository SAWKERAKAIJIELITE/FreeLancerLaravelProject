<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_requests', function (Blueprint $table) {
            $table->id();

            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');

            $table->enum('country', ['USA', 'Canada', 'Mexico'])->default('USA');
            $table->enum('language', ['English', 'French', 'Spanish'])->default('English');

            $table->date('birthdate');

            $table->enum('country_code', ['+1', '+44', '+52'])->default('+1');
            $table->string('phone');

            $table->string('email')->unique();
            $table->string('password');

            $table->enum('role', ['super_admin', 'educator', 'regular']);

            // $table->string('referral_input')->nullable();
            $table->foreignId('referral_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // $table->boolean('terms_accepted');

            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_requests');
    }
};
