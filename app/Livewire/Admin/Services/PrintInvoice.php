<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('livewire.admin.layouts.app')]
class PrintInvoice extends Component
{
    public Service $record;

    public Setting $setting;

    public function mount(): void 
    {
        $this->setting = Setting::query()->first();
    }

    public function render()
    {
        return view('livewire.admin.services.print-invoice');
    }
}
