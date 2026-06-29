<?php

use App\Models\TeamInvitation;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public bool $showPendingInvitationsModal = true;

    public function mount(): void
    {
        if (session()->pull('team-invitation-accepted')) {
            Flux::toast(variant: 'success', text: __('Invitation accepted.'));
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{code: string, inviter_name: string, team_name: string}>
     */
    #[Computed]
    public function pendingInvitations(): \Illuminate\Support\Collection
    {
        $email = Str::lower(Auth::user()->email);

        return TeamInvitation::query()
            ->with(['inviter', 'team'])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(fn (TeamInvitation $invitation) => [
                'code' => $invitation->code,
                'inviter_name' => $invitation->inviter->name,
                'team_name' => $invitation->team->name,
            ]);
    }

    public function acceptInvitation(string $code): void
    {
        $invitation = $this->findPendingInvitation($code);

        $this->redirectRoute('invitations.accept', ['invitation' => $invitation->code], navigate: true);
    }

    public function declineInvitation(string $code): void
    {
        $invitation = $this->findPendingInvitation($code);

        $invitation->delete();

        Flux::toast(variant: 'success', text: __('Invitation declined.'));
    }

    private function findPendingInvitation(string $code): TeamInvitation
    {
        $invitation = TeamInvitation::query()
            ->where('code', $code)
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            throw ValidationException::withMessages([
                'invitation' => [__('This invitation has expired.')],
            ]);
        }

        if (Str::lower($invitation->email) !== Str::lower(Auth::user()->email)) {
            throw ValidationException::withMessages([
                'invitation' => [__('This invitation was sent to a different email address.')],
            ]);
        }

        return $invitation;
    }
}; ?>

<div>
    @if ($this->pendingInvitations->isNotEmpty())
        <div class="modal fade" id="pending-invitations-modal" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" data-test="pending-invitations-modal">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Pending team invitations') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">{{ __('Accept or decline the teams you have been invited to join.') }}</p>

                        <div class="d-grid gap-3">
                            @foreach ($this->pendingInvitations as $invitation)
                                <div class="border rounded p-3" data-test="pending-invitation-row">
                                    <div class="mb-2">
                                        <p class="fw-medium mb-0">{{ $invitation['team_name'] }}</p>
                                        <small class="text-muted">
                                            {{ __(':inviter invited you to join this team.', ['inviter' => $invitation['inviter_name']]) }}
                                        </small>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            wire:click="declineInvitation('{{ $invitation['code'] }}')"
                                            wire:loading.attr="disabled"
                                            data-test="pending-invitation-decline">
                                            {{ __('Decline') }}
                                        </button>

                                        <button type="button" class="btn btn-primary btn-sm"
                                            wire:click="acceptInvitation('{{ $invitation['code'] }}')"
                                            wire:loading.attr="disabled"
                                            data-test="pending-invitation-accept">
                                            {{ __('Accept') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('livewire:init', () => {
                let modal = new bootstrap.Modal('#pending-invitations-modal');
                modal.show();
            });
        </script>
    @endif
</div>
