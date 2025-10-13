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
    Schema::create('barang_kondisis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('barang_id')->constrained()->onDelete('cascade');
        $table->string('kondisi'); // Baik, Rusak Ringan, Rusak Berat
        $table->integer('jumlah'); // misal 7
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_kondisis');
    }
};