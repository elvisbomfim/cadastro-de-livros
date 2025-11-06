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
        Schema::create('livro_autor', function (Blueprint $table) {
            $table->uuid('livro_id');
            $table->uuid('autor_id');

            $table->foreign('livro_id')->references('id')->on('livro')->onDelete('cascade');
            $table->foreign('autor_id')->references('id')->on('autor')->onDelete('cascade');
            
            $table->primary(['livro_id', 'autor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livro_autor');
    }
};
