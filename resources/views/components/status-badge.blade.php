@props(['status'])
@php
    $icons = ['active' => '✔', 'service' => '🔧', 'retired' => '✖'];
    $label = \App\Models\Equipment::STATUSES[$status] ?? $status;
@endphp
<span {{ $attributes->merge(['class' => "status-badge status-{$status}"]) }} title="Stav: {{ $label }}">
    <span aria-hidden="true">{{ $icons[$status] ?? '•' }}</span>
    {{ $label }}
</span>
