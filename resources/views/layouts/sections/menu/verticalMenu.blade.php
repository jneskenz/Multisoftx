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

    <!-- Users -->
    @if (Route::has('users.index'))
      <li class="menu-item {{ request()->routeIs('users.*') ? 'active open' : '' }}">
        <a href="{{ route('users.index') }}" class="menu-link" wire:navigate>
          <i class="menu-icon icon-base ti tabler-users"></i>
          <div data-i18n="Users">{{ __('Usuarios') }}</div>
        </a>
      </li>
    @endif

    <!-- Roles -->
    @if (Route::has('roles.index'))
      <li class="menu-item {{ request()->routeIs('roles.*') ? 'active open' : '' }}">
        <a href="{{ route('roles.index') }}" class="menu-link" wire:navigate>
          <i class="menu-icon icon-base ti tabler-shield"></i>
          <div data-i18n="Roles">{{ __('Roles') }}</div>
        </a>
      </li>
    @endif

    <!-- Teams -->
    {{-- @if (class_exists('App\Models\Team') && Route::has('teams.index'))
      <li class="menu-item {{ request()->routeIs('teams.*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon icon-base ti tabler-building-community"></i>
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
    @endif --}}

    <!-- Modules -->
    @if (class_exists('Nwidart\Modules\Facades\Module'))
      @php
        $modules = \Nwidart\Modules\Facades\Module::allEnabled();
      @endphp

      @if (count($modules))
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ __('Modules') }}</span>
        </li>

        @foreach ($modules as $module)
          @php
            $lowerName = $module->getLowerName();
            $routeName = $lowerName . '.index';
          @endphp

          @if (Route::has($routeName))
            <li class="menu-item {{ request()->routeIs($lowerName . '.*') ? 'active' : '' }}">
              <a href="{{ route($routeName) }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-{{ $lowerName === 'crm' ? 'address-book' : ($lowerName === 'erp' ? 'package' : 'user-check') }}"></i>
                <div data-i18n="{{ $module->getName() }}">{{ $module->getName() }}</div>
              </a>
            </li>
          @endif
        @endforeach
      @endif
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
  
  <!-- Account Menu (footer) -->
  @auth
    <div class="sidebar-account-menu mt-auto">
      <div class="dropdown dropup w-100">
        <a href="javascript:void(0);" class="sidebar-account-toggle d-flex align-items-center w-100" data-bs-toggle="dropdown" aria-expanded="false">
          <div class="sidebar-account-avatar flex-shrink-0">
            @if (!empty(Auth::user()->profile_photo_url))
              <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
            @else
              <span>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
            @endif
          </div>

          <div class="sidebar-account-info menu-text flex-grow-1 text-truncate">
            <span class="sidebar-account-name">{{ Auth::user()->name }}</span>
            <span class="sidebar-account-email">{{ Auth::user()->email }}</span>
          </div>

          <i class="icon-base ti tabler-dots-vertical sidebar-account-chevron menu-text flex-shrink-0"></i>
        </a>

        <ul class="dropdown-menu dropdown-menu-end sidebar-account-dropdown">
          @if (Route::has('profile.edit'))
            <li>
              <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="icon-base ti tabler-user me-2"></i>
                <span>{{ __('Mi Perfil') }}</span>
              </a>
            </li>
          @endif

          @if (Route::has('help.index'))
            <li>
              <a class="dropdown-item" href="{{ route('help.index') }}">
                <i class="icon-base ti tabler-help-circle me-2"></i>
                <span>{{ __('Ayuda') }}</span>
              </a>
            </li>
          @endif

          @if (Route::has('profile.edit') || Route::has('help.index'))
            <li><hr class="dropdown-divider"></li>
          @endif

          @if (Route::has('logout'))
            <li>
              <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                <i class="icon-base ti tabler-logout me-2"></i>
                <span>{{ __('Cerrar Sesión') }}</span>
              </a>
            </li>
          @endif
        </ul>
      </div>

      @if (Route::has('logout'))
        <form id="sidebar-logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
          @csrf
        </form>
      @endif
    </div>
  @endauth

  <style>
    .sidebar-account-menu {
      border-top: 1px solid var(--bs-border-color, rgba(0,0,0,.08));
      padding: .75rem 1rem;
    }

    .sidebar-account-toggle {
      padding: .5rem;
      border-radius: .5rem;
      transition: background-color .2s ease;
      text-decoration: none;
      color: inherit;
    }

    .sidebar-account-toggle:hover,
    .sidebar-account-toggle:focus,
    .sidebar-account-toggle[aria-expanded="true"] {
      background-color: rgba(115, 103, 240, .08);
    }

    .sidebar-account-avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #7367f0, #9c93fa);
      color: #fff;
      font-weight: 600;
      font-size: .85rem;
      margin-right: .65rem;
    }

    .sidebar-account-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .sidebar-account-info {
      display: flex;
      flex-direction: column;
      line-height: 1.25;
      overflow: hidden;
      white-space: nowrap;
    }

    .sidebar-account-name {
      font-size: .8125rem;
      font-weight: 600;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .sidebar-account-email {
      font-size: .72rem;
      color: var(--bs-secondary-color, #8a8d93);
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .sidebar-account-chevron {
      font-size: .85rem;
      color: var(--bs-secondary-color, #8a8d93);
      margin-left: .25rem;
    }

    .sidebar-account-dropdown {
      min-width: 14rem;
      padding: .5rem;
      border-radius: .65rem;
      box-shadow: 0 .25rem 1rem rgba(0,0,0,.12);
    }

    .sidebar-account-dropdown .dropdown-item {
      border-radius: .4rem;
      padding: .5rem .65rem;
      font-size: .8125rem;
    }

    /* Sidebar colapsado: solo avatar */
    html.layout-menu-collapsed:not(.layout-menu-hover) .sidebar-account-menu {
      padding-inline: .5rem;
    }

    html.layout-menu-collapsed:not(.layout-menu-hover) .sidebar-account-toggle {
      justify-content: center;
      padding: .5rem 0;
    }

    html.layout-menu-collapsed:not(.layout-menu-hover) .sidebar-account-avatar {
      margin-right: 0;
    }

    html.layout-menu-collapsed:not(.layout-menu-hover) .sidebar-account-info,
    html.layout-menu-collapsed:not(.layout-menu-hover) .sidebar-account-chevron {
      display: none;
    }
  </style>
  
</aside>
<!-- / Vertical Menu -->
