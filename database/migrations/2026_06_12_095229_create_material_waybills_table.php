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
        Schema::create('material_waybills', function (Blueprint $table) {

            $table->id();

            $table->string('waybill_no')->unique();

            $table->foreignId('transaction_id')
                ->constrained('material_transactions');

            $table->string('transporter_name')->nullable();

            $table->string('driver_name')->nullable();

            $table->string('driver_phone')->nullable();

            $table->string('vehicle_number')->nullable();

            $table->string('delivery_location')->nullable();

            $table->string('loaded_by')->nullable();

            $table->string('received_by')->nullable();

            $table->enum('status',[
                'draft',
                'issued',
                'delivered'
            ])->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_waybills');
    }
};
