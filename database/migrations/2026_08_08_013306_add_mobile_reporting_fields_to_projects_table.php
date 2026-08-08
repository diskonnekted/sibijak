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
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('reported_progress', 5, 2)->nullable();
            $table->string('reported_photo')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->string('verification_status')->default('clean'); // clean, pending, verified
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['reported_progress', 'reported_photo', 'reported_at', 'verification_status']);
        });
    }
};
