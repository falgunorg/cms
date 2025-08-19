@props(['label', 'value', 'color'])

@php
$colorMap = [
'blue' => ['bg-primary text-white', 'text-white'],
'green' => ['bg-success text-white', 'text-white'],
'yellow' => ['bg-warning text-dark', 'text-dark'],
'red' => ['bg-danger text-white', 'text-white'],
'gray' => ['bg-secondary text-white', 'text-white'],
'indigo' => ['bg-info text-white', 'text-white'], // Closest match
'purple' => ['bg-dark text-white', 'text-white'],  // Closest match
'orange' => ['bg-warning text-dark', 'text-dark'], // Reuse warning
'teal' => ['bg-info text-white', 'text-white'],    // Reuse info
];

[$cardClasses, $textClasses] = $colorMap[$color] ?? $colorMap['gray'];
@endphp

<div class="card {{ $cardClasses }} p-3 text-center shadow-sm">
    <div class="card-body">
        <h6 class="{{ $textClasses }}">{{ $label }}</h6>
        <h3 class="fw-bold {{ $textClasses }}">{{ $value }}</h3>
    </div>
</div>
