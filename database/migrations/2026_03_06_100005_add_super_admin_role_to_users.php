<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Altera o enum para incluir 'super_admin'
        // MariaDB/MySQL: modifica a coluna role para aceitar o novo valor
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','admin','manager','employee') NOT NULL DEFAULT 'employee'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','employee') NOT NULL DEFAULT 'employee'");
    }
};
