<div class="card glass-card mb-4 border-0 shadow-sm hover-shadow transition-all">
    <div
        class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary">
            @if(isset($icon))
                <i class="{{ $icon }} me-2"></i>
            @endif
            {{ $title }}
        </h5>
        @if(isset($actions))
            <div class="card-actions">
                {{ $actions }}
            </div>
        @endif
    </div>
    <div class="card-body px-4 pb-4">
        {{ $slot }}
    </div>
    @if(isset($footer))
        <div class="card-footer bg-transparent border-top-0 px-4 pb-3 pt-0 text-end">
            {{ $footer }}
        </div>
    @endif
</div>