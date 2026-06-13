<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\ProjectReceipt;
use Livewire\Component;

class ProjectReceiptsPage extends Component
{
    public string $search = '';

    public ?int $project_id = null;

    public $contract_value = 0;
    public $total_received_before = 0;
    public $amount_received = 0;
    public $outstanding_balance = 0;

    public ?string $date_received = null;
    public ?string $receipt_method = null;
    public ?string $bank_name = null;
    public ?string $bank_account = null;
    public ?string $cheque_number = null;
    public ?string $momo_number = null;
    public ?string $transaction_reference = null;

    public ?string $received_from = null;
    public ?string $payer_phone = null;
    public ?string $payer_tin = null;

    public ?string $receipt_narration = null;
    public ?string $remarks = null;
    public ?string $prepared_by = null;
    public ?string $approved_by = null;
    public ?int $editingReceiptId = null;
    public bool $isEditing = false;
    public string $amount_in_words = '';


    public string $status = 'posted';

    public array $receiptMethods = [
        'Cash',
        'Bank Transfer',
        'Cheque',
        'Mobile Money',
        'Card Payment',
        'Other',
    ];

    public array $statuses = [
        'draft',
        'posted',
        'approved',
        'reversed',
    ];

    public function mount(): void
    {
        $this->date_received = now()->toDateString();
    }

    public function updatedProjectId(): void
    {
        $this->loadProjectReceipts();
    }
    public function amountToWords($number): string
        {
            $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);

            $cedis = floor($number);
            $pesewas = round(($number - $cedis) * 100);

            $words = ucfirst($formatter->format($cedis)) . ' Ghana Cedis';

            if ($pesewas > 0) {
                $words .= ' and ' . ucfirst($formatter->format($pesewas)) . ' Pesewas';
            }

