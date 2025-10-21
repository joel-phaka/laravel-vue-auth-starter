<?php

use App\Enums\UserStatus;
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
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->storedAs('CONCAT(first_name, " ", last_name)')->index()->fulltext();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('country_code', 2)->nullable()->index();
            $table->string('phone_number', 16)->nullable()->unique();
            $table->timestamp('phone_number_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->enum('status', enum_values(UserStatus::class))->default(UserStatus::ACTIVE)->index();
            $table->timestamps();

            $table->index(['created_at']);
            $table->index(['updated_at']);
        });

        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_phone_number CHECK (phone_number IS NULL OR phone_number REGEXP '^\\\\+[0-9]{7,15}$');");


        /*
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });*/

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
        try {
            DB::statement('ALTER TABLE users DROP CHECK chk_phone_number;');
        } catch (\Throwable) {
            // Ignore error if the constraint doesn't exist'
        }

        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
