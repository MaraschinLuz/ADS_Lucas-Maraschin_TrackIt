<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_role_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','role')) {
                $table->string('role')->default('usuario')->after('password');
            } else {
                $table->string('role')->default('usuario')->change();
            }
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','role')) {
                $table->dropColumn('role');
            }
        });
    }
};

