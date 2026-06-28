<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            ALTER TABLE material_transactions
            DROP CONSTRAINT IF EXISTS material_transactions_transaction_type_check
        ');

        DB::statement("
            ALTER TABLE material_transactions
            ADD CONSTRAINT material_transactions_transaction_type_check
            CHECK (transaction_type IN (
                'purchase_resale',
                'receive',
                'issue_project',
                'return_project',
                'transfer_project',
                'issue_sale',
                'issue_account',
                'return_account',
                'adjustment'
            ))
        ");
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE material_transactions
            DROP CONSTRAINT IF EXISTS material_transactions_transaction_type_check
        ');

        DB::statement("
            ALTER TABLE material_transactions
            ADD CONSTRAINT material_transactions_transaction_type_check
            CHECK (transaction_type IN (
                'receive',
                'issue_project',
                'issue_sale',
                'adjustment'
            ))
        ");
    }
};