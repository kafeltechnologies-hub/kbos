<?php

namespace App\Services\Projects;

use App\Models\Project;

class ProjectFinancialService
{
    public function summary(Project $project): array
    {
        $contractAmount = (float) $project->contract_amount;
        $budgetAmount = (float) $project->budget_amount;

        $totalCost = (float) $project->costs()->sum('amount');

        $grossPayments = (float) $project->payments()->sum('gross_amount');
        $withholdingTax = (float) $project->payments()->sum('withholding_tax');
        $netReceived = (float) $project->payments()->sum('net_amount');

        $outstandingBalance = $contractAmount - $grossPayments;
        $profitOrLoss = $contractAmount - $totalCost;

        $profitMargin = $contractAmount > 0
            ? ($profitOrLoss / $contractAmount) * 100
            : 0;

        $budgetVariance = $budgetAmount - $totalCost;

        $budgetUsage = $budgetAmount > 0
            ? ($totalCost / $budgetAmount) * 100
            : 0;

        return [
            'contract_amount' => round($contractAmount, 2),
            'budget_amount' => round($budgetAmount, 2),
            'total_cost' => round($totalCost, 2),
            'gross_payments' => round($grossPayments, 2),
            'withholding_tax' => round($withholdingTax, 2),
            'net_received' => round($netReceived, 2),
            'outstanding_balance' => round($outstandingBalance, 2),
            'profit_or_loss' => round($profitOrLoss, 2),
            'profit_margin' => round($profitMargin, 2),
            'budget_variance' => round($budgetVariance, 2),
            'budget_usage' => round($budgetUsage, 2),
        ];
    }
}