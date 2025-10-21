<?php

use App\Enums\AuthState;
use App\Enums\AuthType;
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
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->enum('auth_state', enum_values(AuthState::class))->index();
            $table->enum('auth_type', enum_values(AuthType::class))->index();
            $table->string('auth_type_id')->nullable()->default(null);
            $table->foreignId('oauth_provider_id')
                ->nullable()
                ->default(null)
                ->constrained('oauth_providers')
                ->nullOnDelete();
            $table->string('ip')->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('device_platform')->nullable()->index();;
            $table->string('location')->nullable();
            $table->string('country_code')->nullable()->index();
            $table->string('region_code')->nullable();
            $table->string('area_code')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();

            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logins');
    }
};
