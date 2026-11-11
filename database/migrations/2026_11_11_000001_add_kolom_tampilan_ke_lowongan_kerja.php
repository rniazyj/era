<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lowongan_kerja', function (Blueprint $t) {
            if (! Schema::hasColumn('lowongan_kerja', 'kategori')) {
                $t->string('kategori', 100)->nullable()->after('judul');
            }
            if (! Schema::hasColumn('lowongan_kerja', 'gaji_min')) {
                $t->unsignedInteger('gaji_min')->nullable()->after('kuota'); // rupiah
            }
            if (! Schema::hasColumn('lowongan_kerja', 'gaji_max')) {
                $t->unsignedInteger('gaji_max')->nullable()->after('gaji_min');
            }
            if (! Schema::hasColumn('lowongan_kerja', 'urgent')) {
                $t->boolean('urgent')->default(false)->after('gaji_max');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lowongan_kerja', function (Blueprint $t) {
            $t->dropColumn(['kategori', 'gaji_min', 'gaji_max', 'urgent']);
        });
    }
};