<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_memberships', function (Blueprint $table) {
            $table->date('last_session_reset')->nullable()->after('remaining_sessions');
        });
    }

    public function down(): void
    {
        Schema::table('user_memberships', function (Blueprint $table) {
            $table->dropColumn('last_session_reset');
        });
    }
};
