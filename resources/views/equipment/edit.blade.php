@extends('layouts.app')

@section('title', 'Úprava ' . $equipment->inventory_number . ' – Evidence techniky')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('equipment.index') }}">Zařízení</a></li>
        <li class="breadcrumb-item"><a href="{{ route('equipment.show', $equipment) }}">{{ $equipment->inventory_number }}</a></li>
        <li class="breadcrumb-item active">Upravit</li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <h1 class="h4 mb-4">Upravit zařízení</h1>
        <form method="POST" action="{{ route('equipment.update', $equipment) }}" novalidate>
            @csrf
            @method('PUT')
            @include('equipment._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Uložit změny</button>
                <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-outline-secondary">Zrušit</a>
            </div>
        </form>
    </div>
</div>
@endsection
