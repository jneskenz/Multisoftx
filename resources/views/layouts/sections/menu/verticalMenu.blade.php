<!-- Vertical Menu -->
@php
  $currentTeam = request()->route('current_team') ?? Auth::user()?->currentTeam;
  $dashboardUrl = $currentTeam ? route('dashboard', $currentTeam) : url('/');
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <!-- App brand -->
  <div class="app-brand demo">


    {{-- <a href="{{ url('/') }}" class="app-brand-link">
      <span class="app-brand-text demo menu-text fw-bold ms-0">{{ config('app.name', 'CRM') }}</span>
    </a> --}}
    <a href="{{ url('/') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M16 2L2 9L16 16L30 9L16 2Z" fill="#7367f0" />
              <path d="M2 23L16 30L30 23V9L16 16L2 9V23Z" fill="#7367f0" fill-opacity="0.5" />
          </svg>
      </span>
      <span class="app-brand-text demo ms-2 menu-text fw-bold">{{ config('app.name', 'Multisoft') }}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="icon-base ti tabler-menu-2"></i>
    </a>

    {{-- <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="ti tabler-circle-dot menu-toggle-icon d-none d-xl-block align-middle"></i>
        <i class="ti tabler-x d-block d-xl-none align-middle"></i>
    </a> --}}

  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ Route::currentRouteName() === 'dashboard' ? 'active' : '' }}">
      <a href="{{ $dashboardUrl }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-smart-home"></i>
        <div data-i18n="Dashboard">{{ __('Dashboard') }}</div>
      </a>
    </li>

    <!-- Teams -->
    @if (class_exists('App\Models\Team') && Route::has('teams.index'))
      <li class="menu-item {{ request()->routeIs('teams.*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon icon-base ti tabler-users"></i>
          <div data-i18n="Teams">{{ __('Teams') }}</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item">
            <a href="{{ route('teams.index') }}" class="menu-link">
              <div data-i18n="Teams">{{ __('Teams') }}</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    <!-- Settings Header -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ __('Settings') }}</span>
    </li>

    <!-- Settings -->
    @if (Route::has('profile.edit'))
      <li class="menu-item {{ request()->routeIs('profile.edit', 'appearance.edit', 'security.edit') ? 'active' : '' }}">
        <a href="{{ route('profile.edit') }}" class="menu-link">
          <i class="menu-icon icon-base ti tabler-settings"></i>
          <div data-i18n="Settings">{{ __('Settings') }}</div>
        </a>
      </li>
    @endif
  </ul>
</aside>
<!-- / Vertical Menu -->
