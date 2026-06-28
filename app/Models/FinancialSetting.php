<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialSetting extends Model
{
    protected $table = 'financial_settings';

    protected $fillable = [
        'cash_account_id',
        'bank_account_id',
        'accounts_receivable_id',
        'accounts_payable_id',

        'sales_revenue_id',
        'service_revenue_id',
        'purchase_expense_id',
        'project_cost_id',

        'vat_payable_id',
        'withholding_tax_payable_id',
        'nhil_payable_id',
        'getfund_payable_id',
        'paye_payable_id',
        'ssnit_payable_id',

        'retained_earnings_id',

        'vat_rate',
        'nhil_rate',
        'getfund_rate',
        'withholding_tax_rate',
    ];

    /*
    |--------------------------------------------------------------------------
    | Asset Accounts
    |--------------------------------------------------------------------------
    */

    public function cashAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'cash_account_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'bank_account_id');
    }

    public function accountsReceivable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'accounts_receivable_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Liability Accounts
    |--------------------------------------------------------------------------
    */

    public function accountsPayable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'accounts_payable_id');
    }

    public function vatPayable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'vat_payable_id');
    }

    public function withholdingTaxPayable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'withholding_tax_payable_id');
    }

    public function nhilPayable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'nhil_payable_id');
    }

    public function getfundPayable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'getfund_payable_id');
    }

    public function payePayable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'paye_payable_id');
    }

    public function ssnitPayable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'ssnit_payable_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Income Accounts
    |--------------------------------------------------------------------------
    */

    public function salesRevenue()
    {
        return $this->belongsTo(ChartOfAccount::class, 'sales_revenue_id');
    }

    public function serviceRevenue()
    {
        return $this->belongsTo(ChartOfAccount::class, 'service_revenue_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Expense Accounts
    |--------------------------------------------------------------------------
    */

    public function purchaseExpense()
    {
        return $this->belongsTo(ChartOfAccount::class, 'purchase_expense_id');
    }

    public function projectCost()
    {
        return $this->belongsTo(ChartOfAccount::class, 'project_cost_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Equity
    |--------------------------------------------------------------------------
    */

    public function retainedEarnings()
    {
        return $this->belongsTo(ChartOfAccount::class, 'retained_earnings_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'vat_rate' => 15,
                'nhil_rate' => 2.5,
                'getfund_rate' => 2.5,
                'withholding_tax_rate' => 5,
            ]
        );
    }
}