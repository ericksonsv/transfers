<?php

use App\Livewire\Admin\Services\PrintAllInvoices;
use App\Livewire\Admin\Services\PrintInvoice;
use App\Livewire\Admin\Services\PrintService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('admin/services/{record}/print-service', PrintService::class)
    ->name('admin.services.print-service');
Route::get('admin/services/{record}/print-invoice', PrintInvoice::class)
    ->name('admin.services.print-invoice');
Route::get('admin/orders/{record}/print-all-invoices', PrintAllInvoices::class)
    ->name('admin.orders.print-all-invoices');