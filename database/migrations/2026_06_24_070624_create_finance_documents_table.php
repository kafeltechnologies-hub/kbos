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
       Schema::create('finance_documents', function (Blueprint $table) {
            $table->id();

            $table->string('document_type'); // quotation, invoice
            $table->string('document_no')->unique();
            $table->date('document_date')->nullable();

            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();

            $table->string('customer_name')->nullable();
            $table->text('service_description')->nullable();

            $table->decimal('materials_total', 18, 2)->default(0);
            $table->decimal('labour_cost', 18, 2)->default(0);
            $table->decimal('transport_cost', 18, 2)->default(0);
            $table->decimal('other_cost', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);

            $table->string('source_quotation_no')->nullable(); // invoice converted from quotation

            $table->string('status')->default('draft'); // draft, posted, approved, reversed, cancelled
            $table->text('narration')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_documents');
    }
};
