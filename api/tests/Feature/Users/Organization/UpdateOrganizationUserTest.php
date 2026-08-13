<?php

use App\Enums\RoleEnum;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patchJson;

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

    $this->targetUser = User::factory()->create();

    $this->organization->members()->create([
        'user_id' => $this->targetUser->id,
        'role_id' => Role::where('name', RoleEnum::COACH->value)->first()->id,
    ]);

    $this->route = fn (string $userId) => route('dashboard.users.update', $userId);
});

describe('route', function () {
    it('responds with unauthorized when not authenticated', function () {
        $this->flushHeaders();

        $response = patchJson(($this->route)($this->targetUser->id), [
            'name' => '__updated_name__',
        ]);

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

            $response = patchJson(($this->route)($this->targetUser->id), [
                'name' => '__updated_name__',
            ]);

            $response->assertForbidden();
        }
    });

    it('responds with not found when user does not exist', function () {
        $response = patchJson(($this->route)('non-existent-id'), [
            'name' => '__updated_name__',
        ]);

        $response->assertNotFound();
    });
});

describe('authorization', function () {
    it('responds with forbidden when the target user belongs to a different organization', function () {
        $otherOrg = Organization::factory()->create(['owner_id' => User::factory()->create()->id]);
        $outsideUser = User::factory()->create();
        $otherOrg->members()->create([
            'user_id' => $outsideUser->id,
            'role_id' => Role::where('name', RoleEnum::COACH->value)->first()->id,
        ]);

        $response = patchJson(($this->route)($outsideUser->id), [
            'name' => '__updated_name__',
        ]);

        $response->assertForbidden();
    });
});

describe('validation', function () {
    it('rejects an invalid email', function () {
        $response = patchJson(($this->route)($this->targetUser->id), [
            'email' => 'not-an-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('rejects a duplicate email from another user', function () {
        $otherUser = User::factory()->create(['email' => 'taken@example.com']);

        $response = patchJson(($this->route)($this->targetUser->id), [
            'email' => $otherUser->email,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('rejects an invalid role', function () {
        $response = patchJson(($this->route)($this->targetUser->id), [
            'role' => RoleEnum::SUPER_ADMIN->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['role']);
    });
});

describe('action', function () {
    it('updates the user name', function () {
        patchJson(($this->route)($this->targetUser->id), [
            'name' => '__updated_name__',
        ])->assertOk();

        assertDatabaseHas(User::class, [
            'id' => $this->targetUser->id,
            'name' => '__updated_name__',
        ]);
    });

    it('updates the user role within the organization', function () {
        patchJson(($this->route)($this->targetUser->id), [
            'role' => RoleEnum::ATHLETE->value,
        ])->assertOk();

        $newRoleId = Role::where('name', RoleEnum::ATHLETE->value)->first()->id;

        assertDatabaseHas('organization_user', [
            'user_id' => $this->targetUser->id,
            'organization_id' => $this->organization->id,
            'role_id' => $newRoleId,
        ]);
    });

    it('returns the updated user with role in the response', function () {
        $response = patchJson(($this->route)($this->targetUser->id), [
            'name' => '__updated_name__',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role']])
            ->assertJsonPath('data.name', '__updated_name__');
    });
});
