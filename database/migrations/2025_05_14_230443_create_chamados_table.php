<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('chamados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); 
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('prioridade', ['baixa', 'media', 'alta'])->default('media');
            $table->string('status')->default('aberto');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('chamados');
    }
};
