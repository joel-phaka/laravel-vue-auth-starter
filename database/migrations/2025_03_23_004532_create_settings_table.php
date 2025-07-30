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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_group_id')->nullable()->default(null)->constrained('setting_groups')->nullOnDelete();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('default_value')->nullable();
            $table->enum('type', ['string', 'boolean', 'integer', 'float', 'array'])->default('string');
            $table->string('title')->nullable();
            $table->text('description')->nullable()->default(null);
            $table->string('icon')->nullable();
            $table->boolean('visible')->default(false);
            $table->boolean('required')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
