<?php

use App\Data\UserTeam;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public function currentTeam(): ?array
    {
        $team = Auth::user()->currentTeam;

        return $team ? [
            'id' => $team->id,
            'name' => $team->name,
            'slug' => $team->slug,
        ] : null;
    }

    /**
     * @return Collection<int, UserTeam>
     */
    public function teams(): Collection
    {
        return Auth::user()->toUserTeams(includeCurrent: true);
    }

    public function switchTeam(string $slug): void
    {
        $user = Auth::user();

        abort_unless(
            $user->belongsToTeam($team = Team::where('slug', $slug)->firstOrFail()),
            403
        );

        $currentTeamSlug = $user->currentTeam?->slug;

        $user->switchTeam($team);

        if (! request()->header('Referer')) {
            $this->redirectRoute('dashboard', ['current_team' => $team->slug], navigate: false);

            return;
        }

        if (! $currentTeamSlug) {
            $this->redirect(request()->header('Referer'), navigate: false);

            return;
        }

        $redirectTo = $this->replaceCurrentTeamInReferer(
            request()->header('Referer'),
            $currentTeamSlug,
            $team->slug,
        );

        $this->redirect($redirectTo ?? request()->header('Referer'), navigate: false);
    }

    protected function replaceCurrentTeamInReferer(string $referer, string $currentTeamSlug, string $newTeamSlug): ?string
    {
        $redirectTo = preg_replace(
            '#/'.preg_quote($currentTeamSlug, '#').'(?=/|\?|$)#',
            '/'.$newTeamSlug,
            $referer,
            1,
        );

        return preg_replace(
            '#([?&]current_team=)'.preg_quote($currentTeamSlug, '#').'(?=&|$)#',
            '$1'.$newTeamSlug,
            $redirectTo ?? $referer,
            1,
        );
    }
}; ?>

<div x-data="{ open: false }" class="dropdown d-inline-block" style="min-width:200px;">
    <button @click="open = !open" @click.away="open = false" class="btn btn-outline-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-between" type="button" data-test="team-switcher-trigger">
        <span class="text-truncate">{{ $this->currentTeam()['name'] ?? __('Select team') }}</span>
    </button>
    <ul x-show="open" x-transition class="dropdown-menu w-100 show" style="position:absolute;inset:0 auto auto 0;margin:0;"
        @click="open = false">
        @foreach ($this->teams() as $team)
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center justify-content-between"
                    wire:click="switchTeam('{{ $team->slug }}')"
                    data-test="team-switcher-item">
                    <span>{{ $team->name }}</span>
                    @if ($team->isCurrent)
                        <i class="icon-base ti tabler-check text-primary"></i>
                    @endif
                </button>
            </li>
        @endforeach
        @if (Route::has('teams.index'))
            <li><hr class="dropdown-divider"></li>
            <li>
                <a href="{{ route('teams.index') }}" class="dropdown-item" wire:navigate>
                    <i class="icon-base ti tabler-plus me-2"></i>
                    {{ __('Manage teams') }}
                </a>
            </li>
        @endif
    </ul>
</div>
