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
        Schema::create('project_payments', function (Blueprint $table) {
                $table->id();

                $table->foreignId('project_id')->constrained()->cascadeOnDelete();

                $table->string('payment_code')->unique();

                $table->decimal('gross_amount', 18, 2)->default(0);
                $table->decimal('withholding_tax', 18, 2)->default(0);
                $table->decimal('net_amount', 18, 2)->default(0);

                $table->date('payment_date')->nullable();

                $table->string('payment_method')->nullable();
                $table->string('reference')->nullable();

                $table->text('remarks')->nullable();

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_payments');
    }
};
