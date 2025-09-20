<?php

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
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('auth_type', enum_values(AuthType::class))->index();
            $table->string('auth_type_id')->nullable()->default(null);
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_platform')->nullable();
            $table->string('location')->nullable();
            $table->string('country_code')->nullable()->index();
            $table->string('region_code')->nullable();
            $table->string('area_code')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
