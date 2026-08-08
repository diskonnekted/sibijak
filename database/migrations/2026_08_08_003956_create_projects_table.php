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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->string('nama_pekerjaan');
            $table->bigInteger('nilai_kontrak');
            $table->year('tahun_anggaran');
            $table->string('status');
            $table->decimal('progress', 5, 2)->default(0.00);
            $table->double('latitude');
            $table->double('longitude');
            $table->text('detail_lokasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
