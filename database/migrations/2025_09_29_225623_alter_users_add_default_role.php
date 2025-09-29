<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Garante que a coluna 'role' exista e define o default como 'usuario'
            $table->string('role')->default('usuario')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Se quiser reverter, remove o default
            $table->string('role')->default(null)->change();
        });
    }
};
