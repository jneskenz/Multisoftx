<?php

use App\Models\Team;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

new class extends Component {
    public Team $team;

    public ?int $memberId = null;

    public string $memberName = '';

    public string $modalName = 'remove-member';

    public function mount(
        Team $team,
        ?int $memberId = null,
        ?string $memberName = null,
        ?string $modalName = null,
    ): void
    {
        $this->team = $team;
        $this->memberId = $memberId;
        $this->memberName = $memberName ?? '';
        $this->modalName = $modalName ?? ($memberId ? "remove-member-{$memberId}" : 'remove-member');
    }

    public function removeMember(): void
    {
        Gate::authorize('removeMember', $this->team);

        $user = User::findOrFail($this->memberId);

        if ($this->memberName === '') {
            $this->memberName = $user->name;
        }

        $this->team->memberships()
            ->where('user_id', $user->id)
            ->delete();

        if ($user->isCurrentTeam($this->team)) {
            $user->switchTeam($user->personalTeam());
        }

        Flux::toast(variant: 'success', text: __('Member removed.'));

        $this->redirectRoute('teams.edit', ['team' => $this->team->slug], navigate: true);
    }
}; ?>

<div class="modal fade" id="{{ $modalName }}" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form wire:submit="removeMember">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Remove team member') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">{{ __('Are you sure you want to remove :name from this team?', ['name' => $memberName]) }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger" data-test="remove-member-confirm">{{ __('Remove member') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
