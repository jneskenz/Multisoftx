<!DOCTYPE html>
@php
  $menuFixed = $menuFixed ?? true;
  $navbarType = $navbarType ?? 'layout-navbar-fixed';
  $contentLayout = isset($container) ? ($container === 'container-xxl' ? 'layout-compact' : 'layout-wide') : '';

  // Get theme settings
  $theme = session()->get('theme', 'light');
  $skinName = 'default';
@endphp

<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  dir="ltr"
  data-bs-theme="{{ $theme }}"
  data-assets-path="{{ asset('vuexy') }}/"
  data-template="vertical-menu-template"
  data-skin="{{ $skinName }}"
  class="{{ $navbarType }} {{ $menuFixed ? 'layout-menu-fixed' : '' }} {{ $contentLayout }}"
>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

  <title>@yield('title', __('Dashboard')) | {{ config('app.name', 'Laravel') }}</title>
  
  <meta name="description" content="Multisoftx CRM">
  <meta name="keywords" content="crm, dashboard">
  
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

  <!-- Icons -->
  <link rel="stylesheet" href="{{ asset('vuexy/vendor/fonts/fontawesome.css') }}">
  <link rel="stylesheet" href="{{ asset('vuexy/vendor/fonts/flag-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('vuexy/vendor/fonts/iconify-icons.css') }}">


  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ asset('vuexy/vendor/css/core.css') }}" class="template-customizer-core-css">
  <link rel="stylesheet" href="{{ asset('vuexy/css/demo.css') }}">

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/select2/select2.css') }}">
  <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/pickr/pickr-themes.css') }}" />
  <link rel="stylesheet" href="{{ asset('vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">

  <!-- Helpers -->
  <script src="{{ asset('vuexy/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('vuexy/vendor/js/template-customizer.js') }}"></script>
  <script src="{{ asset('vuexy/js/config.js') }}"></script>


  <!-- App CSS (Vite) -->
  @vite(['resources/css/app.css'])
  
  <!-- Include custom styles -->
  @include('layouts.sections.styles')

  @yield('vendor-style')
  @yield('page-style')

</head>

<body>
  <!-- Layout Content -->
  @yield('layoutContent')
  <!-- / Layout Content -->

  <!-- Core JS -->
    <script src="{{ asset('vuexy/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>

    <script src="{{ asset('vuexy/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('vuexy/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
   
    <script src="{{ asset('vuexy/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('vuexy/js/forms-selects.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('vuexy/js/main.js') }}"></script>
    <script src="{{ asset('vuexy/js/app-chat.js') }}"></script>

  <!-- Include Scripts -->
  @include('layouts.sections.scripts')

  @yield('vendor-script')
  @yield('page-script')

  <!-- Livewire Scripts -->
  @livewireScripts

  <script>
    document.addEventListener('livewire:navigated', () => {
      // Reinitialize Bootstrap dropdowns, tooltips, etc. after Livewire SPA navigation
      document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
        if (!el.classList.contains('dropdown-toggle')) return;
        try { new bootstrap.Dropdown(el); } catch (e) {}
      });
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        try { new bootstrap.Tooltip(el); } catch (e) {}
      });
    });
  </script>

</body>

</html>
