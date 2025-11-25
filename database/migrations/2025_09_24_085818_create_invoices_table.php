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
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('spk_id')->constrained()->onDelete('cascade');
                $table->string('customer_name');
                $table->string('order_name');
                $table->integer('total_qty');
                $table->decimal('total_amount', 10, 2);
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->boolean('is_paid')->default(false);
                $table->timestamps();
            });
        } else {
            // If table exists, check if columns are missing and add them
            if (!Schema::hasColumn('invoices', 'invoice_number')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->string('invoice_number')->unique();
                });
            }
            if (!Schema::hasColumn('invoices', 'spk_id')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->foreignId('spk_id')->constrained()->onDelete('cascade');
                });
            }
            if (!Schema::hasColumn('invoices', 'customer_name')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->string('customer_name');
                });
            }
            if (!Schema::hasColumn('invoices', 'order_name')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->string('order_name');
                });
            }
            if (!Schema::hasColumn('invoices', 'total_qty')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->integer('total_qty');
                });
            }
            if (!Schema::hasColumn('invoices', 'total_amount')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->decimal('total_amount', 10, 2);
                });
            }
            if (!Schema::hasColumn('invoices', 'paid_amount')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->decimal('paid_amount', 10, 2)->default(0);
                });
            }
            if (!Schema::hasColumn('invoices', 'is_paid')) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->boolean('is_paid')->default(false);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
