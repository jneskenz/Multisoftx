<?php

use App\Actions\Teams\CreateTeam;
use App\Data\UserTeam;
use App\Models\Team;
use App\Rules\TeamName;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Teams')] class extends Component {
    public string $name = '';

    public function createTeam(CreateTeam $createTeam): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', new TeamName],
        ]);

        $team = $createTeam->handle(Auth::user(), $validated['name']);

        $this->reset('name');

        Flux::toast(variant: 'success', text: __('Team created.'));

        $this->redirectRoute('teams.edit', ['team' => $team->slug], navigate: true);
    }

    public function leaveTeam(int $teamId): void
    {
        $team = Team::findOrFail($teamId);
        $user = Auth::user();

        Gate::authorize('leave', $team);

        $fallbackTeam = $user->isCurrentTeam($team)
            ? $user->fallbackTeam($team)
            : null;

        $team->memberships()
            ->where('user_id', $user->id)
            ->delete();

        if ($fallbackTeam) {
            $user->switchTeam($fallbackTeam);
        }

        Flux::toast(variant: 'success', text: __('You left the team ":name"', ['name' => $team->name]));

        $this->redirectRoute('teams.index', navigate: true);
    }

    /**
     * @return Collection<int, UserTeam>
     */
    #[Computed]
    public function teams(): Collection
    {
        return Auth::user()->toUserTeams(includeCurrent: true);
    }
}; ?>

<section class="w-full">

    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Teams')" :subheading="__('Manage your teams and team memberships')">
        <div class="d-flex align-items-center justify-content-end mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-team-modal">
                <i class="icon-base ti tabler-plus me-1"></i>
                {{ __('New team') }}
            </button>
        </div>

        <div class="list-group list-group-flush">
            @forelse ($this->teams as $team)
                <div class="list-group-item d-flex align-items-center justify-content-between px-0" data-test="team-row">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-medium">{{ $team->name }}</span>
                                @if ($team->isPersonal)
                                    <span class="badge bg-label-secondary">{{ __('Personal') }}</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $team->roleLabel }}</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        @if (! $team->isPersonal && $team->role !== 'owner')
                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary"
                                data-bs-toggle="modal" data-bs-target="#leave-team-{{ $team->id }}"
                                data-test="team-leave-button"
                                title="{{ __('Leave team') }}">
                                <i class="icon-base ti tabler-arrow-right-start-on-rectangle"></i>
                            </button>
                        @endif

                        <a href="{{ route('teams.edit', $team->slug) }}"
                           class="btn btn-sm btn-icon btn-text-secondary"
                           wire:navigate
                           data-bs-toggle="tooltip"
                           title="{{ $team->role === 'member' ? __('View team') : __('Edit team') }}">
                            <i class="icon-base ti tabler-{{ $team->role === 'member' ? 'eye' : 'pencil' }}"></i>
                        </a>
                    </div>
                </div>

                @if (! $team->isPersonal && $team->role !== 'owner')
                    <div class="modal fade" id="leave-team-{{ $team->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form wire:submit="leaveTeam({{ $team->id }})">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Leave team') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-0">{{ __('Are you sure you want to leave :name?', ['name' => $team->name]) }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit" class="btn btn-danger" data-test="leave-team-confirm">{{ __('Leave team') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center py-8">
                    <p class="text-muted mb-0">{{ __("You don't belong to any teams yet.") }}</p>
                </div>
            @endforelse
        </div>
    </x-pages::settings.layout>

    <div class="modal fade" id="create-team-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="createTeam">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Create a new team') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create-team-name" class="form-label">{{ __('Team name') }}</label>
                            <input type="text" id="create-team-name" class="form-control" wire:model="name" required autofocus data-test="create-team-name" />
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary" data-test="create-team-submit">{{ __('Create team') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</section>
