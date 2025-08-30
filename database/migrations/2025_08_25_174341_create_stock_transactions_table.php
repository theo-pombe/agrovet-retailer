<?php

use App\Enums\Status;
use App\Enums\TransactionType;
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
        // TODO: How to auto-update stocks.stock_level from stock_transactions (via Eloquent events or database triggers), so that the snapshot always stays in sync?

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->enum('type', TransactionType::values());
            $table->integer('quantity');

            // Purchase-related fields
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');

            // Sale-related fields
            $table->decimal('retail_price', 15, 2)->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers');

            // General
            $table->morphs('transactionable');
            $table->date('transacted_date')->useCurrent();
            $table->enum('status', Status::generalStatusValues())
                ->default(Status::ACTIVE->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
