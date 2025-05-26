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
        Schema::table('origem_dado', function (Blueprint $table) {
            $table->foreignId('idConflito')->constrained('conflito')->after('idOrigemDado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('origem_dado', function (Blueprint $table) {
            $table->dropColumn('idConflito');
        });
    }
};
