<?php

namespace App\Livewire\Projects;

use App\Models\Client;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectQuotation;
use App\Models\ProjectQuotationItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProjectQuotationsPage extends Component
{
    public string $search = '';

    public ?int $company_id = null;
    public ?int $client_id = null;
    public ?int $project_id = null;

    public ?int $editingQuotationId = null;
    public bool $isEditing = false;

    public ?string $quotation_date = null;
    public ?string $valid_until = null;

    public ?string $client_name = null;
    public ?string $client_phone = null;
    public ?string $client_email = null;
    public ?string $client_tin = null;
    public ?string $client_address = null;

    public ?string $project_title = null;
    public ?string $scope_of_work = null;

    public float|int|string $subtotal = 0;
    public bool $vat_applicable = false;
    public float|int|string $vat_amount = 0;
    public float|int|string $getfund_amount = 0;
    public float|int|string $nhil_amount = 0;
    public float|int|string $grand_total = 0;

    public string $amount_in_words = '';

    public ?string $terms_and_conditions = null;
    public ?string $notes = null;
    public ?string $prepared_by = null;
    public ?string $approved_by = null;

    public string $status = 'draft';

    public array $items = [];

    public array $statuses = [
        'draft',
        'sent',
        'approved',
        'accepted',
        'rejected',
        'converted',
        'cancelled',
    ];

    public function mount(): void
    {
        $this->quotation_date = now()->toDateString();
        $this->valid_until = now()->addDays(30)->toDateString();

        $this->items = [
            $this->blankItem(),
        ];
    }

    public function blankItem(): array
    {
        return [
            'item_code' => null,
            'description' => '',
            'unit' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'line_total' => 0,
        ];
    }

    public function updatedClientId(): void
    {
        if (! $this->client_id) {
            return;
        }

        $client = Client::find($this->client_id);

        if (! $client) {
            return;
        }

        $this->client_name = $client->name ?? null;
        $this->client_phone = $client->phone ?? null;
        $this->client_email = $client->email ?? null;
        $this->client_tin = $client->tin_number ?? null;
        $this->client_address = $client->address ?? null;
    }

    public function updatedProjectId(): void
    {
        if (! $this->project_id) {
            return;
        }

        $project = Project::find($this->project_id);

        if (! $project) {
            return;
        }

        $this->project_title = $project->project_name ?? null;
        $this->company_id = $project->company_id ?? $this->company_id;
        $this->client_id = $project->client_id ?? $this->client_id;
    }

    public function updatedItems(): void
    {
        $this->calculateTotals();
    }

    public function updatedVatApplicable(): void
    {
        $this->calculateTotals();
    }

    public function addItem(): void
    {
        $this->items[] = $this->blankItem();
        $this->calculateTotals();
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);

        $this->items = array_values($this->items);

        if (count($this->items) === 0) {
            $this->items[] = $this->blankItem();
        }

        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $subtotal = 0;

        foreach ($this->items as $index => $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);

            $lineTotal = round($quantity * $unitPrice, 2);

            $this->items[$index]['line_total'] = $lineTotal;

            $subtotal += $lineTotal;
        }

        $this->subtotal = round($subtotal, 2);

        if ($this->vat_applicable) {
            $this->vat_amount = round($this->subtotal * 0.15, 2);
            $this->getfund_amount = round($this->subtotal * 0.025, 2);
            $this->nhil_amount = round($this->subtotal * 0.025, 2);
        } else {
            $this->vat_amount = 0;
            $this->getfund_amount = 0;
            $this->nhil_amount = 0;
        }

        $this->grand_total = round(
            (float) $this->subtotal +
            (float) $this->vat_amount +
            (float) $this->getfund_amount +
            (float) $this->nhil_amount,
            2
        );

        $this->amount_in_words = $this->amountToWords((float) $this->grand_total);
    }

    public function amountToWords(float $amount): string
    {
        if ($amount <= 0) {
            return '';
        }

        if (! class_exists(\NumberFormatter::class)) {
            return number_format($amount, 2) . ' Ghana Cedis Only';
        }

        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);

        $cedis = floor($amount);
        $pesewas = round(($amount - $cedis) * 100);

        $words = ucfirst($formatter->format($cedis)) . ' Ghana Cedis';

        if ($pesewas > 0) {
            $words .= ' and ' . ucfirst($formatter->format($pesewas)) . ' Pesewas';
        }

        return $words . ' Only';
    }

    public function generateQuotationCode(): string
    {
        $last = ProjectQuotation::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'QUO' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function generateQuotationNumber(): string
    {
        $last = ProjectQuotation::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'QT' . date('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function createNew(): void
    {
        $this->clearForm();

        session()->flash('info', 'New quotation buffer initialized.');
    }

    public function postLedger(): void
    {
        $this->save();
    }

    public function clearBuffer(): void
    {
        $this->clearForm();

        session()->flash('info', 'Quotation buffer cleared successfully.');
    }

    public function sync(): void
    {
        session()->flash('info', 'Quotation ledger synchronized successfully.');
    }

    public function editQuotation(int $quotationId): void
    {
        $quotation = ProjectQuotation::with('items')->findOrFail($quotationId);

        $this->editingQuotationId = $quotation->id;
        $this->isEditing = true;

        $this->company_id = $quotation->company_id;
        $this->client_id = $quotation->client_id;
        $this->project_id = $quotation->project_id;

        $this->quotation_date = $quotation->quotation_date;
        $this->valid_until = $quotation->valid_until;

        $this->client_name = $quotation->client_name;
        $this->client_phone = $quotation->client_phone;
        $this->client_email = $quotation->client_email;
        $this->client_tin = $quotation->client_tin;
        $this->client_address = $quotation->client_address;

        $this->project_title = $quotation->project_title;
        $this->scope_of_work = $quotation->scope_of_work;

        $this->subtotal = $quotation->subtotal;
        $this->vat_applicable = (bool) $quotation->vat_applicable;
        $this->vat_amount = $quotation->vat_amount;
        $this->getfund_amount = $quotation->getfund_amount;
        $this->nhil_amount = $quotation->nhil_amount;
        $this->grand_total = $quotation->grand_total;
        $this->amount_in_words = $quotation->amount_in_words ?? '';

        $this->terms_and_conditions = $quotation->terms_and_conditions;
        $this->notes = $quotation->notes;
        $this->prepared_by = $quotation->prepared_by;
        $this->approved_by = $quotation->approved_by;
        $this->status = $quotation->status;

        $this->items = $quotation->items
            ->map(fn ($item) => [
                'item_code' => $item->item_code,
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'line_total' => $item->line_total,
            ])
            ->toArray();

        if (count($this->items) === 0) {
            $this->items[] = $this->blankItem();
        }

        session()->flash('info', 'Quotation loaded for editing.');
    }

    public function approveQuotation(int $quotationId): void
    {
        ProjectQuotation::findOrFail($quotationId)->update([
            'status' => 'approved',
            'approved_by' => $this->approved_by ?: 'System Approver',
        ]);

        session()->flash('success', 'Quotation approved successfully.');
    }

    public function cancelQuotation(int $quotationId): void
    {
        ProjectQuotation::findOrFail($quotationId)->update([
            'status' => 'cancelled',
        ]);

        session()->flash('info', 'Quotation cancelled successfully.');
    }

    public function clearForm(): void
    {
        $this->reset([
            'company_id',
            'client_id',
            'project_id',
            'editingQuotationId',
            'isEditing',
            'quotation_date',
            'valid_until',
            'client_name',
            'client_phone',
            'client_email',
            'client_tin',
            'client_address',
            'project_title',
            'scope_of_work',
            'subtotal',
            'vat_applicable',
            'vat_amount',
            'getfund_amount',
            'nhil_amount',
            'grand_total',
            'amount_in_words',
            'terms_and_conditions',
            'notes',
            'prepared_by',
            'approved_by',
            'status',
            'items',
        ]);

        $this->quotation_date = now()->toDateString();
        $this->valid_until = now()->addDays(30)->toDateString();
        $this->status = 'draft';
        $this->items = [$this->blankItem()];
    }

    public function save(): void
    {
        $this->calculateTotals();

        $this->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date'],
            'client_name' => ['required', 'string', 'max:255'],
            'project_title' => ['required', 'string', 'max:255'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'grand_total' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $payload = [
            'company_id' => $this->company_id,
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,

            'quotation_date' => $this->quotation_date,
            'valid_until' => $this->valid_until,

            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
            'client_email' => $this->client_email,
            'client_tin' => $this->client_tin,
            'client_address' => $this->client_address,

            'project_title' => $this->project_title,
            'scope_of_work' => $this->scope_of_work,

            'subtotal' => $this->subtotal,
            'vat_applicable' => $this->vat_applicable,
            'vat_amount' => $this->vat_amount,
            'getfund_amount' => $this->getfund_amount,
            'nhil_amount' => $this->nhil_amount,
            'grand_total' => $this->grand_total,
            'amount_in_words' => $this->amount_in_words,

            'terms_and_conditions' => $this->terms_and_conditions,
            'notes' => $this->notes,
            'prepared_by' => $this->prepared_by,
            'approved_by' => $this->approved_by,
            'status' => $this->status,
        ];

        DB::transaction(function () use ($payload) {
            if ($this->isEditing && $this->editingQuotationId) {
                $quotation = ProjectQuotation::findOrFail($this->editingQuotationId);
                $quotation->update($payload);
                $quotation->items()->delete();
            } else {
                $quotation = ProjectQuotation::create(array_merge($payload, [
                    'quotation_code' => $this->generateQuotationCode(),
                    'quotation_number' => $this->generateQuotationNumber(),
                ]));
            }

            foreach ($this->items as $item) {
                $quotation->items()->create([
                    'item_code' => $item['item_code'] ?? null,
                    'description' => $item['description'],
                    'unit' => $item['unit'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'line_total' => $item['line_total'] ?? 0,
                ]);
            }
        });

        $this->clearForm();

        session()->flash('success', $this->isEditing ? 'Quotation updated successfully.' : 'Quotation posted successfully.');
    }

    public function render()
    {
        $companies = Company::where('active', true)->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $projects = Project::orderBy('project_name')->get();

        $quotations = ProjectQuotation::with(['company', 'client', 'project'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('quotation_code', 'like', "%{$this->search}%")
                        ->orWhere('quotation_number', 'like', "%{$this->search}%")
                        ->orWhere('client_name', 'like', "%{$this->search}%")
                        ->orWhere('project_title', 'like', "%{$this->search}%")
                        ->orWhere('status', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.projects.project-quotations-page', compact(
            'companies',
            'clients',
            'projects',
            'quotations'
        ))->layout('layouts.erp');
    }
}