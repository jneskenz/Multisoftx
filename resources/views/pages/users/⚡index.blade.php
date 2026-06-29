<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Users')] class extends Component {
    use WithPagination;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function createUser(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $this->reset('name', 'email', 'password', 'password_confirmation');

        Flux::toast(variant: 'success', text: __('User created.'));

        $this->redirectRoute('users.edit', $user, navigate: true);
    }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            Flux::toast(variant: 'danger', text: __('You cannot delete yourself.'));

            return;
        }

        $user->delete();

        Flux::toast(variant: 'success', text: __('User deleted.'));
    }

    #[Computed]
    public function users()
    {
        return User::query()->orderBy('name')->paginate(10);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}; ?>

<section class="w-full">
    <div class="col-md-12 col-12">
        <div class="card">
            {{-- // accesoateams --}}
            <x-card-header title="{{ __('Registros de usuarios') }}" description="{{ __('Empleados con acceso a') }} {{ config('app.name') }}"
                textColor="text-plus" icon="ti tabler-users" iconColor="bg-label-info">
                <button class="btn btn-primary btn-md btn-md-normal px-1 px-md-3 waves-effect d-flex align-items-center"
                    title="{{ __('Nuevo Empleado') }}"
                    data-bs-toggle="modal"
                    data-bs-target="#create-user-modal">
                    <i class="ti tabler-plus me-md-1"></i>
                    <span class="d-none d-md-inline ms-1">{{ __('Nuevo Empleado') }}</span>
                </button>
            </x-card-header>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Registered') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-label-primary"
                                                style="width:34px;height:34px;font-weight:600;font-size:.8rem;color:#7367f0;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <small class="text-muted">{{ $user->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('users.edit', $user) }}"
                                            class="btn btn-sm btn-icon btn-text-secondary" wire:navigate
                                            title="{{ __('Edit') }}">
                                            <i class="icon-base ti tabler-pencil"></i>
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <button type="button" class="btn btn-sm btn-icon btn-text-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#delete-user-{{ $user->id }}"
                                                title="{{ __('Delete') }}">
                                                <i class="icon-base ti tabler-trash"></i>
                                            </button>

                                            <div class="modal fade" id="delete-user-{{ $user->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                                    <div class="modal-content">
                                                        <form wire:submit="deleteUser({{ $user->id }})">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('Delete user') }}</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <p class="mb-0">
                                                                    {{ __('Are you sure you want to delete :name?', ['name' => $user->name]) }}
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-outline-secondary"
                                                                    data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger">{{ __('Delete') }}</button>
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

                <div class="mt-3">
                    {{ $this->users->links() }}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="create-user-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="createUser">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Create user') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user-name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" id="user-name" class="form-control" wire:model="name" required
                                autofocus />
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="user-email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" id="user-email" class="form-control" wire:model="email" required />
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="user-password" class="form-label">{{ __('Password') }}</label>
                            <input type="password" id="user-password" class="form-control" wire:model="password"
                                required />
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="user-password-confirmation"
                                class="form-label">{{ __('Confirm password') }}</label>
                            <input type="password" id="user-password-confirmation" class="form-control"
                                wire:model="password_confirmation" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Create user') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
