{{-- Společný formulář pro vytvoření i úpravu zařízení --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Formulář obsahuje chyby:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-md-8">
        <label for="name" class="form-label">Název zařízení *</label>
        <input type="text" id="name" name="name" maxlength="255" required
               value="{{ old('name', $equipment->name) }}"
               class="form-control @error('name') is-invalid @enderror">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="inventory_number" class="form-label">Evidenční číslo *</label>
        <input type="text" id="inventory_number" name="inventory_number" maxlength="50" required
               value="{{ old('inventory_number', $equipment->inventory_number) }}"
               placeholder="např. INV-00042"
               class="form-control inv @error('inventory_number') is-invalid @enderror">
        @error('inventory_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Musí být jedinečné.</div>
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Stav *</label>
        <select id="status" name="status" required class="form-select @error('status') is-invalid @enderror">
            @foreach (\App\Models\Equipment::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $equipment->status) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="purchased_at" class="form-label">Datum nákupu *</label>
        <input type="date" id="purchased_at" name="purchased_at" required
               min="1950-01-01" max="{{ now()->format('Y-m-d') }}"
               value="{{ old('purchased_at', $equipment->purchased_at?->format('Y-m-d')) }}"
               class="form-control @error('purchased_at') is-invalid @enderror">
        @error('purchased_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
