<?php

namespace App\Livewire\Finance;

use App\Models\Client;
use App\Models\Company;
use App\Models\InvoiceVoucher;
use App\Models\Material;
use App\Models\Project;
use App\Models\ProjectQuotation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Services\Accounting\AccountingPostingService;

class InvoiceCentrePage extends Component
{
    public string $search = '';

    public ?int $editingInvoiceId = null;
    public bool $isEditing = false;
    
    public ?int $company_id = null;
    public ?int $client_id = null;
    public ?int $project_id = null;
    public ?int $project_quotation_id = null;

    public ?string $invoice_date = null;
    public ?string $due_date = null;

    public ?string $client_name = null;
    public ?string $client_phone = null;
    public ?string $client_email = null;
    public ?string $client_tin = null;
    public ?string $client_address = null;

    public ?string $project_title = null;
    public ?string $scope_of_work = null;

    public string $document_type = 'invoice';

    public array $documentTypes = [
        'quotation' => 'Quotation',
        'invoice' => 'Invoice',
    ];

    public $contract_value = 0;
    public $previous_invoices = 0;
    public $outstanding_before_invoice = 0;

    public $subtotal = 0;
    public $labor_charge = 0;
    public $transport_charge = 0;
    public $other_charges = 0;
    public ?string $other_charges_description = null;

    public bool $vat_applicable = false;
    public $vat_amount = 0;
    public $getfund_amount = 0;
    public $nhil_amount = 0;
    public $grand_total = 0;
    public $balance_after_invoice = 0;

    public string $amount_in_words = '';

    public ?string $terms_and_conditions = null;
    public ?string $notes = null;

    public string $status = 'draft';

    public ?string $prepared_by = null;
    public ?string $checked_by = null;
    public ?string $approved_by = null;

    public array $items = [];

    public array $statuses = [
        'draft',
        'prepared',
        'approved',
        'sent',
        'part_paid',
        'paid',
        'posted',
        'cancelled',
    ];

    public function mount(): void
    {
        $this->invoice_date = now()->toDateString();
        $this->due_date = now()->addDays(30)->toDateString();
        $this->items = [$this->blankItem()];
    }

    public function blankItem(): array
    {
        return [
            'material_id' => null,
            'item_code' => '',
            'description' => '',
            'unit' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'line_total' => 0,
        ];
    }

    public function updatedProjectQuotationId(): void
    {
        $this->loadQuotation();
    }

    public function updatedProjectId(): void
    {
        $this->loadProjectFinancials();
        $this->calculateTotals();
    }

    public function updatedClientId(): void
    {
        $this->loadClient();
    }

    public function updatedItems($value, $key): void
    {
        if (str_ends_with($key, '.material_id')) {
            $index = (int) explode('.', $key)[0];
            $this->loadMaterialIntoItem($index);
        }

        $this->calculateTotals();
    }

    public function updatedLaborCharge(): void
    {
        $this->calculateTotals();
    }

    public function updatedTransportCharge(): void
    {
        $this->calculateTotals();
    }

    public function updatedOtherCharges(): void
    {
        $this->calculateTotals();
    }

    public function updatedVatApplicable(): void
    {
        $this->calculateTotals();
    }

    public function loadClient(): void
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

    public function loadProjectFinancials(): void
    {
        if (! $this->project_id) {
            $this->contract_value = 0;
            $this->previous_invoices = 0;
            $this->outstanding_before_invoice = 0;
            return;
        }

        $project = Project::find($this->project_id);

        if (! $project) {
            return;
        }

        $this->company_id = $project->company_id ?? $this->company_id;
        $this->client_id = $project->client_id ?? $this->client_id;
        $this->project_title = $project->project_name ?? $this->project_title;
        $this->contract_value = (float) ($project->contract_amount ?? 0);

        if ($this->client_id) {
            $this->loadClient();
        }

        $this->previous_invoices = (float) InvoiceVoucher::where('project_id', $project->id)
            ->whereNotIn('status', ['cancelled'])
            ->when($this->isEditing && $this->editingInvoiceId, function ($query) {
                $query->where('id', '!=', $this->editingInvoiceId);
            })
            ->sum('grand_total');

        $this->outstanding_before_invoice =
            (float) $this->contract_value - (float) $this->previous_invoices;
    }

    public function postInvoiceToLedger(int $invoiceId): void
        {
            $invoice = InvoiceVoucher::with(['project', 'client', 'company'])->findOrFail($invoiceId);

            $invoice->update([
                'status' => 'posted',
            ]);

            app(AccountingPostingService::class)->postInvoice($invoice);

            session()->flash('success', 'Invoice posted to General Ledger successfully.');
        }

