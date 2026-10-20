<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Evidence techniky')</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body { background: #f4f5f7; }
        .navbar-brand { font-weight: 700; letter-spacing: .02em; }
        .card { border: 0; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .inv { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 600; }
        /* Stav zařízení – barva + ikona + text, aby byl rozlišitelný i bez barev */
        .status-badge { display: inline-flex; align-items: center; gap: .35rem; padding: .3rem .65rem;
            border-radius: 999px; font-size: .85rem; font-weight: 600; border: 1px solid transparent; white-space: nowrap; }
        .status-active  { background: #e6f4ea; color: #146c2e; border-color: #a8dab5; }
        .status-service { background: #fff4e0; color: #8a5300; border-color: #ffd28a; }
        .status-retired { background: #eceff1; color: #455a64; border-color: #c5ccd1; text-decoration: line-through; text-decoration-thickness: 1px; }
        .status-dot { width: .55rem; height: .55rem; border-radius: 50%; background: currentColor; }
        tr.row-retired td { color: #78868f; }
        .total-box { background: #1f2a37; color: #fff; border-radius: .5rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('equipment.index') }}">🛠 Evidence techniky</a>
        <a class="btn btn-sm btn-outline-light" href="{{ route('equipment.create') }}">+ Nové zařízení</a>
    </div>
</nav>

<main class="container pb-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zavřít"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
