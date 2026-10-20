@extends('layouts.app')

@section('title', 'Nové zařízení – Evidence techniky')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('equipment.index') }}">Zařízení</a></li>
        <li class="breadcrumb-item active">Nové zařízení</li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <h1 class="h4 mb-4">Nové zařízení</h1>
        <form method="POST" action="{{ route('equipment.store') }}" novalidate>
            @csrf
            @include('equipment._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Uložit</button>
                <a href="{{ route('equipment.index') }}" class="btn btn-outline-secondary">Zrušit</a>
            </div>
        </form>
    </div>
</div>
@endsection
