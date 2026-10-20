@extends('layouts.app')

@section('title', $equipment->inventory_number . ' – Evidence techniky')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('equipment.index') }}">Zařízení</a></li>
        <li class="breadcrumb-item active">{{ $equipment->inventory_number }}</li>
    </ol>
</nav>

<div class="row g-3 mb-4">
    {{-- Údaje o zařízení --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <div class="inv text-muted">{{ $equipment->inventory_number }}</div>
                        <h1 class="h3 mb-2">{{ $equipment->name }}</h1>
                        <x-status-badge :status="$equipment->status" />
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('equipment.edit', $equipment) }}" class="btn btn-outline-secondary">Upravit</a>
                        <form method="POST" action="{{ route('equipment.destroy', $equipment) }}"
                              onsubmit="return confirm('Opravdu smazat zařízení {{ $equipment->inventory_number }} i s celou servisní historií?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger">Smazat</button>
                        </form>
                    </div>
                </div>
                <dl class="row mt-3 mb-0">
                    <dt class="col-sm-4">Datum nákupu</dt>
                    <dd class="col-sm-8">{{ $equipment->purchased_at->format('d. m. Y') }}</dd>
                    <dt class="col-sm-4">Počet servisních zásahů</dt>
                    <dd class="col-sm-8">{{ $records->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Součet servisních nákladů --}}
    <div class="col-lg-4">
        <div class="total-box h-100 p-4 d-flex flex-column justify-content-center">
            <div class="small text-uppercase opacity-75">Celkové náklady na servis</div>
            <div class="display-6 fw-bold" id="total-cost">{{ number_format($totalCost, 2, ',', ' ') }} Kč</div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Přidání servisního záznamu --}}
    <div class="col-lg-4 order-lg-2">
        <div class="card">
            <div class="card-body">
                <h2 class="h5 mb-3">Přidat servisní záznam</h2>

                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('equipment.service-records.store', $equipment) }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="serviced_at" class="form-label">Datum servisu *</label>
                        <input type="date" id="serviced_at" name="serviced_at" required
                               min="{{ $equipment->purchased_at->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}"
                               value="{{ old('serviced_at', now()->format('Y-m-d')) }}"
                               class="form-control @error('serviced_at') is-invalid @enderror">
                        @error('serviced_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Popis zásahu *</label>
                        <textarea id="description" name="description" rows="3" required maxlength="2000"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="cost" class="form-label">Cena (Kč) *</label>
                        <input type="number" id="cost" name="cost" required min="0" step="0.01"
                               value="{{ old('cost') }}" placeholder="0,00"
                               class="form-control @error('cost') is-invalid @enderror">
                        @error('cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Přidat záznam</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Servisní historie --}}
    <div class="col-lg-8 order-lg-1">
        <div class="card">
            <div class="card-body pb-0 d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Servisní historie</h2>
                @if ($records->isNotEmpty())
                    <a href="{{ route('equipment.export', $equipment) }}" class="btn btn-sm btn-outline-success">⬇ Export do CSV</a>
                @endif
            </div>
            <div class="table-responsive mt-3">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Datum</th>
                        <th>Popis</th>
                        <th class="text-end">Cena</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td class="text-nowrap">{{ $record->serviced_at->format('d. m. Y') }}</td>
                            <td>{{ $record->description }}</td>
                            <td class="text-end text-nowrap">{{ number_format((float) $record->cost, 2, ',', ' ') }} Kč</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('equipment.service-records.destroy', [$equipment, $record]) }}"
                                      onsubmit="return confirm('Smazat tento servisní záznam?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0" title="Smazat záznam">Smazat</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Zařízení zatím nemá žádný servisní záznam.</td>
                        </tr>
                    @endforelse
                    </tbody>
                    @if ($records->isNotEmpty())
                        <tfoot>
                        <tr class="fw-bold">
                            <td colspan="2" class="text-end">Celkem</td>
                            <td class="text-end text-nowrap">{{ number_format($totalCost, 2, ',', ' ') }} Kč</td>
                            <td></td>
                        </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
