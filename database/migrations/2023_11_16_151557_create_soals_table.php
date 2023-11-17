<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->string('pertanyaan');
            //kategori
            $table->string('kategori');
            //jawaban_a
            $table->string('jawaban_a');
            //jawaban_b
            $table->string('jawaban_b');
            //jawaban_c
            $table->string('jawaban_c');
            //jawaban_d
            $table->string('jawaban_d');
            //kunci
            $table->enum('kunci', ['a', 'b', 'c', 'd']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
