<div class="row">
    <div class="col-md-3 col-12 mb-4">
        <div class="card">
            <div class="card-body p-2">
                <div class="list-group list-group-flush">
                    <a href="{{ route('profile.edit') }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                       wire:navigate>
                        <i class="icon-base ti tabler-user icon-md"></i>
                        <span>{{ __('Profile') }}</span>
                    </a>
                    <a href="{{ route('security.edit') }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 {{ request()->routeIs('security.edit') ? 'active' : '' }}"
                       wire:navigate>
                        <i class="icon-base ti tabler-shield icon-md"></i>
                        <span>{{ __('Security') }}</span>
                    </a>
                    <a href="{{ route('appearance.edit') }}"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-3 {{ request()->routeIs('appearance.edit') ? 'active' : '' }}"
                       wire:navigate>
                        <i class="icon-base ti tabler-palette icon-md"></i>
                        <span>{{ __('Appearance') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9 col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $heading ?? '' }}</h5>
                <small class="text-muted">{{ $subheading ?? '' }}</small>
            </div>
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
