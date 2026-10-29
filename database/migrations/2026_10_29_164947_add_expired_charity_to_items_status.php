<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('searching', 'matched', 'resolved', 'expired_charity') DEFAULT 'searching'");
        } else {
            Schema::table('items', function (Blueprint $table) {
                $table->string('status', 50)->default('searching')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('searching', 'matched', 'resolved') DEFAULT 'searching'");
        } else {
            Schema::table('items', function (Blueprint $table) {
                $table->string('status', 50)->default('searching')->change();
            });
        }
    }
};
