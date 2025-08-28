<?php

use App\Enums\PurchaseSaleStatus;
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
        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->dateTime('order_date')->default(now());
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', PurchaseSaleStatus::values())->default(PurchaseSaleStatus::PENDING->value);
            $table->date('expected_date')->nullable(); // when delivery expected
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_orders');
    }
};
