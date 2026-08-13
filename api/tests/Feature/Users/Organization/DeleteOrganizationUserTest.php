<?php

use App\Enums\RoleEnum;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

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

    $this->route = fn (string $userId) => route('dashboard.users.delete', $userId);
});

describe('route', function () {
    it('responds with unauthorized when not authenticated', function () {
        $this->flushHeaders();

        $response = deleteJson(($this->route)($this->targetUser->id));

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

            $response = deleteJson(($this->route)($this->targetUser->id));

            $response->assertForbidden();
        }
    });

    it('responds with not found when user does not exist', function () {
        $response = deleteJson(($this->route)('non-existent-id'));

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

        $response = deleteJson(($this->route)($outsideUser->id));

        $response->assertForbidden();
    });
});

describe('action', function () {
    it('removes the user from the organization', function () {
        deleteJson(($this->route)($this->targetUser->id))->assertNoContent();

        assertDatabaseMissing('organization_user', [
            'user_id' => $this->targetUser->id,
            'organization_id' => $this->organization->id,
        ]);
    });

    it('does not delete the user globally', function () {
        deleteJson(($this->route)($this->targetUser->id))->assertNoContent();

        assertDatabaseCount(User::class, 2); // authenticated user + target user still exist
    });

    it('responds with 204 no content', function () {
        $response = deleteJson(($this->route)($this->targetUser->id));

        $response->assertNoContent();
    });
});
