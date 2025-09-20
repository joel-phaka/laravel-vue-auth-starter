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
        Schema::table('users', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->index()
                ->after('email_verified_at');
            $table->string('phone_number', 16)->nullable()->unique()
                ->after('country_code');
            $table->timestamp('phone_number_verified_at')->nullable()
                ->after('phone_number');
        });

        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_phone_number CHECK (phone_number IS NULL OR phone_number REGEXP '^\\\\+[0-9]{7,15}$');");
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

        Schema::dropColumnsIfExist('users', [
            'country_code',
            'phone_number',
            'phone_number_verified_at'
        ]);
    }
};
