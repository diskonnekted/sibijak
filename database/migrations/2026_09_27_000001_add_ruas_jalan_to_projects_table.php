<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Referensi ruas jalan (dari public/ruas_jalan.geojson) tempat proyek berada.
            // id adalah integer unik yang sama dengan properties.id pada setiap fitur GeoJSON.
            $table->unsignedBigInteger('ruas_jalan_id')->nullable()->after('detail_lokasi');
            $table->string('ruas_jalan_nomor', 20)->nullable()->after('ruas_jalan_id');
            $table->string('ruas_jalan_nama', 255)->nullable()->after('ruas_jalan_nomor');

            $table->index('ruas_jalan_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['ruas_jalan_id']);
            $table->dropColumn(['ruas_jalan_id', 'ruas_jalan_nomor', 'ruas_jalan_nama']);
        });
    }
};