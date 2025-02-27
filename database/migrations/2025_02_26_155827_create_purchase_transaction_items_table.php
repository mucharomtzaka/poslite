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
        Schema::create('purchase_transaction_items', function (Blueprint $table) {
            $table->id();
            // Nullable foreign key for purchase_transaction_id
            $table->foreignId('purchase_transaction_id')->nullable(); // Sets NULL instead of deleting the row
            
            // Nullable foreign key for product_id
            $table->foreignId('product_id')
                ->nullable();
            $table->integer('quantity');
            $table->decimal('purchase_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_transaction_items');
    }
};
