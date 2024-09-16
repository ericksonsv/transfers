<?php

namespace App\Livewire\Admin\Services;

use App\Models\Order;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('livewire.admin.layouts.app')]
class PrintAllInvoices extends Component
{
    public Order $record;

    public Setting $setting;

    public function mount(): void 
    {
        $this->setting = Setting::query()->first();
    }

    public function render()
    {
        return view('livewire.admin.services.print-all-invoices');
    }
}
