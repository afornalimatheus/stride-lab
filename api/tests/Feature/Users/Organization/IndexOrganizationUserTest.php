<?php

use App\Enums\RoleEnum;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    Model::unguard();

    foreach (RoleEnum::cases() as $roleEnum) {
        Role::create(['name' => $roleEnum->value]);
    }

    $this->withAuthentication = function (): void {
        $this->authenticatedUser = User::factory()->create();

        $this->organization = Organization::factory()->create([
            'owner_id' => $this->authenticatedUser->id,
        ]);

        $this->organization->members()->create([
            'user_id' => $this->authenticatedUser->id,
            'role_id' => Role::where('name', RoleEnum::OWNER->value)->first()->id,
        ]);

        $token = JWTAuth::fromUser($this->authenticatedUser);
        $this->withToken($token);
    };

    ($this->withAuthentication)();

    $this->route = route('dashboard.users.index');
});

describe('route', function () {
    it('responds with unauthorized when not authenticated', function () {
        $this->flushHeaders();

        $response = getJson($this->route);

        $response->assertUnauthorized();
    });

    it('responds with forbidden when role is not OWNER or MANAGER', function () {
        foreach ([RoleEnum::COACH, RoleEnum::ATHLETE] as $role) {
            $user = User::factory()->create();
            $org = Organization::factory()->create(['owner_id' => $user->id]);
            $org->members()->create([
                'user_id' => $user->id,
                'role_id' => Role::where('name', $role->value)->first()->id,
            ]);
            $this->withToken(JWTAuth::fromUser($user));

            $response = getJson($this->route);

            $response->assertForbidden();
        }
    });
});

describe('action', function () {
    it('returns a paginated list of users from the same organization', function () {
        $memberUser = User::factory()->create();
        $this->organization->members()->create([
            'user_id' => $memberUser->id,
            'role_id' => Role::where('name', RoleEnum::COACH->value)->first()->id,
        ]);

        $response = getJson($this->route);

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('does not return users from other organizations', function () {
        $otherOrg = Organization::factory()->create(['owner_id' => User::factory()->create()->id]);
        $otherUser = User::factory()->create();
        $otherOrg->members()->create([
            'user_id' => $otherUser->id,
            'role_id' => Role::where('name', RoleEnum::COACH->value)->first()->id,
        ]);

        $response = getJson($this->route);

        $response->assertOk();

        $returnedIds = collect($response->json('data'))->pluck('id');
        expect($returnedIds->contains($otherUser->id))->toBeFalse();
    });

    it('filters users by name', function () {
        $memberUser = User::factory()->create(['name' => 'Alice Wonder']);
        $this->organization->members()->create([
            'user_id' => $memberUser->id,
            'role_id' => Role::where('name', RoleEnum::COACH->value)->first()->id,
        ]);

        $response = getJson($this->route . '?filter[name]=Alice');

        $response->assertOk();

        $names = collect($response->json('data'))->pluck('name');
        expect($names->every(fn ($n) => str_contains($n, 'Alice')))->toBeTrue();
    });

    it('filters users by role', function () {
        $coachUser = User::factory()->create();
        $this->organization->members()->create([
            'user_id' => $coachUser->id,
            'role_id' => Role::where('name', RoleEnum::COACH->value)->first()->id,
        ]);

        $response = getJson($this->route . '?filter[role]=' . RoleEnum::COACH->value);

        $response->assertOk();
        expect($response->json('data'))->not->toBeEmpty();
    });

    it('filters active users when active=1', function () {
        $response = getJson($this->route . '?filter[active]=1');

        $response->assertOk();
        expect(collect($response->json('data'))->every(fn ($u) => $u['active'] === true))->toBeTrue();
    });
});
