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
            // Lapisan 1: verifikasi pengawas lapangan (pemeriksa_lapangan)
            $table->timestamp('pengawas_verified_at')->nullable();
            $table->foreignId('pengawas_verified_by')->nullable()->constrained('users')->onDelete('set null');

            // Lapisan 2: persetujuan final admin PUPR
            $table->text('final_verification_note')->nullable();
            $table->timestamp('final_verified_at')->nullable();
            $table->foreignId('final_verified_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pengawas_verified_by');
            $table->dropColumn('pengawas_verified_at');
            $table->dropConstrainedForeignId('final_verified_by');
            $table->dropColumn('final_verified_at');
            $table->dropColumn('final_verification_note');
        });
    }
};