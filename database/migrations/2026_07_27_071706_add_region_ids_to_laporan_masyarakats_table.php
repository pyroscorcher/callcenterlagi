<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_masyarakats', function (Blueprint $table) {

            $table->foreignId('provinsi_id')
                ->nullable()
                ->after('lokasi')
                ->constrained('provinsis')
                ->nullOnDelete();

            $table->foreignId('kabupaten_kota_id')
                ->nullable()
                ->after('provinsi_id')
                ->constrained('kabupaten_kotas')
                ->nullOnDelete();

            $table->foreignId('kecamatan_id')
                ->nullable()
                ->after('kabupaten_kota_id')
                ->constrained('kecamatans')
                ->nullOnDelete();

            $table->foreignId('kelurahan_id')
                ->nullable()
                ->after('kecamatan_id')
                ->constrained('kelurahans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_masyarakats', function (Blueprint $table) {

            $table->dropConstrainedForeignId('provinsi_id');
            $table->dropConstrainedForeignId('kabupaten_kota_id');
            $table->dropConstrainedForeignId('kecamatan_id');
            $table->dropConstrainedForeignId('kelurahan_id');
        });
    }
};