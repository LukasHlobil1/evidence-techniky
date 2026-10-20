<?php

// Základní české chybové hlášky pro pravidla použitá v aplikaci.
return [
    'required' => 'Pole :attribute je povinné.',
    'string' => 'Pole :attribute musí být text.',
    'max' => [
        'string' => 'Pole :attribute může mít nejvýše :max znaků.',
        'numeric' => 'Pole :attribute nesmí být větší než :max.',
    ],
    'min' => [
        'numeric' => 'Pole :attribute nesmí být menší než :min.',
    ],
    'numeric' => 'Pole :attribute musí být číslo.',
    'decimal' => 'Pole :attribute musí mít :decimal desetinných míst.',
    'unique' => 'Hodnota pole :attribute už je použita.',
    'in' => 'Zvolená hodnota pole :attribute není platná.',
    'regex' => 'Pole :attribute má neplatný formát.',
    'date' => 'Pole :attribute musí být platné datum.',
    'date_format' => 'Pole :attribute musí odpovídat formátu :format.',
    'after_or_equal' => 'Pole :attribute musí být datum :date nebo pozdější.',
    'before_or_equal' => 'Pole :attribute musí být datum :date nebo dřívější.',

    'attributes' => [],
];
