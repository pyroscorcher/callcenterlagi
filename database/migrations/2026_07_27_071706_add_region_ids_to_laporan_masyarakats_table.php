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

            $table->dropForeign(['provinsi_id']);
            $table->dropForeign(['kabupaten_kota_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['kelurahan_id']);

            $table->dropColumn([
                'provinsi_id',
                'kabupaten_kota_id',
                'kecamatan_id',
                'kelurahan_id',
            ]);
        });
    }
};