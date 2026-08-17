<?php

use App\Http\Controllers\AssistantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('assistant', [AssistantController::class, 'index'])->name('assistant.index');
    Route::post('assistant/stream', [AssistantController::class, 'stream'])->name('assistant.stream');
});
