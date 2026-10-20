<?php

namespace App\Http\Requests;

use App\Models\Equipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'inventory_number' => strtoupper(trim((string) $this->input('inventory_number'))),
        ]);
    }

    public function rules(): array
    {
        /** @var Equipment|null $equipment */
        $equipment = $this->route('equipment');

        return [
            'name' => ['required', 'string', 'max:255'],
            'inventory_number' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9][A-Z0-9\-\/]*$/',
                Rule::unique('equipment', 'inventory_number')->ignore($equipment?->id),
            ],
            'status' => ['required', Rule::in(array_keys(Equipment::STATUSES))],
            'purchased_at' => ['required', 'date_format:Y-m-d', 'after_or_equal:1950-01-01', 'before_or_equal:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'název',
            'inventory_number' => 'evidenční číslo',
            'status' => 'stav',
            'purchased_at' => 'datum nákupu',
        ];
    }

    public function messages(): array
    {
        return [
            'inventory_number.unique' => 'Zařízení s tímto evidenčním číslem už existuje.',
            'inventory_number.regex' => 'Evidenční číslo smí obsahovat jen písmena A–Z, číslice, pomlčku a lomítko.',
            'status.in' => 'Zvolený stav není povolený (povoleno: aktivní, v servisu, vyřazeno).',
            'purchased_at.date_format' => 'Datum nákupu musí být platné datum ve formátu RRRR-MM-DD.',
            'purchased_at.after_or_equal' => 'Datum nákupu nesmí být dříve než 1. 1. 1950.',
            'purchased_at.before_or_equal' => 'Datum nákupu nesmí být v budoucnosti.',
        ];
    }
}
