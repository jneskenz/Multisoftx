<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public User $user;

    public string $name = '';
    public string $email = '';
    public ?string $password = null;
    public ?string $password_confirmation = null;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateUser(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
        ];

        if ($this->password) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $this->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        $this->user->update($data);

        Flux::toast(variant: 'success', text: __('User updated.'));
    }

    public function deleteUser(): void
    {
        if ($this->user->id === auth()->id()) {
            Flux::toast(variant: 'danger', text: __('You cannot delete yourself.'));

            return;
        }

        $this->user->delete();

        Flux::toast(variant: 'success', text: __('User deleted.'));

        $this->redirectRoute('users.index', navigate: true);
    }

    public function render()
    {
        return $this->view()->title(__('Edit :name', ['name' => $this->user->name]));
    }

    #[Computed]
    public function breadcrumbs(): array
    {
        $items = [['name' => __('Usuarios')], ['name' => __('Editar')]];

        return [
            'title' => 'Gestión de Usuarios',
            'description' => 'Gestión Administrativo de Usuarios',
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
            {{-- // accesoateams --}}
            <x-card-header title="{{ __('Registros de usuarios') }}"
                description="{{ __('Empleados con acceso a') }} {{ config('app.name') }}" textColor="text-plus"
                icon="ti tabler-users" iconColor="bg-label-info">
                <button class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                    title="{{ __('Nuevo Empleado') }}" data-bs-toggle="modal" data-bs-target="#create-user-modal">
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nuevo Empleado') }}</span>
                </button>
            </x-card-header>

            <div class="card-body">
                <form wire:submit="updateUser">
                    <div class="mb-3">
                        <label for="edit-user-name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" id="edit-user-name" class="form-control" wire:model="name" required />
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit-user-email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" id="edit-user-email" class="form-control" wire:model="email" required />
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit-user-password" class="form-label">{{ __('New password') }} <small
                                class="text-muted">({{ __('leave blank to keep current') }})</small></label>
                        <input type="password" id="edit-user-password" class="form-control" wire:model="password" />
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit-user-password-confirmation"
                            class="form-label">{{ __('Confirm new password') }}</label>
                        <input type="password" id="edit-user-password-confirmation" class="form-control"
                            wire:model="password_confirmation" />
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"
                            wire:navigate>{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                @if ($user->id !== auth()->id())
                    <hr class="my-4" />
                    <div>
                        <div class="alert alert-danger d-flex align-items-center justify-content-between">
                            <div>
                                <p class="fw-medium mb-1">{{ __('¡Advertencia!') }} {{ __('Se eliminará el usuario permanentemente')  }}</p>
                                <p class="small mb-0">{{ __('This action cannot be undone.') }}</p>
                            </div>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#delete-user-modal">
                                {{ __('Eliminar usuario') }}
                            </button>
                        </div>
                    </div>

                    <div class="modal fade" id="delete-user-modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content">
                                <form wire:submit="deleteUser">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Delete user') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <p class="mb-0">
                                            {{ __('Are you sure you want to delete :name?', ['name' => $user->name]) }}
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
