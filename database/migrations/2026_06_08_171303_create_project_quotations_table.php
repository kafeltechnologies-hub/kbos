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
Schema::create('project_quotations', function (Blueprint $table) {
    $table->id();

    $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();

    $table->string('quotation_code')->unique();
    $table->string('quotation_number')->unique();

    $table->date('quotation_date');
    $table->date('valid_until')->nullable();

    $table->string('client_name')->nullable();
    $table->string('client_phone')->nullable();
    $table->string('client_email')->nullable();
    $table->string('client_tin')->nullable();
    $table->text('client_address')->nullable();

    $table->string('project_title')->nullable();
    $table->text('scope_of_work')->nullable();

    $table->decimal('subtotal', 18, 2)->default(0);
    $table->boolean('vat_applicable')->default(false);
    $table->decimal('vat_amount', 18, 2)->default(0);
    $table->decimal('getfund_amount', 18, 2)->default(0);
    $table->decimal('nhil_amount', 18, 2)->default(0);
    $table->decimal('grand_total', 18, 2)->default(0);

    $table->text('amount_in_words')->nullable();
    $table->text('terms_and_conditions')->nullable();
    $table->text('notes')->nullable();

    $table->string('prepared_by')->nullable();
    $table->string('approved_by')->nullable();

    $table->string('status')->default('draft');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_quotations');
    }
};
