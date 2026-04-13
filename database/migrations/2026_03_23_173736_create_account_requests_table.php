<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_requests', function (Blueprint $table) {
            $table->id();

            $table->string('username');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');

            $table->foreignId('country_id')->constrained()->restrictOnDelete();
            $table->foreignId('language_id')->constrained()->restrictOnDelete();
            $table->foreignId('phone_country_id')->constrained('countries')->restrictOnDelete();

            $table->string('phone', 30);

            $table->date('birthdate');

            $table->string('email');
            $table->string('password');

            $table->enum('role', ['networker', 'educator', 'regular'])->index();

            $table->text('rejection_reason')->nullable();

            $table->foreignId('referral_id')->constrained('users');
            $table->foreignId('who_fill_data_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->unique()->nullable()->constrained()->nullOnDelete();

            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->index();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('terms_accepted_at')->nullable();

            $table->timestamps();

            $table->foreignId('resubmitted_from_id')
                ->nullable()
                ->constrained('account_requests')
                ->nullOnDelete();

            $table->string('pending_email_key')
                ->virtualAs("CASE WHEN status = 'pending' THEN email ELSE NULL END")
                ->nullable();

            $table->string('pending_username_key')
                ->virtualAs("CASE WHEN status = 'pending' THEN username ELSE NULL END")
                ->nullable();

            $table->index('status','only_status_index');
            $table->index('referral_id');
            $table->index('reviewed_by');
            $table->index('resubmitted_from_id');

            $table->unique('pending_email_key', 'account_requests_pending_email_unique');
            $table->unique('pending_username_key', 'account_requests_pending_username_unique');

            $table->index(['email', 'status']);
            $table->index(['username', 'status']);

            // $table->string('resubmission_token')->nullable()->unique();
            // $table->timestamp('resubmission_token_expires_at')->nullable();
            // $table->timestamp('resubmission_token_used_at')->nullable();

            // $table->index('resubmission_token');
        });

        DB::unprepared("
            CREATE TRIGGER trg_account_requests_before_insert
            BEFORE INSERT ON account_requests
            FOR EACH ROW
            BEGIN
                IF NEW.status = 'pending' THEN

                    IF EXISTS (
                        SELECT 1
                        FROM users
                        WHERE email = NEW.email
                        LIMIT 1
                    ) THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'account_REQUEST_EMAIL_EXISTS_IN_USERS';
                    END IF;

                    IF EXISTS (
                        SELECT 1
                        FROM users
                        WHERE username = NEW.username
                        LIMIT 1
                    ) THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'account_REQUEST_USERNAME_EXISTS_IN_USERS';
                    END IF;

                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_account_requests_before_update
            BEFORE UPDATE ON account_requests
            FOR EACH ROW
            BEGIN
                IF NEW.status = 'pending' THEN

                    IF EXISTS (
                        SELECT 1
                        FROM users
                        WHERE email = NEW.email
                        LIMIT 1
                    ) THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'account_REQUEST_EMAIL_EXISTS_IN_USERS';
                    END IF;

                    IF EXISTS (
                        SELECT 1
                        FROM users
                        WHERE username = NEW.username
                        LIMIT 1
                    ) THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'account_REQUEST_USERNAME_EXISTS_IN_USERS';
                    END IF;

                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_account_requests_before_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_account_requests_before_update');
        Schema::dropIfExists('account_requests');
    }
};