    public function loadQuotation(): void
    {
        if (! $this->project_quotation_id) {
            return;
        }

        $quotation = ProjectQuotation::with('items')->find($this->project_quotation_id);

        if (! $quotation) {
            return;
        }

        $this->company_id = $quotation->company_id;
        $this->client_id = $quotation->client_id;
        $this->project_id = $quotation->project_id;

        $this->client_name = $quotation->client_name;
        $this->client_phone = $quotation->client_phone;
        $this->client_email = $quotation->client_email;
        $this->client_tin = $quotation->client_tin;
        $this->client_address = $quotation->client_address;

        $this->project_title = $quotation->project_title;
        $this->scope_of_work = $quotation->scope_of_work;

        $this->labor_charge = $quotation->labor_charge ?? 0;
        $this->transport_charge = $quotation->transport_charge ?? 0;
        $this->other_charges = $quotation->other_charges ?? 0;
        $this->other_charges_description = $quotation->other_charges_description;

        $this->vat_applicable = (bool) $quotation->vat_applicable;
        $this->terms_and_conditions = $quotation->terms_and_conditions;
        $this->notes = $quotation->notes;

        $this->items = $quotation->items->map(fn ($item) => [
            'material_id' => $item->material_id,
            'item_code' => $item->item_code,
            'description' => $item->description,
            'unit' => $item->unit,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'line_total' => $item->line_total,
        ])->toArray();

        if (count($this->items) === 0) {
            $this->items = [$this->blankItem()];
        }

        if ($this->project_id) {
            $this->loadProjectFinancials();
        }

        $this->calculateTotals();

        session()->flash('info', 'Quotation loaded into invoice.');
    }

