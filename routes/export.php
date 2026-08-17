<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('export/csv', [ExportController::class, 'csv'])->name('export.csv');
});
