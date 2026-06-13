<?php

namespace Database\Seeders;

use App\Models\IncomeCategory;
use Illuminate\Database\Seeder;

class IncomeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_code' => 'REV001', 'name' => 'Project Revenue'],
            ['category_code' => 'REV002', 'name' => 'Electrical Contract Revenue'],
            ['category_code' => 'REV003', 'name' => 'ICT Services Revenue'],
            ['category_code' => 'REV004', 'name' => 'Consultancy Revenue'],
            ['category_code' => 'REV005', 'name' => 'Maintenance Contract Revenue'],
            ['category_code' => 'REV006', 'name' => 'Solar Installation Revenue'],
            ['category_code' => 'REV007', 'name' => 'Sale of Materials'],
            ['category_code' => 'REV008', 'name' => 'Service Charges'],
            ['category_code' => 'REV009', 'name' => 'Training Income'],
            ['category_code' => 'REV010', 'name' => 'Other Income'],

            ['category_code' => 'CAP001', 'name' => 'Owner Capital Injection'],
            ['category_code' => 'CAP002', 'name' => 'Director Loan Received'],
            ['category_code' => 'CAP003', 'name' => 'Bank Loan Received'],

            ['category_code' => 'AST001', 'name' => 'Asset Disposal Income'],

            ['category_code' => 'MISC001', 'name' => 'Miscellaneous Income'],
        ];

        foreach ($categories as $category) {
            IncomeCategory::updateOrCreate(
                ['category_code' => $category['category_code']],
                [
                    'name' => $category['name'],
                    'active' => true,
                ]
            );
        }
    }
}