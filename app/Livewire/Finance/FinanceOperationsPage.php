<?php

namespace App\Livewire\Finance;

use App\Models\ChartOfAccount;
use App\Models\FinanceDocument;
use App\Models\FinanceParty;
use App\Models\FinanceTransaction;
use App\Models\Material;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinanceOperationsPage extends FinanceBasePage
{
    public string $activeTab = 'home';

    public string $search = '';
    public ?string $date_from = null;
    public ?string $date_to = null;
    public ?string $type_filter = null;
    public ?string $status_filter = null;

    public ?int $editingDocumentId = null;
    public ?int $editingTransactionId = null;

    public bool $showNewPartyForm = false;
    public bool $showNewProjectForm = false;

    public string $new_party_type = 'customer';
    public string $new_party_name = '';
    public string $new_party_phone = '';
    public string $new_party_email = '';
    public string $new_party_tin = '';

    public string $new_project_name = '';
    public string $new_project_location = '';

    public string $document_type = 'quotation';
    public ?string $document_date = null;
    public string $document_no = '';
    public ?int $party_id = null;
    public ?int $project_id = null;
    public string $customer_name = '';
    public string $service_description = '';
    public string $source_quotation_no = '';

    public float|int|string $labour_cost = 0;
    public float|int|string $transport_cost = 0;
    public float|int|string $other_cost = 0;
    public float|int|string $discount_amount = 0;
    public float|int|string $tax_rate = 0;

    public string $document_narration = '';
    public array $documentLines = [];

    public string $transaction_type = 'receipt';
    public string $transaction_category = 'customer_receipt';
    public string $transaction_subtype = 'invoice_payment';

    public ?string $reference_date = null;
    public string $reference_no = '';
    public ?int $finance_document_id = null;
    public ?int $transaction_project_id = null;
    public ?int $budget_id = null;

    public string $party_name = '';
    public string $payment_method = 'cash';
    public ?int $from_account_id = null;
    public ?int $to_account_id = null;
    public ?int $cash_account_id = null;

    public float|int|string $gross_amount = 0;
    public float|int|string $discount_transaction_amount = 0;
    public float|int|string $tax_transaction_amount = 0;
    public float|int|string $wht_amount = 0;
    public float|int|string $net_amount = 0;

    public string $external_reference = '';
    public string $transaction_narration = '';

    public string $lender_name = '';
    public float|int|string $interest_rate = 0;
    public string $interest_period = 'monthly';
    public ?string $loan_start_date = null;
    public ?string $loan_due_date = null;

    public function mount(): void
    {
        $today = now()->toDateString();

        $this->document_date = $today;
        $this->reference_date = $today;
        $this->loan_start_date = $today;
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = $today;

        $this->documentLines = [$this->blankLine()];
    }

    public function go(string $tab): void
    {
        $this->activeTab = $tab;

        if ($tab === 'quotation') {
            $this->document_type = 'quotation';
        }

        if (in_array($tab, ['invoice', 'convert'], true)) {
            $this->document_type = 'invoice';
        }

        if ($tab === 'receipt') {
            $this->setTransactionMode('receipt', 'customer_receipt', 'invoice_payment');
        }

        if ($tab === 'loan') {
            $this->setTransactionMode('loan', 'loan_capital', 'bank_loan');
        }

        if ($tab === 'payment') {
            $this->setTransactionMode('payment', 'disbursement', 'operations');
        }

        if ($tab === 'transfer') {
            $this->setTransactionMode('transfer', 'internal_transfer', 'bank_to_bank');
        }
    }

    public function openWorkflow(string $tab, ?string $category = null, ?string $subtype = null): void
    {
        $this->go($tab);

        if ($category) {
            $this->transaction_category = $category;
        }

        if ($subtype) {
            $this->transaction_subtype = $subtype;
        }
    }

    private function setTransactionMode(string $type, string $category, string $subtype): void
    {
        $this->transaction_type = $type;
        $this->transaction_category = $category;
        $this->transaction_subtype = $subtype;
    }

    public function blankLine(): array
    {
        return [
            'material_id' => null,
            'line_type' => 'material',
            'description' => '',
            'unit' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'installation_cost' => 0,
            'amount' => 0,
        ];
    }

    public function addLine(): void
    {
        $this->documentLines[] = $this->blankLine();
    }

    public function removeLine(int $index): void
    {
        unset($this->documentLines[$index]);
        $this->documentLines = array_values($this->documentLines);

        if (count($this->documentLines) < 1) {
            $this->documentLines[] = $this->blankLine();
        }

        $this->calculateLines();
    }

    public function updatedDocumentLines(): void
    {
        $this->calculateLines();
    }

    public function updatedPartyId(): void
    {
        if (!$this->party_id) {
            return;
        }

        $party = FinanceParty::find($this->party_id);

        if ($party) {
            $this->customer_name = $party->name;
            $this->party_name = $party->name;
        }
    }

    public function updatedSourceQuotationNo(): void
    {
        $this->loadQuotationToInvoice();
    }

    public function updatedFinanceDocumentId(): void
    {
        $this->loadInvoiceToReceipt();
    }

    public function updatedGrossAmount(): void
    {
        $this->calculateTransactionNet();
    }

    public function updatedDiscountTransactionAmount(): void
    {
        $this->calculateTransactionNet();
    }

    public function updatedTaxTransactionAmount(): void
    {
        $this->calculateTransactionNet();
    }

    public function updatedWhtAmount(): void
    {
        $this->calculateTransactionNet();
    }

    public function createPartyFromForm(): void
    {
        $this->validate([
            'new_party_type' => ['required', 'string'],
            'new_party_name' => ['required', 'string', 'max:255'],
        ]);

        $party = FinanceParty::create([
            'party_code' => 'PTY' . now()->format('YmdHis'),
            'party_type' => $this->new_party_type,
            'name' => $this->new_party_name,
            'phone' => $this->new_party_phone,
            'email' => $this->new_party_email,
            'tin' => $this->new_party_tin,
            'active' => true,
            'created_by' => auth()->id(),
        ]);

        $this->party_id = $party->id;
        $this->customer_name = $party->name;
        $this->party_name = $party->name;

        $this->showNewPartyForm = false;
        $this->new_party_type = 'customer';
        $this->new_party_name = '';
        $this->new_party_phone = '';
        $this->new_party_email = '';
        $this->new_party_tin = '';
    }

    public function createProjectFromForm(): void
    {
        $this->validate([
            'new_project_name' => ['required', 'string', 'max:255'],
        ]);

        $project = Project::create([
            'project_name' => $this->new_project_name,
            'location' => $this->new_project_location,
            'status' => 'draft',
        ]);

        $this->project_id = $project->id;
        $this->transaction_project_id = $project->id;

        $this->showNewProjectForm = false;
        $this->new_project_name = '';
        $this->new_project_location = '';
    }

    public function calculateLines(): void
    {
        foreach ($this->documentLines as $index => $line) {
            if (!empty($line['material_id'])) {
                $material = Material::find($line['material_id']);

                if ($material) {
                    if (empty($this->documentLines[$index]['description'])) {
                        $this->documentLines[$index]['description'] = $material->description ?: $material->name;
                    }

                    if (empty($this->documentLines[$index]['unit'])) {
                        $this->documentLines[$index]['unit'] = $material->unit ?? '';
                    }

                    if ((float)($this->documentLines[$index]['unit_price'] ?? 0) <= 0) {
                        $this->documentLines[$index]['unit_price'] =
                            $material->selling_price ?? $material->standard_price ?? 0;
                    }
                }
            }

            $qty = (float)($this->documentLines[$index]['quantity'] ?? 0);
            $price = (float)($this->documentLines[$index]['unit_price'] ?? 0);
            $install = (float)($this->documentLines[$index]['installation_cost'] ?? 0);

            $this->documentLines[$index]['amount'] = round($qty * ($price + $install), 2);
        }
    }

    public function getMaterialsTotalProperty(): float
    {
        $this->calculateLines();

        return (float)collect($this->documentLines)->sum(fn ($line) => (float)($line['amount'] ?? 0));
    }

    public function getOtherTotalProperty(): float
    {
        return (float)$this->labour_cost + (float)$this->transport_cost + (float)$this->other_cost;
    }

    public function getSubTotalProperty(): float
    {
        return $this->materialsTotal + $this->otherTotal;
    }

    public function getTaxAmountProperty(): float
    {
        $taxable = max(0, $this->subTotal - (float)$this->discount_amount);

        return round($taxable * ((float)$this->tax_rate / 100), 2);
    }

    public function getGrandTotalProperty(): float
    {
        return round($this->subTotal - (float)$this->discount_amount + $this->taxAmount, 2);
    }

    public function calculateTransactionNet(): void
    {
        $this->net_amount = max(
            0,
            (float)$this->gross_amount
            - (float)$this->discount_transaction_amount
            + (float)$this->tax_transaction_amount
            - (float)$this->wht_amount
        );
    }

    public function saveDocument(string $status = 'draft'): void
    {
        $this->calculateLines();

        if ($this->editingDocumentId) {
            $existing = FinanceDocument::findOrFail($this->editingDocumentId);

            if (!$this->canModify($existing->status)) {
                session()->flash('success', 'This document is locked and cannot be edited.');
                return;
            }
        }

        $this->validate([
            'document_type' => ['required', 'in:quotation,invoice'],
            'document_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'documentLines' => ['required', 'array', 'min:1'],
        ]);

        DB::transaction(function () use ($status) {
            $prefix = $this->document_type === 'quotation' ? 'QT' : 'INV';

            $document = FinanceDocument::updateOrCreate(
                ['id' => $this->editingDocumentId],
                $this->filterColumns('finance_documents', [
                    'document_type' => $this->document_type,
                    'document_no' => $this->document_no ?: $this->nextReference($prefix),
                    'document_date' => $this->document_date,
                    'project_id' => $this->project_id,
                    'customer_name' => $this->customer_name,
                    'service_description' => $this->service_description,
                    'materials_total' => $this->materialsTotal,
                    'labour_cost' => (float)$this->labour_cost,
                    'transport_cost' => (float)$this->transport_cost,
                    'other_cost' => (float)$this->other_cost,
                    'discount_amount' => (float)$this->discount_amount,
                    'tax_rate' => (float)$this->tax_rate,
                    'tax_amount' => $this->taxAmount,
                    'grand_total' => $this->grandTotal,
                    'source_quotation_no' => $this->source_quotation_no ?: null,
                    'status' => $status,
                    'narration' => $this->document_narration,
                    'created_by' => auth()->id(),
                ])
            );

            $document->lines()->delete();

            foreach ($this->documentLines as $line) {
                $document->lines()->create([
                    'material_id' => $line['material_id'] ?: null,
                    'line_type' => $line['line_type'] ?: 'material',
                    'description' => $line['description'] ?? '',
                    'unit' => $line['unit'] ?? '',
                    'quantity' => (float)($line['quantity'] ?? 0),
                    'unit_price' => (float)($line['unit_price'] ?? 0),
                    'amount' => (float)($line['amount'] ?? 0),
                ]);
            }

            $this->editingDocumentId = $document->id;
            $this->document_no = $document->document_no;
        });

        session()->flash('success', ucfirst($this->document_type) . ' saved successfully.');
    }

    public function submitDocument(): void
    {
        $this->saveDocument('submitted');
    }

    public function loadQuotationToInvoice(): void
    {
        if (!$this->source_quotation_no) {
            return;
        }

        $quotation = FinanceDocument::with('lines')
            ->where('document_type', 'quotation')
            ->where('document_no', $this->source_quotation_no)
            ->first();

        if (!$quotation) {
            return;
        }

        $this->document_type = 'invoice';
        $this->customer_name = $quotation->customer_name ?? '';
        $this->project_id = $quotation->project_id;
        $this->service_description = $quotation->service_description ?? '';
        $this->labour_cost = $quotation->labour_cost;
        $this->transport_cost = $quotation->transport_cost;
        $this->other_cost = $quotation->other_cost;
        $this->discount_amount = $quotation->discount_amount;
        $this->tax_rate = $quotation->tax_rate;
        $this->document_narration = 'Invoice converted from quotation ' . $quotation->document_no;

        $this->documentLines = $quotation->lines->map(fn ($line) => [
            'material_id' => $line->material_id,
            'line_type' => $line->line_type,
            'description' => $line->description,
            'unit' => $line->unit,
            'quantity' => $line->quantity,
            'unit_price' => $line->unit_price,
            'installation_cost' => 0,
            'amount' => $line->amount,
        ])->toArray();

        if (empty($this->documentLines)) {
            $this->documentLines = [$this->blankLine()];
        }
    }

    public function editDocument(int $id): void
    {
        $document = FinanceDocument::with('lines')->findOrFail($id);

        if (!$this->canModify($document->status)) {
            session()->flash('success', 'This document is locked.');
            return;
        }

        $this->editingDocumentId = $document->id;
        $this->activeTab = $document->document_type === 'quotation' ? 'quotation' : 'invoice';
        $this->document_type = $document->document_type;
        $this->document_date = optional($document->document_date)->toDateString();
        $this->document_no = $document->document_no;
        $this->project_id = $document->project_id;
        $this->customer_name = $document->customer_name ?? '';
        $this->service_description = $document->service_description ?? '';
        $this->source_quotation_no = $document->source_quotation_no ?? '';
        $this->labour_cost = $document->labour_cost;
        $this->transport_cost = $document->transport_cost;
        $this->other_cost = $document->other_cost;
        $this->discount_amount = $document->discount_amount;
        $this->tax_rate = $document->tax_rate;
        $this->document_narration = $document->narration ?? '';

        $this->documentLines = $document->lines->map(fn ($line) => [
            'material_id' => $line->material_id,
            'line_type' => $line->line_type,
            'description' => $line->description,
            'unit' => $line->unit,
            'quantity' => $line->quantity,
            'unit_price' => $line->unit_price,
            'installation_cost' => 0,
            'amount' => $line->amount,
        ])->toArray();

        if (empty($this->documentLines)) {
            $this->documentLines = [$this->blankLine()];
        }
    }

    public function duplicateDocument(int $id): void
    {
        if (!$id) {
            return;
        }

        $old = FinanceDocument::with('lines')->findOrFail($id);

        DB::transaction(function () use ($old) {
            $new = $old->replicate();
            $new->document_no = $this->nextReference($old->document_type === 'quotation' ? 'QT' : 'INV');
            $new->status = 'draft';
            $new->created_by = auth()->id();
            $new->approved_by = null;
            $new->approved_at = null;
            $new->save();

            foreach ($old->lines as $line) {
                $new->lines()->create($line->only([
                    'material_id',
                    'line_type',
                    'description',
                    'unit',
                    'quantity',
                    'unit_price',
                    'amount',
                ]));
            }
        });

        session()->flash('success', 'Document duplicated.');
    }

    public function deleteDocument(int $id): void
    {
        $document = FinanceDocument::findOrFail($id);

        if (!$this->canModify($document->status)) {
            session()->flash('success', 'Approved or posted documents cannot be deleted.');
            return;
        }

        $document->delete();

        session()->flash('success', 'Document deleted.');
    }

    public function saveTransaction(string $status = 'draft'): void
    {
        if ($this->editingTransactionId) {
            $existing = FinanceTransaction::findOrFail($this->editingTransactionId);

            if (!$this->canModify($existing->status)) {
                session()->flash('success', 'This transaction is locked.');
                return;
            }
        }

        $this->calculateTransactionNet();

        $this->validate([
            'transaction_type' => ['required', 'in:receipt,payment,transfer,loan,capital,refund,adjustment'],
            'reference_date' => ['required', 'date'],
            'gross_amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($this->transaction_type === 'transfer') {
            $this->validate([
                'from_account_id' => ['required', 'integer', 'different:to_account_id'],
                'to_account_id' => ['required', 'integer'],
            ]);
        } else {
            $this->validate([
                'cash_account_id' => ['required', 'integer'],
            ]);
        }

        if (in_array($this->transaction_type, ['receipt', 'payment'], true)) {
            $this->validate([
                'party_name' => ['required', 'string', 'max:255'],
            ]);
        }

        if ($this->transaction_type === 'loan') {
            $this->validate([
                'lender_name' => ['required', 'string', 'max:255'],
            ]);
        }

        $prefix = match ($this->transaction_type) {
            'receipt' => 'RCPT',
            'transfer' => 'TRF',
            'loan' => 'LN',
            'capital' => 'CAP',
            'refund' => 'REF',
            default => 'PV',
        };

        $transaction = FinanceTransaction::updateOrCreate(
            ['id' => $this->editingTransactionId],
            [
                'transaction_type' => $this->transaction_type,
                'transaction_category' => $this->transaction_category,
                'transaction_subtype' => $this->transaction_subtype,
                'reference_no' => $this->reference_no ?: $this->nextReference($prefix),
                'reference_date' => $this->reference_date,
                'finance_document_id' => $this->finance_document_id,
                'party_id' => $this->party_id,
                'party_name' => match ($this->transaction_type) {
                    'transfer' => 'Internal Transfer',
                    'loan' => $this->lender_name,
                    default => $this->party_name,
                },
                'project_id' => $this->transaction_project_id,
                'budget_id' => $this->budget_id,
                'from_account_id' => $this->transaction_type === 'transfer' ? $this->from_account_id : null,
                'to_account_id' => $this->transaction_type === 'transfer' ? $this->to_account_id : null,
                'cash_account_id' => $this->transaction_type === 'transfer' ? null : $this->cash_account_id,
                'gross_amount' => (float)$this->gross_amount,
                'discount_amount' => (float)$this->discount_transaction_amount,
                'tax_amount' => (float)$this->tax_transaction_amount,
                'wht_amount' => (float)$this->wht_amount,
                'net_amount' => (float)$this->net_amount,
                'currency' => 'GHS',
                'exchange_rate' => 1,
                'payment_method' => $this->payment_method,
                'external_reference' => $this->external_reference,
                'lender_name' => $this->transaction_type === 'loan' ? $this->lender_name : null,
                'interest_rate' => $this->transaction_type === 'loan' ? (float)$this->interest_rate : 0,
                'interest_period' => $this->transaction_type === 'loan' ? $this->interest_period : null,
                'loan_start_date' => $this->transaction_type === 'loan' ? $this->loan_start_date : null,
                'loan_due_date' => $this->transaction_type === 'loan' ? $this->loan_due_date : null,
                'narration' => $this->transaction_narration,
                'status' => $status,
                'prepared_by' => auth()->id(),
                'prepared_at' => now(),
            ]
        );

        $this->editingTransactionId = $transaction->id;
        $this->reference_no = $transaction->reference_no;

        session()->flash('success', ucfirst($this->transaction_type) . ' saved successfully.');
    }

    public function submitTransaction(): void
    {
        $this->saveTransaction('submitted');
    }

    public function loadInvoiceToReceipt(): void
    {
        if (!$this->finance_document_id) {
            return;
        }

        $invoice = FinanceDocument::where('document_type', 'invoice')->find($this->finance_document_id);

        if (!$invoice) {
            return;
        }

        $paid = FinanceTransaction::where('transaction_type', 'receipt')
            ->where('finance_document_id', $invoice->id)
            ->whereNotIn('status', ['reversed', 'cancelled'])
            ->sum('gross_amount');

        $this->party_name = $invoice->customer_name ?? '';
        $this->transaction_project_id = $invoice->project_id;
        $this->gross_amount = max(0, (float)$invoice->grand_total - (float)$paid);
        $this->transaction_category = 'customer_receipt';
        $this->transaction_subtype = 'invoice_payment';
        $this->transaction_narration = 'Receipt against invoice ' . $invoice->document_no;

        $this->calculateTransactionNet();
    }

    public function editTransaction(int $id): void
    {
        $transaction = FinanceTransaction::findOrFail($id);

        if (!$this->canModify($transaction->status)) {
            session()->flash('success', 'This transaction is locked.');
            return;
        }

        $this->editingTransactionId = $transaction->id;
        $this->activeTab = match ($transaction->transaction_type) {
            'receipt' => 'receipt',
            'loan' => 'loan',
            'transfer' => 'transfer',
            default => 'payment',
        };

        $this->transaction_type = $transaction->transaction_type;
        $this->transaction_category = $transaction->transaction_category ?? '';
        $this->transaction_subtype = $transaction->transaction_subtype ?? '';
        $this->reference_no = $transaction->reference_no;
        $this->reference_date = optional($transaction->reference_date)->toDateString();
        $this->finance_document_id = $transaction->finance_document_id;
        $this->party_id = $transaction->party_id;
        $this->party_name = $transaction->party_name ?? '';
        $this->transaction_project_id = $transaction->project_id;
        $this->budget_id = $transaction->budget_id;
        $this->from_account_id = $transaction->from_account_id;
        $this->to_account_id = $transaction->to_account_id;
        $this->cash_account_id = $transaction->cash_account_id;
        $this->gross_amount = $transaction->gross_amount;
        $this->discount_transaction_amount = $transaction->discount_amount;
        $this->tax_transaction_amount = $transaction->tax_amount;
        $this->wht_amount = $transaction->wht_amount;
        $this->net_amount = $transaction->net_amount;
        $this->payment_method = $transaction->payment_method ?? 'cash';
        $this->external_reference = $transaction->external_reference ?? '';
        $this->transaction_narration = $transaction->narration ?? '';
        $this->lender_name = $transaction->lender_name ?? '';
        $this->interest_rate = $transaction->interest_rate ?? 0;
        $this->interest_period = $transaction->interest_period ?? 'monthly';
        $this->loan_start_date = optional($transaction->loan_start_date)->toDateString();
        $this->loan_due_date = optional($transaction->loan_due_date)->toDateString();
    }

    public function duplicateTransaction(int $id): void
    {
        $old = FinanceTransaction::findOrFail($id);

        $new = $old->replicate();
        $new->reference_no = $this->nextReference(match ($old->transaction_type) {
            'receipt' => 'RCPT',
            'loan' => 'LN',
            'transfer' => 'TRF',
            default => 'PV',
        });
        $new->status = 'draft';
        $new->prepared_by = auth()->id();
        $new->prepared_at = now();
        $new->approved_by = null;
        $new->approved_at = null;
        $new->posted_by = null;
        $new->posted_at = null;
        $new->save();

        session()->flash('success', 'Transaction duplicated.');
    }

    public function deleteTransaction(int $id): void
    {
        $transaction = FinanceTransaction::findOrFail($id);

        if (!$this->canModify($transaction->status)) {
            session()->flash('success', 'Approved or posted transactions cannot be deleted.');
            return;
        }

        $transaction->delete();

        session()->flash('success', 'Transaction deleted.');
    }

    public function approveDocument(int $id): void
    {
        $document = FinanceDocument::findOrFail($id);

        if (!in_array($document->status, ['draft', 'submitted'], true)) {
            return;
        }

        $document->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', 'Document approved and removed from queue.');
    }

    public function approveTransaction(int $id): void
    {
        $transaction = FinanceTransaction::findOrFail($id);

        if (!in_array($transaction->status, ['draft', 'submitted'], true)) {
            return;
        }

        $transaction->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', 'Transaction approved and removed from queue.');
    }

    public function cancelDocument(int $id): void
    {
        $document = FinanceDocument::findOrFail($id);

        if ($this->canModify($document->status)) {
            $document->update(['status' => 'cancelled']);
        }
    }

    public function cancelTransaction(int $id): void
    {
        $transaction = FinanceTransaction::findOrFail($id);

        if ($this->canModify($transaction->status)) {
            $transaction->update(['status' => 'cancelled']);
        }
    }

    public function reverseDocument(int $id): void
    {
        FinanceDocument::findOrFail($id)->update(['status' => 'reversed']);
    }

    public function reverseTransaction(int $id): void
    {
        FinanceTransaction::findOrFail($id)->update([
            'status' => 'reversed',
            'reversed_by' => auth()->id(),
            'reversed_at' => now(),
        ]);
    }

    public function clearDocumentForm(): void
    {
        $this->editingDocumentId = null;
        $this->document_no = '';
        $this->document_date = now()->toDateString();
        $this->party_id = null;
        $this->project_id = null;
        $this->customer_name = '';
        $this->service_description = '';
        $this->source_quotation_no = '';
        $this->labour_cost = 0;
        $this->transport_cost = 0;
        $this->other_cost = 0;
        $this->discount_amount = 0;
        $this->tax_rate = 0;
        $this->document_narration = '';
        $this->documentLines = [$this->blankLine()];
    }

    public function clearTransactionForm(): void
    {
        $this->editingTransactionId = null;
        $this->reference_no = '';
        $this->reference_date = now()->toDateString();
        $this->finance_document_id = null;
        $this->party_id = null;
        $this->transaction_project_id = null;
        $this->budget_id = null;
        $this->party_name = '';
        $this->payment_method = 'cash';
        $this->from_account_id = null;
        $this->to_account_id = null;
        $this->cash_account_id = null;
        $this->gross_amount = 0;
        $this->discount_transaction_amount = 0;
        $this->tax_transaction_amount = 0;
        $this->wht_amount = 0;
        $this->net_amount = 0;
        $this->external_reference = '';
        $this->transaction_narration = '';
        $this->lender_name = '';
        $this->interest_rate = 0;
        $this->interest_period = 'monthly';
        $this->loan_start_date = now()->toDateString();
        $this->loan_due_date = null;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
        $this->type_filter = null;
        $this->status_filter = null;
    }

    private function canModify(?string $status): bool
    {
        return in_array($status, ['draft', 'submitted'], true);
    }

    private function filterColumns(string $table, array $payload): array
    {
        return collect($payload)
            ->filter(fn ($value, $column) => Schema::hasColumn($table, $column))
            ->all();
    }

    private function nextReference(string $prefix): string
    {
        return $prefix . now()->format('YmdHis');
    }

    private function accounts()
    {
        return Schema::hasTable('chart_of_accounts')
            ? ChartOfAccount::orderBy('account_code')->get()
            : collect();
    }

    private function cashAccounts()
    {
        return $this->accounts()->filter(function ($account) {
            $name = strtolower((string)$account->account_name);

            return str_contains($name, 'cash')
                || str_contains($name, 'bank')
                || str_contains($name, 'momo')
                || str_contains($name, 'mobile money');
        })->values();
    }

    private function parties()
    {
        return Schema::hasTable('finance_parties')
            ? FinanceParty::where('active', true)->orderBy('name')->get()
            : collect();
    }

    private function projects()
    {
        return Schema::hasTable('projects')
            ? Project::orderBy('project_name')->get()
            : collect();
    }

    private function materials()
    {
        return Schema::hasTable('materials')
            ? Material::orderBy('name')->get()
            : collect();
    }

    private function budgets()
    {
        return Schema::hasTable('finance_budgets')
            ? DB::table('finance_budgets')->orderByDesc('id')->get()
            : collect();
    }

    private function documents()
    {
        if (!Schema::hasTable('finance_documents')) {
            return collect();
        }

        return FinanceDocument::with('project')
            ->when($this->activeTab === 'transactions' && !$this->status_filter, fn ($q) => $q->whereIn('status', ['draft', 'submitted']))
            ->when($this->type_filter && in_array($this->type_filter, ['quotation', 'invoice'], true), fn ($q) => $q->where('document_type', $this->type_filter))
            ->when($this->status_filter, fn ($q) => $q->where('status', $this->status_filter))
            ->when($this->date_from, fn ($q) => $q->whereDate('document_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('document_date', '<=', $this->date_to))
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('document_no', 'ilike', "%{$this->search}%")
                    ->orWhere('customer_name', 'ilike', "%{$this->search}%")
                    ->orWhere('narration', 'ilike', "%{$this->search}%");
            }))
            ->latest()
            ->take(150)
            ->get();
    }

    private function transactions()
    {
        if (!Schema::hasTable('finance_transactions')) {
            return collect();
        }

        return FinanceTransaction::with(['document', 'project', 'party'])
            ->when($this->activeTab === 'transactions' && !$this->status_filter, fn ($q) => $q->whereIn('status', ['draft', 'submitted']))
            ->when($this->type_filter && in_array($this->type_filter, ['receipt', 'payment', 'transfer', 'loan', 'capital', 'refund'], true), fn ($q) => $q->where('transaction_type', $this->type_filter))
            ->when($this->status_filter, fn ($q) => $q->where('status', $this->status_filter))
            ->when($this->date_from, fn ($q) => $q->whereDate('reference_date', '>=', $this->date_from))
            ->when($this->date_to, fn ($q) => $q->whereDate('reference_date', '<=', $this->date_to))
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('reference_no', 'ilike', "%{$this->search}%")
                    ->orWhere('party_name', 'ilike', "%{$this->search}%")
                    ->orWhere('lender_name', 'ilike', "%{$this->search}%")
                    ->orWhere('narration', 'ilike', "%{$this->search}%")
                    ->orWhere('external_reference', 'ilike', "%{$this->search}%");
            }))
            ->latest()
            ->take(150)
            ->get();
    }

    public function render()
    {
        $documents = $this->documents();
        $transactions = $this->transactions();
        $today = now()->toDateString();

        $todayReceipts = Schema::hasTable('finance_transactions')
            ? (float)FinanceTransaction::whereDate('reference_date', $today)
                ->whereIn('transaction_type', ['receipt', 'loan', 'capital'])
                ->whereNotIn('status', ['cancelled', 'reversed'])
                ->sum('gross_amount')
            : 0;

        $todayPayments = Schema::hasTable('finance_transactions')
            ? (float)FinanceTransaction::whereDate('reference_date', $today)
                ->where('transaction_type', 'payment')
                ->whereNotIn('status', ['cancelled', 'reversed'])
                ->sum('gross_amount')
            : 0;

        return view('livewire.finance.finance-operations-page', [
            'financeNavLinks' => $this->financeNavLinks(),
            'accounts' => $this->accounts(),
            'cashAccounts' => $this->cashAccounts(),
            'parties' => $this->parties(),
            'projects' => $this->projects(),
            'materials' => $this->materials(),
            'budgets' => $this->budgets(),

            'documents' => $documents,
            'quotations' => $documents->where('document_type', 'quotation')->values(),
            'invoices' => $documents->where('document_type', 'invoice')->values(),
            'quotationOptions' => Schema::hasTable('finance_documents')
                ? FinanceDocument::where('document_type', 'quotation')->latest()->take(100)->get()
                : collect(),
            'invoiceOptions' => Schema::hasTable('finance_documents')
                ? FinanceDocument::where('document_type', 'invoice')->latest()->take(100)->get()
                : collect(),

            'transactions' => $transactions,
            'receipts' => $transactions->where('transaction_type', 'receipt')->values(),
            'paymentVouchers' => $transactions->where('transaction_type', 'payment')->values(),
            'transfers' => $transactions->where('transaction_type', 'transfer')->values(),
            'loans' => $transactions->where('transaction_type', 'loan')->values(),

            'todayReceipts' => $todayReceipts,
            'todayPayments' => $todayPayments,
            'todayNetMovement' => $todayReceipts - $todayPayments,
            'pendingApprovals' => $documents->where('status', 'submitted')->count() + $transactions->where('status', 'submitted')->count(),
            'draftTransactions' => $documents->where('status', 'draft')->count() + $transactions->where('status', 'draft')->count(),
            'approvedTransactions' => $documents->where('status', 'approved')->count() + $transactions->where('status', 'approved')->count(),
        ])->layout($this->layoutName());
    }
}