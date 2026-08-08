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
            $table->date('tanggal_kontrak')->nullable();
            $table->date('tanggal_pelaksanaan')->nullable();
            $table->date('tanggal_pemeriksaan')->nullable();
            $table->date('tanggal_deadline')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['tanggal_kontrak', 'tanggal_pelaksanaan', 'tanggal_pemeriksaan', 'tanggal_deadline']);
        });
    }
};
