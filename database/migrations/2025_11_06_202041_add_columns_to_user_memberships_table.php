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
        // Si la tabla ya existe, solo agregamos las columnas faltantes
        if (Schema::hasTable('user_memberships')) {
            Schema::table('user_memberships', function (Blueprint $table) {
                if (!Schema::hasColumn('user_memberships', 'remaining_sessions')) {
                    $table->integer('remaining_sessions')->nullable()->after('amount_paid');
                }
                if (!Schema::hasColumn('user_memberships', 'payment_method')) {
                    $table->enum('payment_method', ['cash', 'card', 'transfer', 'other'])->default('cash')->after('amount_paid');
                }
                if (!Schema::hasColumn('user_memberships', 'payment_amount')) {
                    $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('user_memberships', 'notes')) {
                    $table->text('notes')->nullable()->after('status');
                }
            });
        } else {
            // Si la tabla no existe, la creamos completa
            Schema::create('user_memberships', function (Blueprint $table) {
                $table->id();
                $table->foreignId('gym_id')->constrained('gyms')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('membership_id')->constrained('memberships')->onDelete('cascade');
                $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('amount_paid', 10, 2);
                $table->integer('remaining_sessions')->nullable();
                $table->enum('payment_method', ['cash', 'card', 'transfer', 'other'])->default('cash');
                $table->decimal('payment_amount', 10, 2)->nullable();
                $table->enum('status', ['active', 'expired', 'pending', 'cancelled'])->default('pending');
                $table->text('notes')->nullable();

                $table->timestamps();

                // Índices para mejorar el rendimiento
                $table->index(['gym_id', 'status']);
                $table->index(['user_id', 'status']);
                $table->index(['start_date', 'end_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No eliminamos la tabla en el rollback si ya existía
        // Solo eliminaríamos las columnas nuevas si es necesario
        if (Schema::hasTable('user_memberships')) {
            Schema::table('user_memberships', function (Blueprint $table) {
                if (Schema::hasColumn('user_memberships', 'remaining_sessions')) {
                    $table->dropColumn('remaining_sessions');
                }
                if (Schema::hasColumn('user_memberships', 'payment_method')) {
                    $table->dropColumn('payment_method');
                }
                if (Schema::hasColumn('user_memberships', 'payment_amount')) {
                    $table->dropColumn('payment_amount');
                }
                if (Schema::hasColumn('user_memberships', 'notes')) {
                    $table->dropColumn('notes');
                }
            });
        }
    }
};
