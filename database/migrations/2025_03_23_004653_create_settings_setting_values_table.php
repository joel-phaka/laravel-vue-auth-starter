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
        Schema::create('settings_setting_values', function (Blueprint $table) {
            $table->foreignId('setting_id')->constrained('settings')->cascadeOnDelete();
            $table->foreignId('setting_value_id')->constrained('setting_values')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->primary(['setting_id', 'setting_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_setting_values');
    }
};
