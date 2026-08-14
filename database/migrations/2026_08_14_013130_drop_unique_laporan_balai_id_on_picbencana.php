<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('picbencana', function (Blueprint $table) {
            // The unique index is backing the FK's lookup index in MySQL --
            // drop the FK first, or dropping the unique index alone fails
            // with "needed in a foreign key constraint".
            $table->dropForeign(['laporan_balai_id']);
        });

        Schema::table('picbencana', function (Blueprint $table) {
            $table->dropUnique(['laporan_balai_id']);
        });

        Schema::table('picbencana', function (Blueprint $table) {
            // Re-add the FK; this creates a normal (non-unique) index.
            $table->foreign('laporan_balai_id')
                ->references('id')->on('laporan_balai')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('picbencana', function (Blueprint $table) {
            $table->dropForeign(['laporan_balai_id']);
        });

        Schema::table('picbencana', function (Blueprint $table) {
            $table->unique('laporan_balai_id');
        });

        Schema::table('picbencana', function (Blueprint $table) {
            $table->foreign('laporan_balai_id')
                ->references('id')->on('laporan_balai')
                ->cascadeOnDelete();
        });
    }
};