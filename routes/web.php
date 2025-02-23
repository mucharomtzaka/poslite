<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderPrintController;

//default route
Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});
// route print nota
Route::get('/orders/{order}/print', [OrderPrintController::class, 'print'])->name('orders.print');