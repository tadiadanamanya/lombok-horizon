<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('kavlings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('nomor');
            $table->double('luas_m2')->nullable();
            $table->double('harga')->nullable();
            $table->enum('status', ['available', 'booked', 'sold', 'disabled'])->default('available');
            $table->json('koordinat_bidang')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'nomor']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('kavlings');
    }
};