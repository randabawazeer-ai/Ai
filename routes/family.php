<?php

use App\Http\Controllers\FamilyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('family', [FamilyController::class, 'index'])->name('family.index');
    Route::post('family', [FamilyController::class, 'store'])->name('family.store');
    Route::post('family/invite', [FamilyController::class, 'invite'])->name('family.invite');
    Route::post('family/join', [FamilyController::class, 'accept'])->name('family.join');
    Route::delete('family/leave', [FamilyController::class, 'leave'])->name('family.leave');
});
