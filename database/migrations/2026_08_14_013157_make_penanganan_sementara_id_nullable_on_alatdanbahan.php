<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the FK before altering the column, then re-add it nullable.
        Schema::table('alat_dan_bahan', function (Blueprint $table) {
            $table->dropForeign(['penanganan_sementara_id']);
        });

        Schema::table('alat_dan_bahan', function (Blueprint $table) {
            $table->unsignedBigInteger('penanganan_sementara_id')->nullable()->change();

            $table->foreignId('laporan_balai_id')
                ->nullable()
                ->after('penanganan_sementara_id')
                ->constrained('laporan_balai')
                ->cascadeOnDelete();
        });

        Schema::table('alat_dan_bahan', function (Blueprint $table) {
            $table->foreign('penanganan_sementara_id')
                ->references('id')->on('penanganan_sementara')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('alat_dan_bahan', function (Blueprint $table) {
            $table->dropForeign(['penanganan_sementara_id']);
            $table->dropForeign(['laporan_balai_id']);
            $table->dropColumn('laporan_balai_id');
        });

        Schema::table('alat_dan_bahan', function (Blueprint $table) {
            $table->unsignedBigInteger('penanganan_sementara_id')->nullable(false)->change();
        });

        Schema::table('alat_dan_bahan', function (Blueprint $table) {
            $table->foreign('penanganan_sementara_id')
                ->references('id')->on('penanganan_sementara')
                ->cascadeOnDelete();
        });
    }
};