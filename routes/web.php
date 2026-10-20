<?php

use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ServiceRecordController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/equipment');

Route::resource('equipment', EquipmentController::class);

Route::get('equipment/{equipment}/export', [EquipmentController::class, 'export'])
    ->name('equipment.export');

Route::post('equipment/{equipment}/service-records', [ServiceRecordController::class, 'store'])
    ->name('equipment.service-records.store');

Route::delete('equipment/{equipment}/service-records/{serviceRecord}', [ServiceRecordController::class, 'destroy'])
    ->scopeBindings()
    ->name('equipment.service-records.destroy');
