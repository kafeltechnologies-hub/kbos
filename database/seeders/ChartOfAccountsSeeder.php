<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [

            /*
            |--------------------------------------------------------------------------
            | 1000 - ASSETS
            |--------------------------------------------------------------------------
            */
            [
                'account_code' => '1000',
                'account_name' => 'Assets',
                'account_type' => 'Asset',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '1100',
                'account_name' => 'Current Assets',
                'account_type' => 'Asset',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '1110',
                'account_name' => 'Cash at Hand',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1120',
                'account_name' => 'Bank Account',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1130',
                'account_name' => 'Mobile Money Wallet',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1140',
                'account_name' => 'Petty Cash',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1200',
                'account_name' => 'Accounts Receivable',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1210',
                'account_name' => 'Trade Debtors',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1300',
                'account_name' => 'Inventory',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1310',
                'account_name' => 'Electrical Materials Inventory',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1320',
                'account_name' => 'ICT Equipment Inventory',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1400',
                'account_name' => 'Prepayments',
                'account_type' => 'Asset',
                'account_group' => 'Current Asset',
            ],
            [
                'account_code' => '1500',
                'account_name' => 'Fixed Assets',
                'account_type' => 'Asset',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '1510',
                'account_name' => 'Motor Vehicles',
                'account_type' => 'Asset',
                'account_group' => 'Fixed Asset',
            ],
            [
                'account_code' => '1520',
                'account_name' => 'Tools and Equipment',
                'account_type' => 'Asset',
                'account_group' => 'Fixed Asset',
            ],
            [
                'account_code' => '1530',
                'account_name' => 'Office Furniture and Fittings',
                'account_type' => 'Asset',
                'account_group' => 'Fixed Asset',
            ],
            [
                'account_code' => '1540',
                'account_name' => 'Computers and Servers',
                'account_type' => 'Asset',
                'account_group' => 'Fixed Asset',
            ],
            [
                'account_code' => '1550',
                'account_name' => 'Plant and Machinery',
                'account_type' => 'Asset',
                'account_group' => 'Fixed Asset',
            ],

            /*
            |--------------------------------------------------------------------------
            | 2000 - LIABILITIES
            |--------------------------------------------------------------------------
            */
            [
                'account_code' => '2000',
                'account_name' => 'Liabilities',
                'account_type' => 'Liability',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '2100',
                'account_name' => 'Current Liabilities',
                'account_type' => 'Liability',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '2110',
                'account_name' => 'Accounts Payable',
                'account_type' => 'Liability',
                'account_group' => 'Current Liability',
            ],
            [
                'account_code' => '2120',
                'account_name' => 'Trade Creditors',
                'account_type' => 'Liability',
                'account_group' => 'Current Liability',
            ],
            [
                'account_code' => '2200',
                'account_name' => 'Tax Liabilities',
                'account_type' => 'Liability',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '2210',
                'account_name' => 'PAYE Payable',
                'account_type' => 'Liability',
                'account_group' => 'Current Liability',
            ],
            [
                'account_code' => '2220',
                'account_name' => 'SSNIT Payable',
                'account_type' => 'Liability',
                'account_group' => 'Current Liability',
            ],
            [
                'account_code' => '2230',
                'account_name' => 'VAT Payable',
                'account_type' => 'Liability',
                'account_group' => 'Current Liability',
            ],
            [
                'account_code' => '2240',
                'account_name' => 'Withholding Tax Payable',
                'account_type' => 'Liability',
                'account_group' => 'Current Liability',
            ],
            [
                'account_code' => '2250',
                'account_name' => 'Corporate Tax Payable',
                'account_type' => 'Liability',
                'account_group' => 'Current Liability',
            ],
            [
                'account_code' => '2300',
                'account_name' => 'Accrued Expenses',
                'account_type' => 'Liability',
                'account_group' => 'Current Liability',
            ],
            [
                'account_code' => '2400',
                'account_name' => 'Loans Payable',
                'account_type' => 'Liability',
                'account_group' => 'Long Term Liability',
            ],

            /*
            |--------------------------------------------------------------------------
            | 3000 - EQUITY
            |--------------------------------------------------------------------------
            */
            [
                'account_code' => '3000',
                'account_name' => 'Equity',
                'account_type' => 'Equity',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '3100',
                'account_name' => 'Owner Capital',
                'account_type' => 'Equity',
                'account_group' => 'Capital',
            ],
            [
                'account_code' => '3200',
                'account_name' => 'Retained Earnings',
                'account_type' => 'Equity',
                'account_group' => 'Capital',
            ],
            [
                'account_code' => '3300',
                'account_name' => 'Director Drawings',
                'account_type' => 'Equity',
                'account_group' => 'Drawings',
            ],

            /*
            |--------------------------------------------------------------------------
            | 4000 - REVENUE
            |--------------------------------------------------------------------------
            */
            [
                'account_code' => '4000',
                'account_name' => 'Revenue',
                'account_type' => 'Revenue',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '4100',
                'account_name' => 'Electrical Contract Revenue',
                'account_type' => 'Revenue',
                'account_group' => 'Operating Revenue',
            ],
            [
                'account_code' => '4200',
                'account_name' => 'ICT Services Revenue',
                'account_type' => 'Revenue',
                'account_group' => 'Operating Revenue',
            ],
            [
                'account_code' => '4300',
                'account_name' => 'Consultancy Revenue',
                'account_type' => 'Revenue',
                'account_group' => 'Operating Revenue',
            ],
            [
                'account_code' => '4400',
                'account_name' => 'Maintenance Contract Revenue',
                'account_type' => 'Revenue',
                'account_group' => 'Operating Revenue',
            ],
            [
                'account_code' => '4500',
                'account_name' => 'Solar Installation Revenue',
                'account_type' => 'Revenue',
                'account_group' => 'Operating Revenue',
            ],
            [
                'account_code' => '4600',
                'account_name' => 'Other Income',
                'account_type' => 'Revenue',
                'account_group' => 'Other Income',
            ],

            /*
            |--------------------------------------------------------------------------
            | 5000 - COST OF SALES / PROJECT COSTS
            |--------------------------------------------------------------------------
            */
            [
                'account_code' => '5000',
                'account_name' => 'Cost of Sales',
                'account_type' => 'Expense',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '5100',
                'account_name' => 'Project Materials',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5110',
                'account_name' => 'Electrical Materials',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5120',
                'account_name' => 'ICT Materials',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5200',
                'account_name' => 'Project Labour',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5210',
                'account_name' => 'Casual Labour',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5220',
                'account_name' => 'Subcontractor Payments',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5300',
                'account_name' => 'Project Transport',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5400',
                'account_name' => 'Project Accommodation',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5500',
                'account_name' => 'Project Feeding',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5600',
                'account_name' => 'Equipment Hire',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],
            [
                'account_code' => '5700',
                'account_name' => 'Site Expenses',
                'account_type' => 'Expense',
                'account_group' => 'Cost of Sales',
            ],

            /*
            |--------------------------------------------------------------------------
            | 6000 - OPERATING EXPENSES
            |--------------------------------------------------------------------------
            */
            [
                'account_code' => '6000',
                'account_name' => 'Operating Expenses',
                'account_type' => 'Expense',
                'account_group' => 'Header',
            ],
            [
                'account_code' => '6100',
                'account_name' => 'Salaries and Wages',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6110',
                'account_name' => 'Staff Allowances',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6120',
                'account_name' => 'Staff Welfare',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6200',
                'account_name' => 'Fuel Expense',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6210',
                'account_name' => 'Vehicle Maintenance',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6220',
                'account_name' => 'Vehicle Insurance',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6230',
                'account_name' => 'Vehicle Licensing',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6300',
                'account_name' => 'Office Rent',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6310',
                'account_name' => 'Office Utilities',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6320',
                'account_name' => 'Electricity Bills',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6330',
                'account_name' => 'Water Bills',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6340',
                'account_name' => 'Office Cleaning',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6350',
                'account_name' => 'Office Maintenance',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6360',
                'account_name' => 'Stationery and Printing',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6400',
                'account_name' => 'Internet and Communications',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6410',
                'account_name' => 'Software Licenses',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6420',
                'account_name' => 'Cloud Hosting',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6430',
                'account_name' => 'Domain Registration',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6440',
                'account_name' => 'Cybersecurity Services',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6500',
                'account_name' => 'Training and Development',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6510',
                'account_name' => 'Professional Certifications',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6600',
                'account_name' => 'Legal and Professional Fees',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6610',
                'account_name' => 'Audit Fees',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6620',
                'account_name' => 'Accounting Services',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6630',
                'account_name' => 'Consultancy Fees',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6700',
                'account_name' => 'Marketing and Advertising',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6710',
                'account_name' => 'Business Development',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6720',
                'account_name' => 'Corporate Branding',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6730',
                'account_name' => 'Client Entertainment',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6800',
                'account_name' => 'Bank Charges',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6810',
                'account_name' => 'Interest Expense',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6900',
                'account_name' => 'Miscellaneous Expense',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
            [
                'account_code' => '6910',
                'account_name' => 'Donations and CSR',
                'account_type' => 'Expense',
                'account_group' => 'Operating Expense',
            ],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['account_code' => $account['account_code']],
                [
                    'account_name' => $account['account_name'],
                    'account_type' => $account['account_type'],
                    'account_group' => $account['account_group'],
                    'active' => true,
                ]
            );
        }
    }
}