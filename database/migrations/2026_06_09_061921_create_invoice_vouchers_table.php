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
        Schema::create('invoice_vouchers', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_quotation_id')->nullable()->constrained()->nullOnDelete();

            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_tin')->nullable();
            $table->text('client_address')->nullable();

            $table->string('project_title')->nullable();
            $table->text('scope_of_work')->nullable();

            $table->decimal('contract_value', 18, 2)->default(0);
            $table->decimal('previous_invoices', 18, 2)->default(0);
            $table->decimal('outstanding_before_invoice', 18, 2)->default(0);

            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('labor_charge', 18, 2)->default(0);
            $table->decimal('transport_charge', 18, 2)->default(0);
            $table->decimal('other_charges', 18, 2)->default(0);
            $table->string('other_charges_description')->nullable();

            $table->boolean('vat_applicable')->default(false);
            $table->decimal('vat_amount', 18, 2)->default(0);
            $table->decimal('getfund_amount', 18, 2)->default(0);
            $table->decimal('nhil_amount', 18, 2)->default(0);

            $table->decimal('grand_total', 18, 2)->default(0);
            $table->decimal('balance_after_invoice', 18, 2)->default(0);

            $table->text('amount_in_words')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();

            $table->string('status')->default('draft');

            $table->string('prepared_by')->nullable();
            $table->string('checked_by')->nullable();
            $table->string('approved_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_vouchers');
    }
};
