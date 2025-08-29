<?php

use App\Enums\PaymentStatus;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Polymorphic link (Purchase or Sale)
            $table->string('invoiceable_type');
            $table->unsignedBigInteger('invoiceable_id');

            // Invoice details
            $table->string('invoice_number')->unique(); // auto-generated daily reset
            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            // Financials
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);

            // Status (pending, partial, paid)
            $table->enum('status', PaymentStatus::values())->default(PaymentStatus::UNPAID->value);

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for polymorphic relation
            $table->index(['invoiceable_type', 'invoiceable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
