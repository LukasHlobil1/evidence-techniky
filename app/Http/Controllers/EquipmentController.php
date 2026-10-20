<?php

namespace App\Http\Controllers;

use App\Http\Requests\EquipmentRequest;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EquipmentController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Equipment::STATUSES))],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $status = $validated['status'] ?? null;
        $q = $validated['q'] ?? null;

        $equipment = Equipment::query()
            ->withCount('serviceRecords')
            ->withSum('serviceRecords', 'cost')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($q, fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('inventory_number', 'like', "%{$q}%")))
            ->orderBy('inventory_number')
            ->paginate(10)
            ->withQueryString();

        $counts = Equipment::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('equipment.index', compact('equipment', 'status', 'q', 'counts'));
    }

    public function create(): View
    {
        return view('equipment.create', ['equipment' => new Equipment(['status' => 'active'])]);
    }

    public function store(EquipmentRequest $request): RedirectResponse
    {
        $equipment = Equipment::create($request->validated());

        return redirect()->route('equipment.show', $equipment)
            ->with('success', "Zařízení {$equipment->inventory_number} bylo přidáno.");
    }

    public function show(Equipment $equipment): View
    {
        $records = $equipment->serviceRecords()->get();
        $totalCost = (float) $records->sum('cost');

        return view('equipment.show', compact('equipment', 'records', 'totalCost'));
    }

    public function edit(Equipment $equipment): View
    {
        return view('equipment.edit', compact('equipment'));
    }

    public function update(EquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        $equipment->update($request->validated());

        return redirect()->route('equipment.show', $equipment)
            ->with('success', 'Změny byly uloženy.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        $number = $equipment->inventory_number;
        $equipment->delete(); // servisní záznamy se smažou kaskádově

        return redirect()->route('equipment.index')
            ->with('success', "Zařízení {$number} bylo smazáno včetně servisní historie.");
    }

    /** Volitelné rozšíření: export servisní historie jednoho zařízení do CSV. */
    public function export(Equipment $equipment): StreamedResponse
    {
        $filename = 'servisni-historie-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $equipment->inventory_number) . '.csv';
        $records = $equipment->serviceRecords()->get();

        return response()->streamDownload(function () use ($equipment, $records) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM, aby Excel správně zobrazil diakritiku
            fputcsv($out, ['Evidenční číslo', 'Zařízení', 'Datum servisu', 'Popis', 'Cena (Kč)'], ';');
            foreach ($records as $record) {
                fputcsv($out, [
                    $equipment->inventory_number,
                    $equipment->name,
                    $record->serviced_at->format('Y-m-d'),
                    $record->description,
                    number_format((float) $record->cost, 2, ',', ''),
                ], ';');
            }
            fputcsv($out, ['', '', '', 'Celkem', number_format((float) $records->sum('cost'), 2, ',', '')], ';');
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
