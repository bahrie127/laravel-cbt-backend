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
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();
            //user_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            //nilai angka, verbal, dan logika
            $table->integer('nilai_angka')->nullable();
            $table->integer('nilai_verbal')->nullable();
            $table->integer('nilai_logika')->nullable();
            //status ujian enum (start, done)
            $table->enum('status_angka', ['start', 'done'])->default('start');
            $table->enum('status_verbal', ['start', 'done'])->default('start');
            $table->enum('status_logika', ['start', 'done'])->default('start');
            //timer ujian per kategori
            $table->integer('timer_angka')->nullable();
            $table->integer('timer_verbal')->nullable();
            $table->integer('timer_logika')->nullable();
            //hasil
            $table->string('hasil')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujians');
    }
};
