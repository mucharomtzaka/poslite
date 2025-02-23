<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderPrintController;

// Redirect root URL to Filament admin panel
Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});
/// route print
Route::get('/orders/{order}/print', [OrderPrintController::class, 'print'])->name('orders.print');