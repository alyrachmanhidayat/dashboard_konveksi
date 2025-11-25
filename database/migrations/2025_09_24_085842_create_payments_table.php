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
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->date('payment_date');
                $table->timestamps();
            });
        } else {
            // If table exists, check if columns are missing and add them
            if (!Schema::hasColumn('payments', 'invoice_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
                });
            }
            if (!Schema::hasColumn('payments', 'amount')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->decimal('amount', 10, 2);
                });
            }
            if (!Schema::hasColumn('payments', 'payment_date')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->date('payment_date');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
