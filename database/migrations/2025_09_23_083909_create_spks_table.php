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
        Schema::create('spks', function (Blueprint $table) {
            $table->id();
            $table->string('spk_number')->unique(); // Nomor SPK unik
            $table->string('customer_name');
            $table->string('order_name');
            $table->date('entry_date');
            $table->date('delivery_date');
            $table->string('material');
            $table->text('description')->nullable();
            $table->integer('total_qty');
            $table->float('total_meter')->nullable(); // Bisa nullable karena mungkin diisi belakangan
            $table->string('design_image_path')->nullable();
            $table->enum('status', ['In Progress', 'Closed', 'Rejected'])->default('In Progress');
            $table->decimal('price_per_meter', 10, 2)->nullable();
            $table->date('closed_date')->nullable();
            $table->boolean('is_design_done')->default(false);
            $table->boolean('is_print_done')->default(false);
            $table->boolean('is_press_done')->default(false);
            $table->boolean('is_delivery_done')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spks');
    }
};