            return $words . ' Only';
        }

    
    public function updatedAmountReceived(): void
    {
        $this->calculateOutstanding();
        $this->amount_in_words =
        $this->amountToWords((float)$this->amount_received);

    }

    public function createNew(): void
    {
        $this->clearForm();
        session()->flash('info', 'New receipt buffer initialized.');
    }

    public function postLedger(): void
    {
        $this->save();
    }

    public function clearBuffer(): void
    {
        $this->clearForm();
        session()->flash('info', 'Receipt buffer cleared successfully.');
    }

    public function sync(): void
    {
        $this->loadProjectReceipts();
        session()->flash('info', 'Receipt ledger synchronized successfully.');
    }

    public function generateReceiptCode(): string
    {
        $last = ProjectReceipt::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'RCPT' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function generateReceiptNumber(): string
    {
        $last = ProjectReceipt::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'RV' . date('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function loadProjectReceipts(): void
    {
        if (! $this->project_id) {
            $this->contract_value = 0;
            $this->total_received_before = 0;
            $this->outstanding_balance = 0;
            return;
        }

        $project = Project::find($this->project_id);

        if (! $project) {
            $this->contract_value = 0;
            $this->total_received_before = 0;
            $this->outstanding_balance = 0;
            return;
        }

        $this->contract_value = (float) ($project->contract_amount ?? 0);

        $this->total_received_before = (float) ProjectReceipt::where('project_id', $project->id)
            ->sum('amount_received');

        $this->calculateOutstanding();
    }

    public function calculateOutstanding(): void
    {
        $this->outstanding_balance =
            (float) $this->contract_value
            - (float) $this->total_received_before
            - (float) $this->amount_received;
    }

    public function clearForm(): void
    {
        $this->reset([
            'project_id',
            'contract_value',
            'total_received_before',
            'amount_received',
            'outstanding_balance',
            'receipt_method',
            'bank_name',
            'bank_account',
            'cheque_number',
            'momo_number',
            'transaction_reference',
            'received_from',
            'payer_phone',
            'payer_tin',
            'receipt_narration',
            'remarks',
            'prepared_by',
            'approved_by',
            'status',
        ]);

        $this->contract_value = 0;
        $this->total_received_before = 0;
        $this->amount_received = 0;
        $this->outstanding_balance = 0;
        $this->status = 'posted';
        $this->date_received = now()->toDateString();
    }

   public function save(): void
        {
            $this->loadProjectReceipts();

            $this->validate([
                'project_id' => ['required', 'exists:projects,id'],
                'amount_received' => ['required', 'numeric', 'min:0.01'],
                'date_received' => ['required', 'date'],
                'receipt_method' => ['nullable', 'string', 'max:255'],
                'received_from' => ['nullable', 'string', 'max:255'],
                'status' => ['required', 'string'],
                            ]);

            $payload = [
                'project_id' => $this->project_id,
                'contract_value' => $this->contract_value,
                'total_received_before' => $this->total_received_before,
                'amount_received' => $this->amount_received,
                'outstanding_balance' => $this->outstanding_balance,
                'date_received' => $this->date_received,
                'receipt_method' => $this->receipt_method,
                'bank_name' => $this->bank_name,
                'bank_account' => $this->bank_account,
                'cheque_number' => $this->cheque_number,
                'momo_number' => $this->momo_number,
                'transaction_reference' => $this->transaction_reference,
                'received_from' => $this->received_from,
                'payer_phone' => $this->payer_phone,
                'payer_tin' => $this->payer_tin,
                'receipt_narration' => $this->receipt_narration,
                'remarks' => $this->remarks,
                'prepared_by' => $this->prepared_by,
                'approved_by' => $this->approved_by,
                'status' => $this->status,
                'amount_in_words' => $this->amount_in_words,
            ];

            if ($this->isEditing && $this->editingReceiptId) {
                ProjectReceipt::findOrFail($this->editingReceiptId)->update($payload);

                session()->flash('success', 'Receipt updated successfully.');
            } else {
                ProjectReceipt::create(array_merge($payload, [
                    'receipt_code' => $this->generateReceiptCode(),
                    'receipt_number' => $this->generateReceiptNumber(),
                ]));

                session()->flash('success', 'Receipt voucher posted successfully.');
            }

            $this->clearForm();
        }

    public function editReceipt(int $receiptId): void
        {
            $receipt = ProjectReceipt::findOrFail($receiptId);

            $this->editingReceiptId = $receipt->id;
            $this->isEditing = true;

            $this->project_id = $receipt->project_id;
            $this->contract_value = $receipt->contract_value;
            $this->total_received_before = $receipt->total_received_before;
            $this->amount_received = $receipt->amount_received;
            $this->outstanding_balance = $receipt->outstanding_balance;
            $this->date_received = $receipt->date_received;
            $this->receipt_method = $receipt->receipt_method;
            $this->bank_name = $receipt->bank_name;
            $this->bank_account = $receipt->bank_account;
            $this->cheque_number = $receipt->cheque_number;
            $this->momo_number = $receipt->momo_number;
            $this->transaction_reference = $receipt->transaction_reference;
            $this->received_from = $receipt->received_from;
            $this->payer_phone = $receipt->payer_phone;
            $this->payer_tin = $receipt->payer_tin;
            $this->receipt_narration = $receipt->receipt_narration;
            $this->remarks = $receipt->remarks;
            $this->prepared_by = $receipt->prepared_by;
            $this->approved_by = $receipt->approved_by;
            $this->status = $receipt->status;

            session()->flash('info', 'Receipt loaded for editing.');
        }

        public function approveReceipt(int $receiptId): void
        {
            $receipt = ProjectReceipt::findOrFail($receiptId);

            $receipt->update([
                'status' => 'approved',
                'approved_by' => $this->approved_by ?: 'System Approver',
            ]);

            session()->flash('success', 'Receipt approved successfully.');
        }

        public function reverseReceipt(int $receiptId): void
        {
            $receipt = ProjectReceipt::findOrFail($receiptId);

            $receipt->update([
                'status' => 'reversed',
            ]);

            session()->flash('info', 'Receipt reversed successfully.');
        }

    public function render()
    {
        $projects = Project::orderBy('project_name')->get();

        $receipts = ProjectReceipt::with('project')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('receipt_code', 'like', "%{$this->search}%")
                        ->orWhere('receipt_number', 'like', "%{$this->search}%")
                        ->orWhere('received_from', 'like', "%{$this->search}%")
                        ->orWhere('receipt_method', 'like', "%{$this->search}%")
                        ->orWhere('transaction_reference', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.projects.project-receipts-page', compact(
            'projects',
            'receipts'
        ))->layout('layouts.erp');
    }
}