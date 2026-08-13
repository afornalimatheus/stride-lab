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

    $this->route = fn (string $userId) => route('admin.dashboard.users.update', $userId);
});

describe('route', function () {
    it('responds with unauthorized when not authenticated', function () {
        $this->flushHeaders();

        $response = patchJson(($this->route)($this->targetUser->id), [
            'name' => '__updated_name__',
        ]);

        $response->assertUnauthorized();
    });

    it('responds with forbidden when role is not SUPER_ADMIN', function () {
        foreach ([RoleEnum::OWNER, RoleEnum::MANAGER, RoleEnum::COACH, RoleEnum::ATHLETE] as $role) {
            ($this->withRole)($role);

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

    it('allows updating the email to the same value', function () {
        $response = patchJson(($this->route)($this->targetUser->id), [
            'email' => $this->targetUser->email,
        ]);

        $response->assertOk();
    });

    it('rejects a password shorter than 8 characters', function () {
        $response = patchJson(($this->route)($this->targetUser->id), [
            'password' => 'short',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
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

    it('updates the user email', function () {
        patchJson(($this->route)($this->targetUser->id), [
            'email' => 'new@example.com',
        ])->assertOk();

        assertDatabaseHas(User::class, [
            'id' => $this->targetUser->id,
            'email' => 'new@example.com',
        ]);
    });

    it('does not change fields that are not provided', function () {
        $originalName = $this->targetUser->name;

        patchJson(($this->route)($this->targetUser->id), [
            'email' => 'onlyemail@example.com',
        ])->assertOk();

        assertDatabaseHas(User::class, [
            'id' => $this->targetUser->id,
            'name' => $originalName,
        ]);
    });

    it('returns the updated user in the response', function () {
        $response = patchJson(($this->route)($this->targetUser->id), [
            'name' => '__updated_name__',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $this->targetUser->id)
            ->assertJsonPath('data.name', '__updated_name__');
    });
});
