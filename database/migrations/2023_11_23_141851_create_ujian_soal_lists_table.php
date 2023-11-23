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
        Schema::create('ujian_soal_lists', function (Blueprint $table) {
            $table->id();
            //ujian_id
            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            //soal_id
            $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
            //kebenaran
            $table->boolean('kebenaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_soal_lists');
    }
};
