<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderPrintController;


Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::get('/orders/{order}/print', [OrderPrintController::class, 'print'])->name('orders.print');