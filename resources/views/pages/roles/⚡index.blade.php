<?php

use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Title('Roles')] class extends Component {
    public string $name = '';

    public function createRole(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique(Role::class)],
        ]);

        Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        $this->reset('name');

        Flux::toast(variant: 'success', text: __('Role created.'));
    }

    public function deleteRole(int $id): void
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'super-admin') {
            Flux::toast(variant: 'danger', text: __('Cannot delete super-admin role.'));

            return;
        }

        $role->delete();

        Flux::toast(variant: 'success', text: __('Role deleted.'));
    }

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->orderBy('name')
            ->get();
    }
}; ?>

<section class="w-full">
    <x-pages::settings.layout :heading="__('Roles')" :subheading="__('Manage roles and permissions')">
        <div class="d-flex align-items-center justify-content-end mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-role-modal">
                <i class="icon-base ti tabler-plus me-1"></i>
                {{ __('New role') }}
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Permissions') }}</th>
                        <th>{{ __('Users') }}</th>
                        <th>{{ __('Guard') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->roles as $role)
                        <tr>
                            <td>
                                <span class="fw-medium">{{ $role->name }}</span>
                                @if ($role->name === 'super-admin')
                                    <span class="badge bg-label-warning ms-1">{{ __('Full access') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $role->permissions->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $role->users->count() }}</span>
                            </td>
                            <td><code>{{ $role->guard_name }}</code></td>
                            <td class="text-end">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-icon btn-text-secondary" wire:navigate title="{{ __('Edit') }}">
                                    <i class="icon-base ti tabler-pencil"></i>
                                </a>
                                @if ($role->name !== 'super-admin')
                                    <button type="button" class="btn btn-sm btn-icon btn-text-danger"
                                        data-bs-toggle="modal" data-bs-target="#delete-role-{{ $role->id }}"
                                        title="{{ __('Delete') }}">
                                        <i class="icon-base ti tabler-trash"></i>
                                    </button>

                                    <div class="modal fade" id="delete-role-{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content">
                                                <form wire:submit="deleteRole({{ $role->id }})">
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
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-pages::settings.layout>

    <div class="modal fade" id="create-role-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form wire:submit="createRole">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Create role') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="role-name" class="form-label">{{ __('Role name') }}</label>
                            <input type="text" id="role-name" class="form-control" wire:model="name" required autofocus />
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
