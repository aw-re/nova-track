@props(['type' => 'primary', 'value', 'label', 'icon'])

<div class="card stat-card {{ $type }} glass-card h-100 border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="stat-value fw-bold text-dark fs-3">{{ $value }}</div>
                <div class="stat-label text-uppercase text-muted fw-bold small ls-1">{{ $label }}</div>
            </div>
            <div class="stat-icon d-flex align-items-center justify-content-center">
                <i class="{{ $icon }}"></i>
            </div>
        </div>
    </div>
</div>