    public function loadMaterialIntoItem(int $index): void
    {
        $materialId = $this->items[$index]['material_id'] ?? null;

        if (! $materialId) {
            return;
        }

        $material = Material::find($materialId);

        if (! $material) {
            return;
        }

        $this->items[$index]['item_code'] = $material->material_code;
        $this->items[$index]['description'] = $material->description ?: $material->name;
        $this->items[$index]['unit'] = $material->unit;
        $this->items[$index]['unit_price'] = $material->selling_price ?: $material->standard_price;

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
        $itemsTotal = 0;

        foreach ($this->items as $index => $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $lineTotal = round($qty * $price, 2);

            $this->items[$index]['line_total'] = $lineTotal;
            $itemsTotal += $lineTotal;
        }

        $this->subtotal = round(
            $itemsTotal +
            (float) $this->labor_charge +
            (float) $this->transport_charge +
            (float) $this->other_charges,
            2
        );

        if ((bool) $this->vat_applicable) {
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

        $this->balance_after_invoice = round(
            (float) $this->outstanding_before_invoice - (float) $this->grand_total,
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

    public function generateInvoiceNumber(): string
    {
        $last = InvoiceVoucher::latest('id')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'INV' . date('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function createNew(): void
    {
        $this->clearForm();
        session()->flash('info', 'New invoice buffer initialized.');
    }

    public function postLedger(): void
    {
        $this->save();
    }

    public function clearBuffer(): void
    {
        $this->clearForm();
        session()->flash('info', 'Invoice buffer cleared.');
    }

    public function sync(): void
    {
        if ($this->project_id) {
            $this->loadProjectFinancials();
        }

        $this->calculateTotals();

        session()->flash('info', 'Invoice centre synchronized.');
    }

    public function approveInvoice(int $invoiceId): void
    {
        InvoiceVoucher::findOrFail($invoiceId)->update([
            'status' => 'approved',
            'approved_by' => $this->approved_by ?: 'System Approver',
        ]);

        session()->flash('success', 'Invoice approved.');
    }

    public function cancelInvoice(int $invoiceId): void
    {
        InvoiceVoucher::findOrFail($invoiceId)->update([
            'status' => 'cancelled',
        ]);

        session()->flash('info', 'Invoice cancelled.');
    }

    public function editInvoice(int $invoiceId): void
    {
        $invoice = InvoiceVoucher::with('items')->findOrFail($invoiceId);

        $this->editingInvoiceId = $invoice->id;
        $this->isEditing = true;

        foreach ($invoice->only([
            'company_id','client_id','project_id','project_quotation_id',
            'invoice_date','due_date','client_name','client_phone','client_email',
            'client_tin','client_address','project_title','scope_of_work',
            'contract_value','previous_invoices','outstanding_before_invoice',
            'subtotal','labor_charge','transport_charge','other_charges',
            'other_charges_description','vat_applicable','vat_amount','getfund_amount',
            'nhil_amount','grand_total','balance_after_invoice','amount_in_words',
            'terms_and_conditions','notes','status','prepared_by','checked_by','approved_by',
        ]) as $key => $value) {
            $this->{$key} = $value;
        }

        $this->vat_applicable = (bool) $invoice->vat_applicable;

        $this->items = $invoice->items->map(fn ($item) => [
            'material_id' => $item->material_id,
            'item_code' => $item->item_code,
            'description' => $item->description,
            'unit' => $item->unit,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'line_total' => $item->line_total,
        ])->toArray();

        if (count($this->items) === 0) {
            $this->items = [$this->blankItem()];
        }

        session()->flash('info', 'Invoice loaded for editing.');
    }

    public function clearForm(): void
    {
        $this->reset();

        $this->search = '';
        $this->invoice_date = now()->toDateString();
        $this->due_date = now()->addDays(30)->toDateString();
        $this->status = 'draft';
        $this->items = [$this->blankItem()];
    }

    public function save(): void
    {
        if ($this->project_id) {
            $this->loadProjectFinancials();
        }

        $this->calculateTotals();

        $this->validate([
            'invoice_date' => ['required', 'date'],
            'client_name' => ['required', 'string', 'max:255'],
            'project_title' => ['required', 'string', 'max:255'],
            'grand_total' => ['required', 'numeric', 'min:0.01'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $payload = [
            'invoice_date' => $this->invoice_date,
            'due_date' => $this->due_date,
            'company_id' => $this->company_id,
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,
            'project_quotation_id' => $this->project_quotation_id,
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
            'client_email' => $this->client_email,
            'client_tin' => $this->client_tin,
            'client_address' => $this->client_address,
            'project_title' => $this->project_title,
            'scope_of_work' => $this->scope_of_work,
            'contract_value' => $this->contract_value,
            'previous_invoices' => $this->previous_invoices,
            'outstanding_before_invoice' => $this->outstanding_before_invoice,
            'subtotal' => $this->subtotal,
            'labor_charge' => $this->labor_charge,
            'transport_charge' => $this->transport_charge,
            'other_charges' => $this->other_charges,
            'other_charges_description' => $this->other_charges_description,
            'vat_applicable' => $this->vat_applicable,
            'vat_amount' => $this->vat_amount,
            'getfund_amount' => $this->getfund_amount,
            'nhil_amount' => $this->nhil_amount,
            'grand_total' => $this->grand_total,
            'balance_after_invoice' => $this->balance_after_invoice,
            'amount_in_words' => $this->amount_in_words,
            'terms_and_conditions' => $this->terms_and_conditions,
            'notes' => $this->notes,
            'status' => $this->status,
            'prepared_by' => $this->prepared_by,
            'checked_by' => $this->checked_by,
            'approved_by' => $this->approved_by,
        ];

        $wasEditing = $this->isEditing;

        DB::transaction(function () use ($payload) {
            if ($this->isEditing && $this->editingInvoiceId) {
                $invoice = InvoiceVoucher::findOrFail($this->editingInvoiceId);
                $invoice->update($payload);
                $invoice->items()->delete();
            } else {
                $invoice = InvoiceVoucher::create(array_merge($payload, [
                    'invoice_number' => $this->generateInvoiceNumber(),
                ]));
            }

            foreach ($this->items as $item) {
                $invoice->items()->create([
                    'material_id' => $item['material_id'] ?? null,
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

        session()->flash(
            'success',
            $wasEditing ? 'Invoice updated successfully.' : 'Invoice posted successfully.'
        );
    }

    public function render()
    {
        $companies = Company::where('active', true)->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $projects = Project::orderBy('project_name')->get();
        $materials = Material::where('active', true)->orderBy('name')->get();
        $quotations = ProjectQuotation::latest()->get();

        $invoices = InvoiceVoucher::with(['project', 'client', 'company'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', "%{$this->search}%")
                        ->orWhere('client_name', 'like', "%{$this->search}%")
                        ->orWhere('project_title', 'like', "%{$this->search}%")
                        ->orWhere('status', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.finance.invoice-centre-page', compact(
            'companies', 'clients', 'projects', 'materials', 'quotations', 'invoices'
        ))->layout('layouts.erp');
    }
}