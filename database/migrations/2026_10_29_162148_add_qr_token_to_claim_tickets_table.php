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
        Schema::table('claim_tickets', function (Blueprint $table) {
            $table->string('qr_token')->nullable()->after('admin_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_tickets', function (Blueprint $table) {
            $table->dropColumn('qr_token');
        });
    }
};
