<?php

use App\Data\TeamPermissions;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Rules\TeamName;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Team $teamModel;

    public string $teamName = '';

    public array $teamData = [];

    public array $members = [];

    public array $invitations = [];

    public array $availableRoles = [];

    public bool $isCurrentTeam = false;

    public function mount(Team $team): void
    {
        $this->teamModel = $team;
        $this->teamName = $team->name;

        $this->populateTeamData();
    }

    public function updateTeam(): void
    {
        Gate::authorize('update', $this->teamModel);

        $validated = $this->validate([
            'teamName' => ['required', 'string', 'max:255', new TeamName],
        ]);

        $team = DB::transaction(function () use ($validated) {
            $team = Team::whereKey($this->teamModel->id)->lockForUpdate()->firstOrFail();

            $team->update(['name' => $validated['teamName']]);

            return $team;
        });

        $this->teamModel = $team;

        $this->populateTeamData();

        Flux::toast(variant: 'success', text: __('Team updated.'));

        $this->redirectRoute('teams.edit', ['team' => $this->teamModel->fresh()->slug], navigate: true);
    }

    public function updateMember(int $userId, string $role): void
    {
        Gate::authorize('updateMember', $this->teamModel);

        $validated = Validator::make(['role' => $role], [
            'role' => ['required', 'string', Rule::enum(TeamRole::class)],
        ])->validate();

        $this->teamModel->memberships()
            ->where('user_id', $userId)
            ->firstOrFail()
            ->update(['role' => TeamRole::from($validated['role'])]);

        $this->populateTeamData();

        Flux::toast(variant: 'success', text: __('Member role updated.'));
    }

    private function populateTeamData(): void
    {
        $user = Auth::user();

        $team = $this->teamModel->fresh();

        $this->teamData = [
            'id' => $team->id,
            'name' => $team->name,
            'slug' => $team->slug,
            'is_personal' => $team->is_personal,
        ];

        $this->members = $team->members()->get()->map(fn ($member) => [
            'id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'avatar' => $member->avatar ?? null,
            'role' => $member->pivot->role->value,
            'role_label' => $member->pivot->role->label(),
        ])->toArray();

        $this->invitations = $team->invitations()
            ->whereNull('accepted_at')
            ->get()
            ->map(fn ($invitation) => [
                'code' => $invitation->code,
                'email' => $invitation->email,
                'role' => $invitation->role->value,
                'role_label' => $invitation->role->label(),
                'created_at' => $invitation->created_at->toISOString(),
            ])->toArray();

        $this->availableRoles = TeamRole::assignable();

        $this->isCurrentTeam = $user->isCurrentTeam($team);
    }

    public function render()
    {
        $teamName = $this->teamData['name'] ?? $this->teamModel->name;

        $title = $this->permissions->canUpdateTeam
            ? __('Edit :name', ['name' => $teamName])
            : __('View :name', ['name' => $teamName]);

        return $this->view()->title($title);
    }

    #[Computed]
    public function permissions(): TeamPermissions
    {
        return Auth::user()->toTeamPermissions($this->teamModel);
    }
}; ?>

