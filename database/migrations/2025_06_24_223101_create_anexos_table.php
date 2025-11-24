<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
public function up()
{
    Schema::create('anexos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('chamado_id')->constrained()->onDelete('cascade');
        $table->string('arquivo'); 
        $table->timestamps();
    });
}

    
    public function down(): void
    {
        Schema::dropIfExists('anexos');
    }
};