<?php

use App\Enums\RoleEnum;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    Model::unguard();

    foreach (RoleEnum::cases() as $roleEnum) {
        Role::create(['name' => $roleEnum->value]);
    }

    $this->withRole = function (RoleEnum $role): void {
        if ($role === RoleEnum::SUPER_ADMIN) {
            $this->authenticatedUser = User::factory()->globalRole($role)->create();
        } else {
            $this->authenticatedUser = User::factory()->create();

            $this->organization = Organization::factory()->create([
                'owner_id' => $this->authenticatedUser->id,
            ]);

            $this->organization->members()->create([
                'user_id' => $this->authenticatedUser->id,
                'role_id' => Role::where('name', $role->value)->first()->id,
            ]);
        }

        $token = JWTAuth::fromUser($this->authenticatedUser);
        $this->withToken($token);
    };

    ($this->withRole)(RoleEnum::SUPER_ADMIN);

    $this->targetUser = User::factory()->create();

    $this->route = fn (string $userId) => route('admin.dashboard.users.delete', $userId);
});

describe('route', function () {
    it('responds with unauthorized when not authenticated', function () {
        $this->flushHeaders();

        $response = deleteJson(($this->route)($this->targetUser->id));

        $response->assertUnauthorized();
    });

    it('responds with forbidden when role is not SUPER_ADMIN', function () {
        foreach ([RoleEnum::OWNER, RoleEnum::MANAGER, RoleEnum::COACH, RoleEnum::ATHLETE] as $role) {
            ($this->withRole)($role);

            $response = deleteJson(($this->route)($this->targetUser->id));

            $response->assertForbidden();
        }
    });

    it('responds with not found when user does not exist', function () {
        $response = deleteJson(($this->route)('non-existent-id'));

        $response->assertNotFound();
    });
});

describe('action', function () {
    it('soft-deletes the user', function () {
        deleteJson(($this->route)($this->targetUser->id))->assertNoContent();

        assertSoftDeleted(User::class, ['id' => $this->targetUser->id]);
    });

    it('responds with 204 no content', function () {
        $response = deleteJson(($this->route)($this->targetUser->id));

        $response->assertNoContent();
    });

    it('prevents accessing a deleted user again', function () {
        deleteJson(($this->route)($this->targetUser->id))->assertNoContent();

        $response = deleteJson(($this->route)($this->targetUser->id));

        $response->assertNotFound();
    });
});
