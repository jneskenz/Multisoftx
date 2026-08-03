<?php

use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

new class extends Component {
    public Role $role;

    /** @var array<string, bool> */
    public array $permissions = [];

    public function mount(Role $role): void
    {
        $this->role = $role;

        foreach (Permission::all() as $perm) {
            $this->permissions[$perm->name] = $role->hasPermissionTo($perm);
        }
    }

    public function savePermissions(): void
    {
        Gate::authorize('roles.edit');

        $selected = collect($this->permissions)
            ->filter(fn ($val) => $val)
            ->keys()
            ->toArray();

        $this->role->syncPermissions($selected);

        Flux::toast(variant: 'success', text: __('Permissions updated.'));
    }

    public function deleteRole(): void
    {
        Gate::authorize('roles.delete');

        if ($this->role->name === 'super-admin') {
            Flux::toast(variant: 'danger', text: __('Cannot delete super-admin role.'));

            return;
        }

        $this->role->delete();

        Flux::toast(variant: 'success', text: __('Role deleted.'));

        $this->redirectRoute('roles.index', navigate: true);
    }

    #[Computed]
    public function groupedPermissions(): array
    {
        $groups = [];

        foreach (Permission::all()->sortBy('name') as $perm) {
            $group = explode('.', $perm->name)[0];
            $groups[$group][] = $perm;
        }

        return $groups;
    }

    public function render()
    {
        return $this->view()->title(__('Edit :name', ['name' => $this->role->name]));
    }

    #[Computed]
    public function breadcrumbs(): array
    {
        $items = [
            ['name' => __('Roles y Permisos')],
        ];

        return [
            'title' => 'Editar Roles y Permisos',
            'description' => 'Gestión Administrativo de Roles y Permisos',
            'icon' => 'ti tabler-users',
            'items' => $items,
        ];
    }
    
}; ?>

<section class="w-full">

    <x-breadcrumbs :items="$this->breadcrumbs">
        {{-- <x-slot:extra>
            <div class="d-flex align-items-center gap-2">
                
                <span class="badge bg-label-info">
                    <i class="ti tabler-users"></i>
                </span>
            </div>
        </x-slot:extra> --}}
    </x-breadcrumbs>

    <div class="col-md-12 col-12">
        <div class="card">
            <x-card-header title="{{ __('Registros de usuarios') }}" description="{{ __('Empleados con acceso a') }} {{ config('app.name') }}"
                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                {{-- <button class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                    title="{{ __('Nuevo Rol') }}"
                    data-bs-toggle="modal"
                    data-bs-target="#create-role-modal">
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nuevo Empleado') }}</span>
                </button> --}}
            </x-card-header>

            <div class="card-body mt-5">    
                <form wire:submit="savePermissions">

                    @foreach ($this->groupedPermissions as $group => $perms)
                        <div class="card shadow-none border mb-3">
                            <div class="card-header py-2">
                                <h6 class="mb-0 text-capitalize">{{ $group }}</h6>
                            </div>
                            <div class="card-body py-2">
                                <div class="row">
                                    @foreach ($perms as $perm)
                                        <div class="col-md-2 col-6 mb-1">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="perm-{{ $perm->id }}"
                                                    wire:model="permissions.{{ $perm->name }}"
                                                    @if ($role->name === 'super-admin') disabled @endif />
                                                <label class="form-check-label small" for="perm-{{ $perm->id }}">
                                                    {{ ucfirst(str_replace('-', ' ', explode('.', $perm->name)[1] ?? $perm->name)) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary" @if ($role->name === 'super-admin' || ! auth()->user()->can('roles.edit')) disabled @endif>
                            {{ __('Save permissions') }}
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary" wire:navigate>{{ __('Back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

        @can('roles.delete')
        @if ($role->name !== 'super-admin')
            <hr class="my-4" />
            <div>
                <h5 class="mb-1">{{ __('Delete role') }}</h5>
                <p class="text-muted small mb-3">{{ __('Permanently delete this role.') }}</p>
                <div class="alert alert-danger d-flex align-items-center justify-content-between">
                    <div>
                        <p class="fw-medium mb-1">{{ __('Warning') }}</p>
                        <p class="small mb-0">{{ __('This action cannot be undone.') }}</p>
                    </div>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete-role-modal">
                        {{ __('Delete role') }}
                    </button>
                </div>
            </div>

            <div class="modal fade" id="delete-role-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <form wire:submit="deleteRole">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('Delete role') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-start">
                                <p class="mb-0">{{ __('Are you sure you want to delete :name?', ['name' => $role->name]) }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        @endcan
</section>