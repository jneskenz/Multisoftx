<?php

use Flux\Flux;
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
        $selected = collect($this->permissions)
            ->filter(fn ($val) => $val)
            ->keys()
            ->toArray();

        $this->role->syncPermissions($selected);

        Flux::toast(variant: 'success', text: __('Permissions updated.'));
    }

    public function deleteRole(): void
    {
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
}; ?>

<section class="w-full">
    <x-pages::settings.layout :heading="__('Edit role')" :subheading="$role->name">
        <form wire:submit="savePermissions">
            <div class="mb-3">
                <label class="form-label fw-medium">{{ __('Role') }}</label>
                <p class="form-control-plaintext">{{ $role->name }}</p>
            </div>

            <h5 class="mb-3">{{ __('Permissions') }}</h5>

            @foreach ($this->groupedPermissions as $group => $perms)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 text-capitalize">{{ $group }}</h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="row">
                            @foreach ($perms as $perm)
                                <div class="col-md-4 col-6 mb-1">
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
                <button type="submit" class="btn btn-primary" @if ($role->name === 'super-admin') disabled @endif>
                    {{ __('Save permissions') }}
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary" wire:navigate>{{ __('Back') }}</a>
            </div>
        </form>

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
    </x-pages::settings.layout>
</section>
