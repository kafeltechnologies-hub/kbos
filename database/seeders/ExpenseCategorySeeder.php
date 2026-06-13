<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [

            // PROJECTS
            ['category_code' => 'PROJ001', 'name' => 'Project Materials'],
            ['category_code' => 'PROJ002', 'name' => 'Project Labour'],
            ['category_code' => 'PROJ003', 'name' => 'Project Transport'],
            ['category_code' => 'PROJ004', 'name' => 'Project Accommodation'],
            ['category_code' => 'PROJ005', 'name' => 'Project Feeding'],
            ['category_code' => 'PROJ006', 'name' => 'Subcontractor Payments'],
            ['category_code' => 'PROJ007', 'name' => 'Project Equipment Hire'],
            ['category_code' => 'PROJ008', 'name' => 'Project Site Expenses'],

            // PAYROLL
            ['category_code' => 'PAY001', 'name' => 'Staff Salaries'],
            ['category_code' => 'PAY002', 'name' => 'Overtime Payments'],
            ['category_code' => 'PAY003', 'name' => 'Staff Allowances'],
            ['category_code' => 'PAY004', 'name' => 'Casual Labour Payments'],
            ['category_code' => 'PAY005', 'name' => 'Director Remuneration'],
            ['category_code' => 'PAY006', 'name' => 'Staff Welfare'],

            // STATUTORY
            ['category_code' => 'STAT001', 'name' => 'PAYE'],
            ['category_code' => 'STAT002', 'name' => 'SSNIT'],
            ['category_code' => 'STAT003', 'name' => 'Withholding Tax'],
            ['category_code' => 'STAT004', 'name' => 'VAT Payment'],
            ['category_code' => 'STAT005', 'name' => 'Corporate Tax'],
            ['category_code' => 'STAT006', 'name' => 'Business Registration Fees'],

            // TRANSPORT
            ['category_code' => 'TRANS001', 'name' => 'Fuel'],
            ['category_code' => 'TRANS002', 'name' => 'Vehicle Maintenance'],
            ['category_code' => 'TRANS003', 'name' => 'Vehicle Insurance'],
            ['category_code' => 'TRANS004', 'name' => 'Vehicle Licensing'],
            ['category_code' => 'TRANS005', 'name' => 'Driver Expenses'],
            ['category_code' => 'TRANS006', 'name' => 'Transportation Charges'],

            // OFFICE
            ['category_code' => 'OFF001', 'name' => 'Office Rent'],
            ['category_code' => 'OFF002', 'name' => 'Office Utilities'],
            ['category_code' => 'OFF003', 'name' => 'Water Bills'],
            ['category_code' => 'OFF004', 'name' => 'Electricity Bills'],
            ['category_code' => 'OFF005', 'name' => 'Office Cleaning'],
            ['category_code' => 'OFF006', 'name' => 'Office Maintenance'],
            ['category_code' => 'OFF007', 'name' => 'Office Furniture'],
            ['category_code' => 'OFF008', 'name' => 'Stationery & Printing'],

            // ICT
            ['category_code' => 'ICT001', 'name' => 'Internet Services'],
            ['category_code' => 'ICT002', 'name' => 'Software Licenses'],
            ['category_code' => 'ICT003', 'name' => 'Cloud Hosting'],
            ['category_code' => 'ICT004', 'name' => 'Domain Registration'],
            ['category_code' => 'ICT005', 'name' => 'Server Maintenance'],
            ['category_code' => 'ICT006', 'name' => 'Computer Equipment'],
            ['category_code' => 'ICT007', 'name' => 'Network Equipment'],
            ['category_code' => 'ICT008', 'name' => 'Cybersecurity Services'],

            // TOOLS & EQUIPMENT
            ['category_code' => 'EQP001', 'name' => 'Power Tools'],
            ['category_code' => 'EQP002', 'name' => 'Safety Equipment'],
            ['category_code' => 'EQP003', 'name' => 'Testing Instruments'],
            ['category_code' => 'EQP004', 'name' => 'Electrical Equipment'],
            ['category_code' => 'EQP005', 'name' => 'Tool Repairs'],
            ['category_code' => 'EQP006', 'name' => 'Tool Calibration'],

            // PROFESSIONAL
            ['category_code' => 'PROF001', 'name' => 'Consultancy Fees'],
            ['category_code' => 'PROF002', 'name' => 'Legal Fees'],
            ['category_code' => 'PROF003', 'name' => 'Audit Fees'],
            ['category_code' => 'PROF004', 'name' => 'Accounting Services'],
            ['category_code' => 'PROF005', 'name' => 'Engineering Services'],

            // SALES & MARKETING
            ['category_code' => 'MKT001', 'name' => 'Advertising'],
            ['category_code' => 'MKT002', 'name' => 'Marketing Campaigns'],
            ['category_code' => 'MKT003', 'name' => 'Corporate Branding'],
            ['category_code' => 'MKT004', 'name' => 'Business Development'],
            ['category_code' => 'MKT005', 'name' => 'Client Entertainment'],

            // TRAINING
            ['category_code' => 'TRN001', 'name' => 'Staff Training'],
            ['category_code' => 'TRN002', 'name' => 'Professional Certifications'],
            ['category_code' => 'TRN003', 'name' => 'Workshop Attendance'],

            // FINANCE
            ['category_code' => 'FIN001', 'name' => 'Bank Charges'],
            ['category_code' => 'FIN002', 'name' => 'Loan Repayment'],
            ['category_code' => 'FIN003', 'name' => 'Interest Expense'],
            ['category_code' => 'FIN004', 'name' => 'Insurance Premiums'],

            // PROCUREMENT
            ['category_code' => 'PROC001', 'name' => 'Inventory Purchases'],
            ['category_code' => 'PROC002', 'name' => 'Consumables'],
            ['category_code' => 'PROC003', 'name' => 'Spare Parts'],

            // MANAGEMENT
            ['category_code' => 'MGT001', 'name' => 'Board Expenses'],
            ['category_code' => 'MGT002', 'name' => 'Management Meetings'],
            ['category_code' => 'MGT003', 'name' => 'Strategic Planning'],

            // MISC
            ['category_code' => 'MISC001', 'name' => 'Donations'],
            ['category_code' => 'MISC002', 'name' => 'Corporate Social Responsibility'],
            ['category_code' => 'MISC003', 'name' => 'Miscellaneous Expenses'],
        ];

        foreach ($categories as $category) {

            ExpenseCategory::updateOrCreate(
                [
                    'category_code' => $category['category_code']
                ],
                [
                    'name' => $category['name'],
                    'active' => true
                ]
            );
        }
    }
}