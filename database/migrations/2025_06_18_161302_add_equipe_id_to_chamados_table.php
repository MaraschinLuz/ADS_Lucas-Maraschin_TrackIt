<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::table('chamados', function (Blueprint $table) {
        $table->foreignId('equipe_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    
    public function down(): void
    {
        Schema::table('chamados', function (Blueprint $table) {
            
        });
    }
};
