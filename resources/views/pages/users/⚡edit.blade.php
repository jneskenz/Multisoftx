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
}; ?>

<section class="w-full">
    <x-pages::settings.layout :heading="__('Edit user')" :subheading="$user->name">
        <form wire:submit="updateUser">
            <div class="mb-3">
                <label for="edit-user-name" class="form-label">{{ __('Name') }}</label>
                <input type="text" id="edit-user-name" class="form-control" wire:model="name" required />
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="edit-user-email" class="form-label">{{ __('Email') }}</label>
                <input type="email" id="edit-user-email" class="form-control" wire:model="email" required />
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="edit-user-password" class="form-label">{{ __('New password') }} <small class="text-muted">({{ __('leave blank to keep current') }})</small></label>
                <input type="password" id="edit-user-password" class="form-control" wire:model="password" />
                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="edit-user-password-confirmation" class="form-label">{{ __('Confirm new password') }}</label>
                <input type="password" id="edit-user-password-confirmation" class="form-control" wire:model="password_confirmation" />
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" wire:navigate>{{ __('Cancel') }}</a>
            </div>
        </form>

        @if ($user->id !== auth()->id())
            <hr class="my-4" />
            <div>
                <h5 class="mb-1">{{ __('Delete user') }}</h5>
                <p class="text-muted small mb-3">{{ __('Permanently delete this user.') }}</p>
                <div class="alert alert-danger d-flex align-items-center justify-content-between">
                    <div>
                        <p class="fw-medium mb-1">{{ __('Warning') }}</p>
                        <p class="small mb-0">{{ __('This action cannot be undone.') }}</p>
                    </div>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete-user-modal">
                        {{ __('Delete user') }}
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
                                <p class="mb-0">{{ __('Are you sure you want to delete :name?', ['name' => $user->name]) }}</p>
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
