<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRecordRequest;
use App\Models\Equipment;
use App\Models\ServiceRecord;
use Illuminate\Http\RedirectResponse;

class ServiceRecordController extends Controller
{
    /** Přidání servisního záznamu z detailu zařízení. */
    public function store(ServiceRecordRequest $request, Equipment $equipment): RedirectResponse
    {
        $equipment->serviceRecords()->create($request->validated());

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'Servisní záznam byl přidán.');
    }

    public function destroy(Equipment $equipment, ServiceRecord $serviceRecord): RedirectResponse
    {
        $serviceRecord->delete();

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'Servisní záznam byl smazán.');
    }
}
