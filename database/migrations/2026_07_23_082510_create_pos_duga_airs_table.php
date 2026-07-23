<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_duga_airs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // Their identifiers — kept separate from our own auto-increment id.
            $table->string('external_id')->unique(); // API's "id", e.g. "06.14.02190010027"
            $table->unsignedBigInteger('objectid')->nullable()->index();

            $table->string('nama_hidrologi')->nullable();
            $table->string('daerah_aliran_sungai')->nullable();
            $table->string('wilayah_sungai')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kota_kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('pengelola')->nullable();
            $table->string('kode')->nullable();
            $table->string('kode_balai')->nullable();
            $table->string('kode_daerah_aliran_sungai')->nullable();
            $table->string('kode_daerah_aliran_sungai_lain')->nullable();
            $table->string('kode_wilayah_sungai')->nullable();
            $table->string('kode_wilayah_sungai_lain')->nullable();
            $table->string('teknis_jenis_pos')->nullable();
            $table->string('tipe_hidrologi')->nullable();
            $table->decimal('latitude', 12, 8)->nullable();
            $table->decimal('longitude', 12, 8)->nullable();
            $table->unsignedInteger('urut')->nullable();

            // The API's own "updated_at" — kept distinct from our updated_at
            // (which reflects when WE last wrote this row).
            $table->timestamp('source_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_duga_airs');
    }
};