<section class="w-full">
    <x-pages::settings.layout :heading="__('Teams')" :subheading="__('Manage your team settings')">
        <div class="space-y-6">
            @if ($this->permissions->canUpdateTeam)
                <div class="mb-4">
                    <form wire:submit="updateTeam">
                        <div class="mb-3">
                            <label for="team-name-input" class="form-label">{{ __('Team name') }}</label>
                            <input type="text" id="team-name-input" class="form-control" wire:model="teamName" required data-test="team-name-input" />
                            @error('teamName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary" data-test="team-save-button">{{ __('Save') }}</button>
                    </form>
                </div>
            @else
                <h6 class="mb-4">{{ $teamData['name'] }}</h6>
            @endif

            <hr class="my-4" />

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="mb-1">{{ __('Team members') }}</h5>
                    @if ($this->permissions->canAddMember || $this->permissions->canUpdateMember || $this->permissions->canRemoveMember)
                        <p class="text-muted small mb-0">{{ __('Manage who belongs to this team') }}</p>
                    @endif
                </div>

                @if ($this->permissions->canCreateInvitation)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#invite-member-modal">
                        <i class="icon-base ti tabler-user-plus me-1"></i>
                        {{ __('Invite member') }}
                    </button>
                @endif
            </div>

            <div class="list-group list-group-flush">
                @foreach ($members as $member)
                    <div class="list-group-item d-flex align-items-center justify-content-between px-0" data-test="member-row">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-label-primary"
                                 style="width:38px;height:38px;font-weight:600;font-size:.85rem;color:#7367f0;">
                                {{ strtoupper(substr($member['name'], 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-medium">{{ $member['name'] }}</div>
                                <small class="text-muted">{{ $member['email'] }}</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if ($member['role'] !== 'owner' && $this->permissions->canUpdateMember)
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-test="member-role-trigger">
                                        {{ $member['role_label'] }}
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach ($availableRoles as $role)
                                            <li>
                                                <button type="button" class="dropdown-item" wire:click="updateMember({{ $member['id'] }}, '{{ $role['value'] }}')" data-test="member-role-option">
                                                    {{ $role['label'] }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <span class="badge bg-label-secondary">{{ $member['role_label'] }}</span>
                            @endif

                            @if ($member['role'] !== 'owner' && $this->permissions->canRemoveMember)
                                <button type="button" class="btn btn-sm btn-icon btn-text-secondary"
                                    data-bs-toggle="modal" data-bs-target="#remove-member-{{ $member['id'] }}"
                                    data-bs-toggle="tooltip" title="{{ __('Remove member') }}">
                                    <i class="icon-base ti tabler-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($member['role'] !== 'owner' && $this->permissions->canRemoveMember)
                        <livewire:pages::teams.remove-member-modal
                            :team="$teamModel"
                            :member-id="$member['id']"
                            :member-name="$member['name']"
                            :modal-name="'remove-member-'.$member['id']"
                            :key="'remove-member-modal-'.$member['id']"
                        />
                    @endif
                @endforeach
            </div>

            <hr class="my-4" />

            @if (count($invitations) > 0)
                <div class="mb-4">
                    <h5 class="mb-1">{{ __('Pending invitations') }}</h5>
                    <p class="text-muted small mb-3">{{ __('Invitations that have not been accepted yet') }}</p>

                    <div class="list-group list-group-flush">
                        @foreach ($invitations as $invitation)
                            <div class="list-group-item d-flex align-items-center justify-content-between px-0" data-test="invitation-row">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-label-secondary"
                                         style="width:38px;height:38px;">
                                        <i class="icon-base ti tabler-mail text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $invitation['email'] }}</div>
                                        <small class="text-muted">{{ $invitation['role_label'] }}</small>
                                    </div>
                                </div>

                                @if ($this->permissions->canCancelInvitation)
                                    <button type="button" class="btn btn-sm btn-icon btn-text-secondary"
                                        data-bs-toggle="modal" data-bs-target="#cancel-invitation-{{ $invitation['code'] }}"
                                        data-bs-toggle="tooltip" title="{{ __('Cancel invitation') }}">
                                        <i class="icon-base ti tabler-x"></i>
                                    </button>
                                @endif
                            </div>
                            @if ($this->permissions->canCancelInvitation)
                                <livewire:pages::teams.cancel-invitation-modal
                                    :team="$teamModel"
                                    :invitation-code="$invitation['code']"
                                    :invitation-email="$invitation['email']"
                                    :modal-name="'cancel-invitation-'.$invitation['code']"
                                    :key="'cancel-invitation-modal-'.$invitation['code']"
                                />
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($this->permissions->canDeleteTeam && ! $teamData['is_personal'])
                <hr class="my-4" />
                <div>
                    <h5 class="mb-1">{{ __('Delete team') }}</h5>
                    <p class="text-muted small mb-3">{{ __('Permanently delete your team') }}</p>

                    <div class="alert alert-danger d-flex align-items-center justify-content-between">
                        <div>
                            <p class="fw-medium mb-1">{{ __('Warning') }}</p>
                            <p class="small mb-0">{{ __('Please proceed with caution, this cannot be undone.') }}</p>
                        </div>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete-team-modal" data-test="delete-team-button">
                            {{ __('Delete team') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </x-pages::settings.layout>

    @if ($this->permissions->canCreateInvitation)
        <livewire:pages::teams.invite-member-modal :team="$teamModel" />
    @endif

    @if ($this->permissions->canDeleteTeam && ! $teamData['is_personal'])
        <livewire:pages::teams.delete-team-modal :team="$teamModel" />
    @endif
</section>
