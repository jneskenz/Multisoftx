<?php

use App\Data\UserTeam;
use App\Models\Team;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public Team $team;

    public string $deleteName = '';

    public function mount(Team $team): void
    {
        $this->team = $team;
    }

    #[Computed]
    public function deleteConfirmLabel(): string
    {
        return __('Type ":name" to confirm', ['name' => $this->team->name]);
    }

    public function deleteTeam(): void
    {
        Gate::authorize('delete', $this->team);

        $validated = $this->validate([
            'deleteName' => ['required', 'string'],
        ]);

        if ($validated['deleteName'] !== $this->team->name) {
            $this->addError('deleteName', __('The team name does not match.'));

            return;
        }

        $user = Auth::user();

        $fallbackTeam = $user->isCurrentTeam($this->team)
            ? $user->fallbackTeam($this->team)
            : null;

        DB::transaction(function () use ($user) {
            User::where('current_team_id', $this->team->id)
                ->where('id', '!=', $user->id)
                ->each(fn (User $affectedUser) => $affectedUser->switchTeam($affectedUser->personalTeam()));

            $this->team->invitations()->delete();
            $this->team->memberships()->delete();
            $this->team->delete();
        });

        if ($fallbackTeam) {
            $user->switchTeam($fallbackTeam);
        }

        Flux::toast(variant: 'success', text: __('Team deleted.'));

        $this->redirectRoute('teams.index', navigate: true);
    }

    /**
     * @return Collection<int, UserTeam>
     */
    #[Computed]
    public function otherTeams(): Collection
    {
        return Auth::user()->toUserTeams();
    }
}; ?>

<div class="modal fade" id="delete-team-modal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form wire:submit="deleteTeam">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Are you sure?') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-3">
                        <p class="mb-0">{{ __('This action cannot be undone. This will permanently delete the team ":name".', ['name' => $team->name]) }}</p>
                    </div>
                    <div class="mb-3">
                        <label for="delete-team-name" class="form-label">{{ $this->deleteConfirmLabel }}</label>
                        <input type="text" id="delete-team-name" class="form-control" wire:model="deleteName" required data-test="delete-team-name" />
                        @error('deleteName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger" data-test="delete-team-confirm">{{ __('Delete team') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
