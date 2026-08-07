<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('balai_id')->nullable()->after('username')
                ->constrained('balais')->nullOnDelete();
            $table->string('kontak')->nullable()->after('balai_id');
        });

        // PIC accounts have no real email — make it optional.
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('balai_id');
            $table->dropColumn('kontak');
            $table->string('email')->nullable(false)->change();
        });
    }
};