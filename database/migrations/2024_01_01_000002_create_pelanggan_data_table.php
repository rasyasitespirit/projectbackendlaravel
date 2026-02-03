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
        Schema::create('pelanggan_data', function (Blueprint $table) {
            $table->id('pelanggan_data_id');
            $table->unsignedBigInteger('pelanggan_data_pelanggan_id');
            $table->enum('pelanggan_data_jenis', ['KTP', 'SIM']);
            $table->string('pelanggan_data_file', 255);
            $table->timestamps();

            // Foreign key
            $table->foreign('pelanggan_data_pelanggan_id')
                  ->references('pelanggan_id')
                  ->on('pelanggan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan_data');
    }
};
