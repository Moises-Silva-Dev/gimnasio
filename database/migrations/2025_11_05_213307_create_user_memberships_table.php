<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('user_memberships');
        Schema::create('user_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('membership_id')->constrained('memberships')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('remaining_sessions');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->decimal('payment_amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_memberships');
    }
};
