@props(['status'])

@php
    $color = $status instanceof \App\Contracts\HasColorAndLabel ? $status->color() : 'secondary';
    $label = $status instanceof \App\Contracts\HasColorAndLabel ? $status->label() : $status;
@endphp

<span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} px-3 py-2 rounded-pill">
    {{ $label }}
</span>