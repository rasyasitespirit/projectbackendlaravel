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
        Schema::create('penyewaan_detail', function (Blueprint $table) {
            $table->id('penyewaan_detail_id');
            $table->unsignedBigInteger('penyewaan_detail_penyewaan_id');
            $table->unsignedBigInteger('penyewaan_detail_alat_id');
            $table->integer('penyewaan_detail_jumlah');
            $table->integer('penyewaan_detail_subharga');
            $table->timestamps();

            // Foreign keys
            $table->foreign('penyewaan_detail_penyewaan_id')
                  ->references('penyewaan_id')
                  ->on('penyewaan')
                  ->onDelete('cascade');

            $table->foreign('penyewaan_detail_alat_id')
                  ->references('alat_id')
                  ->on('alat')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyewaan_detail');
    }
};
