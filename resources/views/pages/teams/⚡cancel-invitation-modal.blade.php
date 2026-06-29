<?php

use App\Models\Team;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

new class extends Component {
    public Team $team;

    public string $invitationCode = '';

    public string $invitationEmail = '';

    public string $modalName = 'cancel-invitation';

    public function mount(
        Team $team,
        ?string $invitationCode = null,
        ?string $invitationEmail = null,
        ?string $modalName = null,
    ): void
    {
        $this->team = $team;
        $this->invitationCode = $invitationCode ?? '';
        $this->invitationEmail = $invitationEmail ?? '';
        $this->modalName = $modalName ?? ($invitationCode ? "cancel-invitation-{$invitationCode}" : 'cancel-invitation');
    }

    public function cancelInvitation(): void
    {
        $invitation = $this->team->invitations()->where('code', $this->invitationCode)->firstOrFail();

        if ($this->invitationEmail === '') {
            $this->invitationEmail = $invitation->email;
        }

        Gate::authorize('cancelInvitation', $this->team);

        $invitation->delete();

        Flux::toast(variant: 'success', text: __('Invitation cancelled.'));

        $this->redirectRoute('teams.edit', ['team' => $this->team->slug], navigate: true);
    }
}; ?>

<div class="modal fade" id="{{ $modalName }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form wire:submit="cancelInvitation">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Cancel invitation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">{{ __('Are you sure you want to cancel the invitation for :email?', ['email' => $invitationEmail]) }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Keep invitation') }}</button>
                    <button type="submit" class="btn btn-danger" data-test="cancel-invitation-confirm">{{ __('Cancel invitation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
