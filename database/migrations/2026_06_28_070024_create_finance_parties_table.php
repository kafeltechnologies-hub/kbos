<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_parties', function (Blueprint $table) {
            $table->id();

            $table->string('party_code')->unique();
            $table->string('party_type')->default('customer');
            // customer, supplier, employee, bank, lender, director, shareholder, government, utility, contractor, other

            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('email')->nullable();

            $table->string('tin')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('registration_number')->nullable();

            $table->string('gps_address')->nullable();
            $table->string('postal_address')->nullable();
            $table->string('physical_address')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('momo_number')->nullable();

            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->string('currency')->default('GHS');

            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['party_type', 'active']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_parties');
    }
};