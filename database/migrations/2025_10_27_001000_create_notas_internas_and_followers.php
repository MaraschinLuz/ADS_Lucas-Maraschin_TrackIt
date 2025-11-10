<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notas_internas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chamado_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('nota');
            $table->timestamps();
        });

        Schema::create('chamado_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chamado_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['chamado_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chamado_followers');
        Schema::dropIfExists('notas_internas');
    }
};

