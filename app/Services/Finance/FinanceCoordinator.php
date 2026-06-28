<?php

namespace App\Services\Finance;

use App\Models\GeneralLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class FinanceCoordinator
{
    public function postDoubleEntry(array $data): void
    {
        if (! Schema::hasTable('general_ledgers')) {
            throw new InvalidArgumentException('general_ledgers table does not exist.');
        }

        $date = $data['date'] ?? now()->toDateString();
        $reference = $data['reference'] ?? 'GL' . now()->format('YmdHis');
        $narration = $data['narration'] ?? null;
        $sourceModule = $data['source_module'] ?? 'finance';
        $sourceType = $data['source_type'] ?? 'manual';
        $sourceId = $data['source_id'] ?? null;
        $projectId = $data['project_id'] ?? null;

        $lines = $data['lines'] ?? [];

        $debit = collect($lines)->sum(fn ($line) => (float) ($line['debit'] ?? 0));
        $credit = collect($lines)->sum(fn ($line) => (float) ($line['credit'] ?? 0));

        if (round($debit, 2) !== round($credit, 2)) {
            throw new InvalidArgumentException('Finance posting is not balanced. Debit must equal credit.');
        }

        DB::transaction(function () use (
            $lines,
            $date,
            $reference,
            $narration,
            $sourceModule,
            $sourceType,
            $sourceId,
            $projectId
        ) {
            GeneralLedger::where('source_module', $sourceModule)
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->delete();

            foreach ($lines as $line) {
                GeneralLedger::create([
                    'entry_date' => $date,
                    'transaction_date' => $date,
                    'date' => $date,

                    'account_code' => $line['account_code'] ?? null,
                    'account_name' => $line['account_name'],
                    'account' => $line['account_name'],

                    'description' => $line['description'] ?? $narration,
                    'narration' => $narration,

                    'debit' => (float) ($line['debit'] ?? 0),
                    'debit_amount' => (float) ($line['debit'] ?? 0),
                    'credit' => (float) ($line['credit'] ?? 0),
                    'credit_amount' => (float) ($line['credit'] ?? 0),
                    'amount' => max((float) ($line['debit'] ?? 0), (float) ($line['credit'] ?? 0)),

                    'project_id' => $line['project_id'] ?? $projectId,

                    'source_module' => $sourceModule,
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,

                    'reference' => $reference,
                    'status' => 'posted',
                    'created_by' => auth()->id(),
                ]);
            }
        });
    }

    public function postFixedAssetPurchase(object|array $asset): void
    {
        $asset = (object) $asset;

        $this->postDoubleEntry([
            'date' => $asset->purchase_date ?? now()->toDateString(),
            'reference' => $asset->asset_code,
            'narration' => 'Fixed asset purchase: ' . $asset->asset_name,
            'source_module' => 'fixed_assets',
            'source_type' => 'asset_purchase',
            'source_id' => $asset->id,
            'project_id' => $asset->project_id ?? null,
            'lines' => [
                [
                    'account_code' => '1500',
                    'account_name' => 'Fixed Assets',
                    'debit' => $asset->purchase_cost,
                    'credit' => 0,
                    'description' => 'Asset capitalised: ' . $asset->asset_name,
                ],
                [
                    'account_code' => '2000',
                    'account_name' => 'Accounts Payable',
                    'debit' => 0,
                    'credit' => $asset->purchase_cost,
                    'description' => 'Payable for asset: ' . $asset->asset_name,
                ],
            ],
        ]);
    }

    public function postAssetDepreciation(object|array $asset, float $amount, ?string $date = null): void
    {
        $asset = (object) $asset;

        $this->postDoubleEntry([
            'date' => $date ?? now()->toDateString(),
            'reference' => $asset->asset_code . '-DEP',
            'narration' => 'Depreciation for fixed asset: ' . $asset->asset_name,
            'source_module' => 'fixed_assets',
            'source_type' => 'asset_depreciation',
            'source_id' => $asset->id,
            'project_id' => $asset->project_id ?? null,
            'lines' => [
                [
                    'account_code' => '6600',
                    'account_name' => 'Depreciation Expense',
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account_code' => '1600',
                    'account_name' => 'Accumulated Depreciation',
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ],
        ]);
    }

    public function postMaterialReceipt(object|array $transaction, float $amount): void
    {
        $transaction = (object) $transaction;

        $this->postDoubleEntry([
            'date' => $transaction->transaction_date ?? now()->toDateString(),
            'reference' => $transaction->transaction_no,
            'narration' => 'Stock received into inventory',
            'source_module' => 'materials',
            'source_type' => 'stock_received',
            'source_id' => $transaction->id,
            'project_id' => $transaction->project_id ?? null,
            'lines' => [
                [
                    'account_code' => '1200',
                    'account_name' => 'Inventory Asset',
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account_code' => '2010',
                    'account_name' => 'Supplier Payable',
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ],
        ]);
    }

    public function postMaterialIssueToProject(object|array $transaction, float $amount): void
    {
        $transaction = (object) $transaction;

        $this->postDoubleEntry([
            'date' => $transaction->transaction_date ?? now()->toDateString(),
            'reference' => $transaction->transaction_no,
            'narration' => 'Stock issued to project',
            'source_module' => 'materials',
            'source_type' => 'issue_to_project',
            'source_id' => $transaction->id,
            'project_id' => $transaction->project_id ?? null,
            'lines' => [
                [
                    'account_code' => '5010',
                    'account_name' => 'Project Material Cost',
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account_code' => '1200',
                    'account_name' => 'Inventory Asset',
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ],
        ]);
    }

    public function postMaterialReturnFromProject(object|array $transaction, float $amount): void
    {
        $transaction = (object) $transaction;

        $this->postDoubleEntry([
            'date' => $transaction->transaction_date ?? now()->toDateString(),
            'reference' => $transaction->transaction_no,
            'narration' => 'Stock returned from project',
            'source_module' => 'materials',
            'source_type' => 'return_from_project',
            'source_id' => $transaction->id,
            'project_id' => $transaction->project_id ?? null,
            'lines' => [
                [
                    'account_code' => '1200',
                    'account_name' => 'Inventory Asset',
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account_code' => '5010',
                    'account_name' => 'Project Material Cost',
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ],
        ]);
    }

    public function postMaterialBorrowedOut(object|array $transaction, float $amount): void
    {
        $transaction = (object) $transaction;

        $this->postDoubleEntry([
            'date' => $transaction->transaction_date ?? now()->toDateString(),
            'reference' => $transaction->transaction_no,
            'narration' => 'Stock borrowed out',
            'source_module' => 'materials',
            'source_type' => 'borrowed_out',
            'source_id' => $transaction->id,
            'lines' => [
                [
                    'account_code' => '1110',
                    'account_name' => 'Material Receivables',
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account_code' => '1200',
                    'account_name' => 'Inventory Asset',
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ],
        ]);
    }

    public function postBorrowedStockReturned(object|array $transaction, float $amount): void
    {
        $transaction = (object) $transaction;

        $this->postDoubleEntry([
            'date' => $transaction->transaction_date ?? now()->toDateString(),
            'reference' => $transaction->transaction_no,
            'narration' => 'Borrowed stock returned',
            'source_module' => 'materials',
            'source_type' => 'borrowed_returned',
            'source_id' => $transaction->id,
            'lines' => [
                [
                    'account_code' => '1200',
                    'account_name' => 'Inventory Asset',
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account_code' => '1110',
                    'account_name' => 'Material Receivables',
                    'debit' => 0,
                    'credit' => $amount,
                ],
            ],
        ]);
    }

    public function postMaterialSale(object|array $transaction, float $costAmount, float $saleAmount): void
    {
        $transaction = (object) $transaction;

        $this->postDoubleEntry([
            'date' => $transaction->transaction_date ?? now()->toDateString(),
            'reference' => $transaction->transaction_no,
            'narration' => 'Material sale',
            'source_module' => 'materials',
            'source_type' => 'material_sale',
            'source_id' => $transaction->id,
            'lines' => [
                [
                    'account_code' => '1100',
                    'account_name' => 'Accounts Receivable',
                    'debit' => $saleAmount,
                    'credit' => 0,
                ],
                [
                    'account_code' => '4010',
                    'account_name' => 'Material Sales Revenue',
                    'debit' => 0,
                    'credit' => $saleAmount,
                ],
                [
                    'account_code' => '5000',
                    'account_name' => 'Cost Of Goods Sold',
                    'debit' => $costAmount,
                    'credit' => 0,
                ],
                [
                    'account_code' => '1200',
                    'account_name' => 'Inventory Asset',
                    'debit' => 0,
                    'credit' => $costAmount,
                ],
            ],
        ]);
    }

    public function reverseSource(string $sourceModule, string $sourceType, int $sourceId): void
    {
        GeneralLedger::where('source_module', $sourceModule)
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->update([
                'status' => 'reversed',
                'updated_at' => now(),
            ]);
    }

    public function deleteSourcePostings(string $sourceModule, string $sourceType, int $sourceId): void
    {
        GeneralLedger::where('source_module', $sourceModule)
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->delete();
    }
}