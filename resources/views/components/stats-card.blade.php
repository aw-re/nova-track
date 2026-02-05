<div class="card stat-card {{ $type ?? 'primary' }} glass-card h-100">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="stat-value">{{ $value }}</div>
                <div class="stat-label text-uppercase text-muted fw-bold small">{{ $label }}</div>
            </div>
            <div class="stat-icon rounded-3 p-3 d-flex align-items-center justify-content-center">
                <i class="{{ $icon }} fa-2x"></i>
            </div>
        </div>
    </div>
</div>