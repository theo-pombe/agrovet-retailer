<?php

use App\Enums\CustomerType;
use App\Enums\Status;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', CustomerType::values())->default(CustomerType::INDIVIDUAL->value);
            $table->string('full_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', array_map(fn($s) => $s->value, Status::customerStatuses()))
                ->default(Status::ACTIVE->value);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->unsignedInteger('total_visits')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
