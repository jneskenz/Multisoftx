<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Notifications\Teams\TeamInvitation as TeamInvitationNotification;
use App\Rules\UniqueTeamInvitation;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public Team $team;

    public string $inviteEmail = '';

    public string $inviteRole = 'member';

    public function mount(Team $team): void
    {
        $this->team = $team;
    }

    public function createInvitation(): void
    {
        Gate::authorize('inviteMember', $this->team);

        $validated = $this->validate([
            'inviteEmail' => ['required', 'string', 'email', 'max:255', new UniqueTeamInvitation($this->team)],
            'inviteRole' => ['required', 'string', Rule::enum(TeamRole::class)],
        ]);

        $invitation = $this->team->invitations()->create([
            'email' => $validated['inviteEmail'],
            'role' => TeamRole::from($validated['inviteRole']),
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays(3),
        ]);

        Notification::route('mail', $invitation->email)
            ->notify(new TeamInvitationNotification($invitation));

        $this->reset('inviteEmail', 'inviteRole');

        Flux::toast(variant: 'success', text: __('Invitation sent.'));

        $this->redirectRoute('teams.edit', ['team' => $this->team->slug], navigate: true);
    }

    #[Computed]
    public function availableRoles(): array
    {
        return TeamRole::assignable();
    }
}; ?>

<div class="modal fade" id="invite-member-modal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form wire:submit="createInvitation">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Invite a team member') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="invite-email" class="form-label">{{ __('Email address') }}</label>
                        <input type="email" id="invite-email" class="form-control" wire:model="inviteEmail" required data-test="invite-email" />
                        @error('inviteEmail') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="invite-role" class="form-label">{{ __('Role') }}</label>
                        <select id="invite-role" class="form-select" wire:model="inviteRole" data-test="invite-role">
                            @foreach ($this->availableRoles as $role)
                                <option value="{{ $role['value'] }}">{{ $role['label'] }}</option>
                            @endforeach
                        </select>
                        @error('inviteRole') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" data-test="invite-submit">{{ __('Send invitation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
