@extends('layouts.app')

@section('title', 'Seznam zařízení – Evidence techniky')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Firemní nářadí</h1>
    <a href="{{ route('equipment.create') }}" class="btn btn-primary">+ Přidat zařízení</a>
</div>

{{-- Filtr podle stavu --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-3" role="group" aria-label="Filtr podle stavu">
            <a href="{{ route('equipment.index', array_filter(['q' => $q])) }}"
               class="btn btn-sm {{ $status ? 'btn-outline-secondary' : 'btn-dark' }}">
                Vše <span class="badge text-bg-light ms-1">{{ $counts->sum() }}</span>
            </a>
            @foreach (\App\Models\Equipment::STATUSES as $key => $label)
                <a href="{{ route('equipment.index', array_filter(['status' => $key, 'q' => $q])) }}"
                   class="btn btn-sm {{ $status === $key ? 'btn-dark' : 'btn-outline-secondary' }}">
                    <x-status-badge :status="$key" class="py-0 px-2" />
                    <span class="badge text-bg-light ms-1">{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('equipment.index') }}" class="row g-2">
            @if ($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="col-sm-8 col-md-6">
                <input type="search" name="q" value="{{ $q }}" class="form-control"
                       placeholder="Hledat podle názvu nebo evidenčního čísla…">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary">Hledat</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>Evidenční číslo</th>
                <th>Název</th>
                <th>Stav</th>
                <th>Datum nákupu</th>
                <th class="text-end">Servisů</th>
                <th class="text-end">Náklady na servis</th>
                <th class="text-end">Akce</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($equipment as $item)
                <tr class="row-{{ $item->status }}">
                    <td class="inv">{{ $item->inventory_number }}</td>
                    <td><a href="{{ route('equipment.show', $item) }}" class="text-decoration-none">{{ $item->name }}</a></td>
                    <td><x-status-badge :status="$item->status" /></td>
                    <td>{{ $item->purchased_at->format('d. m. Y') }}</td>
                    <td class="text-end">{{ $item->service_records_count }}</td>
                    <td class="text-end">{{ number_format((float) $item->service_records_sum_cost, 2, ',', ' ') }} Kč</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('equipment.show', $item) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        <a href="{{ route('equipment.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Upravit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Žádné zařízení neodpovídá filtru.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $equipment->links() }}
</div>
@endsection
