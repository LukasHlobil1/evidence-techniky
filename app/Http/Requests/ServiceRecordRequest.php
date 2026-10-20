<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Povolíme i českou desetinnou čárku (např. 1250,50).
        if (is_string($this->input('cost'))) {
            $this->merge(['cost' => str_replace([' ', ','], ['', '.'], trim($this->input('cost')))]);
        }
    }

    public function rules(): array
    {
        $equipment = $this->route('equipment');
        $purchased = $equipment?->purchased_at?->format('Y-m-d') ?? '1950-01-01';

        return [
            'description' => ['required', 'string', 'max:2000'],
            'serviced_at' => ['required', 'date_format:Y-m-d', 'after_or_equal:' . $purchased, 'before_or_equal:today'],
            'cost' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
        ];
    }

    public function attributes(): array
    {
        return [
            'description' => 'popis',
            'serviced_at' => 'datum servisu',
            'cost' => 'cena',
        ];
    }

    public function messages(): array
    {
        return [
            'serviced_at.date_format' => 'Datum servisu musí být platné datum ve formátu RRRR-MM-DD.',
            'serviced_at.after_or_equal' => 'Datum servisu nesmí být před datem nákupu zařízení.',
            'serviced_at.before_or_equal' => 'Datum servisu nesmí být v budoucnosti.',
            'cost.numeric' => 'Cena musí být číslo.',
            'cost.decimal' => 'Cena smí mít nejvýše 2 desetinná místa.',
            'cost.min' => 'Cena nesmí být záporná.',
        ];
    }
}
