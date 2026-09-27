<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('email');
            $table->string('bidang')->nullable()->after('role');
        });

        Schema::table('project_photos', function (Blueprint $table) {
            $table->string('type')->default('progress')->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('project_photos', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bidang');
            $table->dropColumn('nip');
        });
    }
};