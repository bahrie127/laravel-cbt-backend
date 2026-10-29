<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alur ujian: waktu mulai per kategori (timer dihitung server), jawaban peserta tersimpan
     * (bisa dilanjutkan), soal panjang, dan gambar materi/konten opsional.
     */
    public function up(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->text('pertanyaan')->change();
        });

        Schema::table('ujians', function (Blueprint $table) {
            $table->timestamp('mulai_angka_at')->nullable()->after('timer_logika');
            $table->timestamp('mulai_verbal_at')->nullable()->after('mulai_angka_at');
            $table->timestamp('mulai_logika_at')->nullable()->after('mulai_verbal_at');
            $table->timestamp('selesai_at')->nullable()->after('hasil');
        });

        Schema::table('ujian_soal_lists', function (Blueprint $table) {
            $table->char('jawaban', 1)->nullable()->after('soal_id');
            $table->unique(['ujian_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::table('ujian_soal_lists', function (Blueprint $table) {
            $table->dropUnique(['ujian_id', 'soal_id']);
            $table->dropColumn('jawaban');
        });

        Schema::table('ujians', function (Blueprint $table) {
            $table->dropColumn(['mulai_angka_at', 'mulai_verbal_at', 'mulai_logika_at', 'selesai_at']);
        });

        Schema::table('soals', function (Blueprint $table) {
            $table->string('pertanyaan')->change();
        });
    }
};
