<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Material;
use App\Models\MaterialTransaction;
use App\Models\MaterialTransactionLine;
use App\Models\MaterialWaybill;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class InventoryReportPrintController extends Controller
{
    public function show(Request $request)
    {
        $type = $request->get('type', 'stock_summary');

        $projectId = $request->get('project_id');
        $materialId = $request->get('material_id');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        $company = Company::first();
        $reportTitle = $this->reportTitle($type);

        $transactions = $this->filteredTransactions(
            type: $type,
            projectId: $projectId,
            materialId: $materialId,
            status: $status,
            dateFrom: $dateFrom,
            dateTo: $dateTo,
            search: $search
        );

        $waybills = $this->filteredWaybills(
            projectId: $projectId,
            materialId: $materialId,
            dateFrom: $dateFrom,
            dateTo: $dateTo,
            search: $search
        );

        $materials = $this->filteredMaterials(
            type: $type,
            projectId: $projectId,
            materialId: $materialId,
            status: $status,
            dateFrom: $dateFrom,
            dateTo: $dateTo,
            search: $search
        );

        $filterSummary = [];

        if ($type === 'project_consumption' && $projectId) {

            $project = \App\Models\Project::find($projectId);

            $filterSummary[] =
                'Project Consumption - ' .
                ($project?->project_name ?? 'Unknown Project');
        }

        if ($materialId) {

            $material = Material::find($materialId);

            $filterSummary[] =
                'Material: ' .
                ($material?->name ?? 'Unknown Material');
        }

        if ($status) {
            $filterSummary[] =
                'Status: ' . strtoupper($status);
        }

        if ($dateFrom) {
            $filterSummary[] =
                'From: ' . \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        }

        if ($dateTo) {
            $filterSummary[] =
                'To: ' . \Carbon\Carbon::parse($dateTo)->format('d M Y');
        }

        if ($search) {
            $filterSummary[] =
                'Search: ' . $search;
        }

        $filterDescription = implode(' | ', $filterSummary);
        return view(
            'projects.prints.inventory-report',
            compact(
                'type',
                'reportTitle',
                'filterDescription',
                'projectId',
                'materialId',
                'status',
                'dateFrom',
                'dateTo',
                'search',
                'company',
                'materials',
                'transactions',
                'waybills'
            )
        );
    }

    private function filteredMaterials(
        string $type,
        mixed $projectId,
        mixed $materialId,
        mixed $status,
        mixed $dateFrom,
        mixed $dateTo,
        mixed $search
    ): Collection {
        if (in_array($type, [
            'project_consumption',
            'material_movement',
            'material_ledger',
            'goods_receipt_register',
            'material_issue_register',
        ], true)) {
            $materialIds = MaterialTransactionLine::query()
                ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
                ->when($type === 'project_consumption', function ($query) {
                    $query->where('material_transactions.transaction_type', 'issue_project');
                })
                ->when($type === 'goods_receipt_register', function ($query) {
                    $query->where('material_transactions.transaction_type', 'receive');
                })
                ->when($type === 'material_issue_register', function ($query) {
                    $query->whereIn('material_transactions.transaction_type', ['issue_project', 'issue_sale']);
                })
                ->when($projectId, function ($query) use ($projectId) {
                    $query->where('material_transactions.project_id', $projectId);
                })
                ->when($materialId, function ($query) use ($materialId) {
                    $query->where('material_transaction_lines.material_id', $materialId);
                })
                ->when($status, function ($query) use ($status) {
                    $query->where('material_transactions.status', $status);
                })
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    $query->whereDate('material_transactions.transaction_date', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query) use ($dateTo) {
                    $query->whereDate('material_transactions.transaction_date', '<=', $dateTo);
                })
                ->pluck('material_transaction_lines.material_id')
                ->unique()
                ->values();

            return Material::with('category')
                ->whereIn('id', $materialIds)
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('material_code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->get();
        }

        return Material::with('category')
            ->when($materialId, function ($query) use ($materialId) {
                $query->where('id', $materialId);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('material_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();
    }

    private function filteredTransactions(
        string $type,
        mixed $projectId,
        mixed $materialId,
        mixed $status,
        mixed $dateFrom,
        mixed $dateTo,
        mixed $search
    ): Collection {
        return MaterialTransaction::with([
            'project',
            'lines.material.category',
            'waybill',
            'approvedBy',
        ])
            ->when($type === 'project_consumption', function ($query) {
                $query->where('transaction_type', 'issue_project');
            })
            ->when($type === 'goods_receipt_register', function ($query) {
                $query->where('transaction_type', 'receive');
            })
            ->when($type === 'material_issue_register', function ($query) {
                $query->whereIn('transaction_type', ['issue_project', 'issue_sale']);
            })
            ->when($type === 'waybill_register', function ($query) {
                $query->where('transaction_type', 'issue_project');
            })
            ->when($projectId, function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })
            ->when($materialId, function ($query) use ($materialId) {
                $query->whereHas('lines', function ($lineQuery) use ($materialId) {
                    $lineQuery->where('material_id', $materialId);
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('transaction_date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('transaction_date', '<=', $dateTo);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('transaction_no', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('remarks', 'like', "%{$search}%")
                        ->orWhere('transaction_type', 'like', "%{$search}%")
                        ->orWhereHas('project', function ($projectQuery) use ($search) {
                            $projectQuery->where('project_name', 'like', "%{$search}%")
                                ->orWhere('project_code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('lines.material', function ($materialQuery) use ($search) {
                            $materialQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('material_code', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->get();
    }

    private function filteredWaybills(
        mixed $projectId,
        mixed $materialId,
        mixed $dateFrom,
        mixed $dateTo,
        mixed $search
    ): Collection {
        return MaterialWaybill::with([
            'transaction.project',
            'transaction.lines.material.category',
        ])
            ->when($projectId, function ($query) use ($projectId) {
                $query->whereHas('transaction', function ($transactionQuery) use ($projectId) {
                    $transactionQuery->where('project_id', $projectId);
                });
            })
            ->when($materialId, function ($query) use ($materialId) {
                $query->whereHas('transaction.lines', function ($lineQuery) use ($materialId) {
                    $lineQuery->where('material_id', $materialId);
                });
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereHas('transaction', function ($transactionQuery) use ($dateFrom) {
                    $transactionQuery->whereDate('transaction_date', '>=', $dateFrom);
                });
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereHas('transaction', function ($transactionQuery) use ($dateTo) {
                    $transactionQuery->whereDate('transaction_date', '<=', $dateTo);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('waybill_no', 'like', "%{$search}%")
                        ->orWhere('transporter_name', 'like', "%{$search}%")
                        ->orWhere('driver_name', 'like', "%{$search}%")
                        ->orWhere('driver_phone', 'like', "%{$search}%")
                        ->orWhere('vehicle_number', 'like', "%{$search}%")
                        ->orWhere('delivery_location', 'like', "%{$search}%")
                        ->orWhereHas('transaction.project', function ($projectQuery) use ($search) {
                            $projectQuery->where('project_name', 'like', "%{$search}%")
                                ->orWhere('project_code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('transaction.lines.material', function ($materialQuery) use ($search) {
                            $materialQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('material_code', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->get();
    }

    public static function stockQuantity(int $materialId): float
    {
        $received = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->whereIn('material_transactions.transaction_type', [
                'receive',
                'return',
                'adjustment',
            ])
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.quantity');

        $issued = MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.status', 'approved')
            ->whereIn('material_transactions.transaction_type', [
                'issue_project',
                'issue_sale',
            ])
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.quantity');

        return (float) $received - (float) $issued;
    }

    public static function projectConsumptionQuantity(int $projectId, int $materialId): float
    {
        return (float) MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.project_id', $projectId)
            ->where('material_transactions.status', 'approved')
            ->where('material_transactions.transaction_type', 'issue_project')
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.quantity');
    }

    public static function projectConsumptionValue(int $projectId, int $materialId): float
    {
        return (float) MaterialTransactionLine::query()
            ->join('material_transactions', 'material_transactions.id', '=', 'material_transaction_lines.transaction_id')
            ->where('material_transactions.project_id', $projectId)
            ->where('material_transactions.status', 'approved')
            ->where('material_transactions.transaction_type', 'issue_project')
            ->where('material_transaction_lines.material_id', $materialId)
            ->sum('material_transaction_lines.line_total');
    }

    private function reportTitle(string $type): string
    {
        return match ($type) {
            'stock_summary' => 'Stock Summary Report',
            'stock_valuation' => 'Stock Valuation Report',
            'low_stock' => 'Low Stock Report',
            'material_master' => 'Material Master List',
            'material_movement' => 'Material Movement Report',
            'material_ledger' => 'Material Ledger Report',
            'project_consumption' => 'Project Material Consumption Report',
            'goods_receipt_register' => 'Goods Receipt Register',
            'material_issue_register' => 'Material Issue Register',
            'waybill_register' => 'Waybill Register',
            default => 'Inventory Report',
        };
    }
}