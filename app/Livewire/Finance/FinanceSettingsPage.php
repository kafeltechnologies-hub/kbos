<?php

namespace App\Livewire\Finance;

use App\Models\ChartOfAccount;
use App\Models\FinancialSetting;
use Illuminate\Support\Facades\Schema;

class FinanceSettingsPage extends FinanceBasePage
{
    public $cash_account_id = '';
    public $accounts_receivable_id = '';
    public $accounts_payable_id = '';
    public $inventory_account_id = '';
    public $fixed_asset_account_id = '';
    public $accumulated_depreciation_account_id = '';
    public $depreciation_expense_account_id = '';
    public $sales_revenue_account_id = '';
    public $material_revenue_account_id = '';
    public $project_cost_account_id = '';
    public $tax_payable_account_id = '';
    public $withholding_tax_account_id = '';

    public $vat_rate = 15;
    public $nhil_rate = 2.5;
    public $getfund_rate = 2.5;
    public $wht_rate = 5;
    public $paye_enabled = 1;

    public $auto_post_fixed_assets = true;
    public $auto_post_materials = true;
    public $auto_post_receipts = true;
    public $auto_post_payments = true;
    public $auto_post_invoices = true;
    public $lock_posted_entries = true;
    public $require_approval_before_posting = false;
    public $allow_backdated_posting = true;

    public $glSyncEnabled = true;
    public $taxEnabled = true;
    public $financial_year;

    public function mount(): void
    {
        $this->financial_year = now()->year;

        if (! Schema::hasTable('financial_settings')) {
            return;
        }

        $settings = FinancialSetting::query()->first();

        if (! $settings) {
            return;
        }

        foreach ($this->settingFields() as $field) {
            if (isset($settings->{$field})) {
                $this->{$field} = $settings->{$field};
            }
        }
    }

    public function saveSettings(): void
    {
        if (! Schema::hasTable('financial_settings')) {
            session()->flash('error', 'financial_settings table does not exist.');
            return;
        }

        $this->validate([
            'vat_rate' => 'nullable|numeric|min:0',
            'nhil_rate' => 'nullable|numeric|min:0',
            'getfund_rate' => 'nullable|numeric|min:0',
            'wht_rate' => 'nullable|numeric|min:0',
        ]);

        $data = [];

        foreach ($this->settingFields() as $field) {
            $data[$field] = $this->{$field} === '' ? null : $this->{$field};
        }

        FinancialSetting::query()->updateOrCreate(
            ['id' => 1],
            $data
        );

        session()->flash('success', 'Finance settings saved successfully.');
    }

    public function resetSettings(): void
    {
        foreach ($this->accountFields() as $field) {
            $this->{$field} = '';
        }

        $this->vat_rate = 15;
        $this->nhil_rate = 2.5;
        $this->getfund_rate = 2.5;
        $this->wht_rate = 5;
        $this->paye_enabled = 1;

        $this->auto_post_fixed_assets = true;
        $this->auto_post_materials = true;
        $this->auto_post_receipts = true;
        $this->auto_post_payments = true;
        $this->auto_post_invoices = true;
        $this->lock_posted_entries = true;
        $this->require_approval_before_posting = false;
        $this->allow_backdated_posting = true;
    }

    public function render()
    {
        $accounts = Schema::hasTable('chart_of_accounts')
            ? ChartOfAccount::query()
                ->orderBy('account_code')
                ->orderBy('account_name')
                ->get()
            : collect();

        return view('livewire.finance.finance-settings-page', [
            'accounts' => $accounts,
            'glSyncEnabled' => $this->glSyncEnabled,
            'taxEnabled' => $this->taxEnabled,
            'financial_year' => $this->financial_year,
            'financeNavLinks' => $this->financeNavLinks(),
        ])->layout($this->layoutName());
    }

    protected function accountFields(): array
    {
        return [
            'cash_account_id',
            'accounts_receivable_id',
            'accounts_payable_id',
            'inventory_account_id',
            'fixed_asset_account_id',
            'accumulated_depreciation_account_id',
            'depreciation_expense_account_id',
            'sales_revenue_account_id',
            'material_revenue_account_id',
            'project_cost_account_id',
            'tax_payable_account_id',
            'withholding_tax_account_id',
        ];
    }

    protected function settingFields(): array
    {
        return array_merge($this->accountFields(), [
            'vat_rate',
            'nhil_rate',
            'getfund_rate',
            'wht_rate',
            'paye_enabled',
            'auto_post_fixed_assets',
            'auto_post_materials',
            'auto_post_receipts',
            'auto_post_payments',
            'auto_post_invoices',
            'lock_posted_entries',
            'require_approval_before_posting',
            'allow_backdated_posting',
        ]);
    }
}