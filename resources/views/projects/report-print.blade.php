<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Report - {{ $project->project_code }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #0f172a;
            font-size: 12px;
            margin: 30px;
        }

        .no-print {
            margin-bottom: 20px;
        }

        .print-panel {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 12px;
        }

        .print-panel h3 {
            margin-bottom: 10px;
            font-size: 13px;
        }

        .print-panel label {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: bold;
        }

        button {
            padding: 8px 14px;
            border: 1px solid #0f172a;
            background: #0f172a;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-right: 5px;
        }

        h1, h2, h3 {
            margin: 0;
        }

        .header {
            border-bottom: 3px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .company {
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .title {
            font-size: 14px;
            font-weight: 700;
            margin-top: 4px;
        }

        .meta {
            margin-top: 8px;
            color: #475569;
            font-size: 11px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #cbd5e1;
            padding: 10px;
            background: #f8fafc;
        }

        .card-label {
            font-size: 10px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }

        .card-value {
            margin-top: 5px;
            font-size: 16px;
            font-weight: 900;
        }

        .section {
            margin-top: 22px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #0f172a;
            color: white;
            padding: 7px 10px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .box {
            border: 1px solid #cbd5e1;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        th {
            background: #e2e8f0;
            color: #0f172a;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            padding: 7px;
            border: 1px solid #cbd5e1;
        }

        td {
            padding: 7px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }

        .money {
            text-align: right;
            font-family: monospace;
            white-space: nowrap;
        }

        .muted {
            color: #64748b;
        }

        .footer {
            margin-top: 40px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .signature {
            border-top: 1px solid #0f172a;
            padding-top: 8px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
        }

        .print-hidden {
            display: none !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 18px;
            }

            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="no-print print-panel">
        <h3>Select Report Sections to Print</h3>

        <label>
            <input type="checkbox" class="section-toggle" data-section="summary" checked>
            Project Summary
        </label>

        <label>
            <input type="checkbox" class="section-toggle" data-section="phases" checked>
            Project Phases
        </label>

        <label>
            <input type="checkbox" class="section-toggle" data-section="wbs" checked>
            WBS
        </label>

        <label>
            <input type="checkbox" class="section-toggle" data-section="deliverables" checked>
            Deliverables
        </label>

        <label>
            <input type="checkbox" class="section-toggle" data-section="materials" checked>
            Materials
        </label>

        <label>
            <input type="checkbox" class="section-toggle" data-section="budget" checked>
            Budget / Other Costs
        </label>

        <label>
            <input type="checkbox" class="section-toggle" data-section="costentries" checked>
            Cost Entries
        </label>

        <label>
            <input type="checkbox" class="section-toggle" data-section="finance" checked>
            Invoices / Receipts / Payments
        </label>

        <div style="margin-top: 12px;">
            <button type="button" onclick="selectAllSections()">Select All</button>
            <button type="button" onclick="clearAllSections()">Clear All</button>
            <button type="button" onclick="printSelectedSections()">Print Selected</button>
        </div>
    </div>

    <div class="header">
        <div class="company">
            {{ $project->company?->name ?? 'Company Name' }}
        </div>

        <div class="title">
            Comprehensive Project Report
        </div>

        <div class="meta">
            Project:
            <strong>{{ $project->project_code }} — {{ $project->project_name }}</strong><br>

            Client:
            <strong>{{ $project->client?->name ?? '-' }}</strong><br>

            Date Printed:
            {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="card-label">Contract Value</div>
            <div class="card-value">{{ number_format($contractValue, 2) }}</div>
        </div>

        <div class="card">
            <div class="card-label">Estimated Cost</div>
            <div class="card-value">{{ number_format($estimatedCost, 2) }}</div>
        </div>

        <div class="card">
            <div class="card-label">Actual Cost Entries</div>
            <div class="card-value">{{ number_format($costEntryTotal, 2) }}</div>
        </div>

        <div class="card">
            <div class="card-label">Projected Profit</div>
            <div class="card-value">{{ number_format($profit, 2) }}</div>
        </div>

        <div class="card">
            <div class="card-label">Invoices Raised</div>
            <div class="card-value">{{ number_format($totalInvoices, 2) }}</div>
        </div>

        <div class="card">
            <div class="card-label">Receipts Received</div>
            <div class="card-value">{{ number_format($totalReceipts, 2) }}</div>
        </div>

        <div class="card">
            <div class="card-label">Payments Made</div>
            <div class="card-value">{{ number_format($totalPayments, 2) }}</div>
        </div>

        <div class="card">
            <div class="card-label">Cash Balance</div>
            <div class="card-value">{{ number_format($cashBalance, 2) }}</div>
        </div>
    </div>

    <div class="section report-section" data-section="summary">
        <div class="section-title">1. Project Summary</div>

        <div class="box">
            <table>
                <tr>
                    <th>Project Type</th>
                    <td>{{ strtoupper(str_replace('_', ' ', $project->project_type ?? '-')) }}</td>
                    <th>Status</th>
                    <td>{{ strtoupper(str_replace('_', ' ', $project->status ?? '-')) }}</td>
                </tr>

                <tr>
                    <th>Start Date</th>
                    <td>{{ $project->start_date ?? '-' }}</td>
                    <th>End Date</th>
                    <td>{{ $project->end_date ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Project Manager</th>
                    <td>{{ $project->project_manager ?? '-' }}</td>
                    <th>Site Engineer</th>
                    <td>{{ $project->site_engineer ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Client Representative</th>
                    <td>{{ $project->client_representative ?? '-' }}</td>
                    <th>Location</th>
                    <td>{{ $project->location ?? '-' }}</td>
                </tr>
            </table>

            <p>
                <strong>Scope:</strong><br>
                {{ $project->scope_summary ?? '-' }}
            </p>

            <p>
                <strong>Objectives:</strong><br>
                {{ $project->objectives ?? '-' }}
            </p>

            <p>
                <strong>Notes / Risks / Assumptions:</strong><br>
                {{ $project->notes ?? '-' }}
            </p>
        </div>
    </div>

    <div class="section report-section" data-section="phases">
        <div class="section-title">2. Project Phases</div>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Phase</th>
                    <th>Responsible</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Progress</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($project->phases as $phase)
                    <tr>
                        <td>{{ $phase->phase_code }}</td>
                        <td>{{ $phase->phase_name }}</td>
                        <td>{{ $phase->responsible_person }}</td>
                        <td>{{ $phase->start_date }}</td>
                        <td>{{ $phase->end_date }}</td>
                        <td>{{ number_format((float) $phase->progress_percent, 2) }}%</td>
                        <td>{{ strtoupper($phase->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted">No phases recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section report-section" data-section="wbs">
        <div class="section-title">3. Work Breakdown Structure</div>

        <table>
            <thead>
                <tr>
                    <th>WBS Code</th>
                    <th>Title</th>
                    <th>Responsible</th>
                    <th>Budget</th>
                    <th>Progress</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($project->wbsItems as $wbs)
                    <tr>
                        <td>{{ $wbs->wbs_code }}</td>
                        <td>{{ $wbs->title }}</td>
                        <td>{{ $wbs->responsible_person }}</td>
                        <td class="money">{{ number_format((float) $wbs->budget_amount, 2) }}</td>
                        <td>{{ number_format((float) $wbs->progress_percent, 2) }}%</td>
                        <td>{{ strtoupper($wbs->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No WBS items recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section report-section" data-section="deliverables">
        <div class="section-title">4. Deliverables</div>

        <table>
            <thead>
                <tr>
                    <th>Deliverable</th>
                    <th>Owner</th>
                    <th>Due Date</th>
                    <th>Acceptance Criteria</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($project->deliverables as $item)
                    <tr>
                        <td>{{ $item->deliverable_name }}</td>
                        <td>{{ $item->owner }}</td>
                        <td>{{ $item->due_date }}</td>
                        <td>{{ $item->acceptance_criteria }}</td>
                        <td>{{ strtoupper($item->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No deliverables recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section report-section" data-section="materials">
        <div class="section-title">5. Project Materials</div>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Qty</th>
                    <th>Unit Cost</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @forelse($project->projectMaterials as $item)
                    <tr>
                        <td>{{ $item->item_code }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="money">{{ number_format((float) $item->quantity, 2) }}</td>
                        <td class="money">{{ number_format((float) $item->unit_cost, 2) }}</td>
                        <td class="money">{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No materials recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section report-section" data-section="budget">
        <div class="section-title">6. Budget / Other Cost Items</div>

        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Estimated</th>
                    <th>Actual</th>
                    <th>Variance</th>
                </tr>
            </thead>

            <tbody>
                @forelse($project->budgetLines as $line)
                    <tr>
                        <td>{{ $line->budget_category }}</td>
                        <td>{{ $line->description }}</td>
                        <td class="money">{{ number_format((float) $line->estimated_amount, 2) }}</td>
                        <td class="money">{{ number_format((float) $line->actual_amount, 2) }}</td>
                        <td class="money">{{ number_format((float) $line->variance_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No budget lines recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section report-section" data-section="costentries">
        <div class="section-title">7. Project Cost Entries</div>

        <table>
            <thead>
                <tr>
                    <th>Cost Code</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
                @forelse($costEntries as $entry)
                    <tr>
                        <td>{{ $entry->cost_code }}</td>
                        <td>{{ strtoupper($entry->cost_type) }}</td>
                        <td>{{ $entry->description }}</td>
                        <td>{{ $entry->cost_date }}</td>
                        <td>{{ strtoupper($entry->status) }}</td>
                        <td class="money">{{ number_format((float) $entry->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No cost entries recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section report-section" data-section="finance">
        <div class="section-title">8. Invoices, Receipts and Payments</div>

        <h3>Invoices</h3>

        <table>
            <thead>
                <tr>
                    <th>Invoice No.</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_no ?? $invoice->invoice_number ?? '-' }}</td>
                        <td>{{ $invoice->invoice_date ?? $invoice->created_at?->format('Y-m-d') }}</td>
                        <td>{{ strtoupper($invoice->status ?? '-') }}</td>
                        <td class="money">{{ number_format((float) $invoice->grand_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No invoices recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <br>

        <h3>Receipts</h3>

        <table>
            <thead>
                <tr>
                    <th>Receipt No.</th>
                    <th>Date</th>
                    <th>Received From</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
                @forelse($receipts as $receipt)
                    <tr>
                        <td>{{ $receipt->receipt_no ?? $receipt->receipt_number ?? '-' }}</td>
                        <td>{{ $receipt->date_received ?? $receipt->receipt_date ?? '-' }}</td>
                        <td>{{ $receipt->received_from ?? '-' }}</td>
                        <td class="money">{{ number_format((float) $receipt->amount_received, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No receipts recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <br>

        <h3>Payments</h3>

        <table>
            <thead>
                <tr>
                    <th>Voucher No.</th>
                    <th>Date</th>
                    <th>Payee</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->voucher_no ?? $payment->voucher_number ?? '-' }}</td>
                        <td>{{ $payment->payment_date ?? '-' }}</td>
                        <td>{{ $payment->payee_name ?? '-' }}</td>
                        <td>{{ strtoupper($payment->status ?? '-') }}</td>
                        <td class="money">{{ number_format((float) $payment->gross_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No payments recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="signature">Prepared By</div>
        <div class="signature">Checked By</div>
        <div class="signature">Approved By</div>
    </div>

    <script>
        function applySectionVisibility() {
            document.querySelectorAll('.section-toggle').forEach(function (checkbox) {
                const sectionName = checkbox.dataset.section;
                const section = document.querySelector('.report-section[data-section="' + sectionName + '"]');

                if (!section) {
                    return;
                }

                if (checkbox.checked) {
                    section.classList.remove('print-hidden');
                } else {
                    section.classList.add('print-hidden');
                }
            });
        }

        function selectAllSections() {
            document.querySelectorAll('.section-toggle').forEach(function (checkbox) {
                checkbox.checked = true;
            });

            applySectionVisibility();
        }

        function clearAllSections() {
            document.querySelectorAll('.section-toggle').forEach(function (checkbox) {
                checkbox.checked = false;
            });

            applySectionVisibility();
        }

        function printSelectedSections() {
            applySectionVisibility();
            window.print();
        }

        document.querySelectorAll('.section-toggle').forEach(function (checkbox) {
            checkbox.addEventListener('change', applySectionVisibility);
        });
    </script>

</body>
</html>