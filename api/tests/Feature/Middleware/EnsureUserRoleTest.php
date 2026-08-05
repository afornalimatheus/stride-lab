<?php

use App\Enums\RoleEnum;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    Model::unguard();

    foreach (RoleEnum::cases() as $roleEnum) {
        Role::create(['name' => $roleEnum->value]);
    }

    $this->withRole = function (RoleEnum $role): void {
        $this->authenticatedUser = User::factory()->create();

        $organization = Organization::factory()->create([
            'owner_id' => $this->authenticatedUser->id,
        ]);

        $organization->members()->create([
            'user_id' => $this->authenticatedUser->id,
            'role_id' => Role::where('name', $role->value)->first()->id,
        ]);

        $token = JWTAuth::fromUser($this->authenticatedUser);
        $this->withToken($token);
    };

    $this->route = route('admin.dashboard.users.create');
});

describe('route', function () {
    it('responds with unauthorized when not authenticated', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertUnauthorized();
    });
});

describe('middleware', function () {
    it('allows a user with the SUPER_ADMIN role', function () {
        ($this->withRole)(RoleEnum::SUPER_ADMIN);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertCreated();
    });

    it('responds with forbidden when the user has the OWNER role', function () {
        ($this->withRole)(RoleEnum::OWNER);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertForbidden();
    });

    it('responds with forbidden when the user has the MANAGER role', function () {
        ($this->withRole)(RoleEnum::MANAGER);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertForbidden();
    });

    it('responds with forbidden when the user has the COACH role', function () {
        ($this->withRole)(RoleEnum::COACH);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertForbidden();
    });

    it('responds with forbidden when the user has the ATHLETE role', function () {
        ($this->withRole)(RoleEnum::ATHLETE);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertForbidden();
    });
});
