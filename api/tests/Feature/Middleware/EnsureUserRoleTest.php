<?php

use App\Enums\RoleEnum;
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

    $this->withGlobalRole = function (RoleEnum $role): void {
        $this->authenticatedUser = User::factory()->globalRole($role)->create();

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
    it('allows a user with the SUPER_ADMIN global role', function () {
        ($this->withGlobalRole)(RoleEnum::SUPER_ADMIN);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertCreated();
    });

    it('responds with forbidden when the user has the OWNER global role', function () {
        ($this->withGlobalRole)(RoleEnum::OWNER);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertForbidden();
    });

    it('responds with forbidden when the user has the MANAGER global role', function () {
        ($this->withGlobalRole)(RoleEnum::MANAGER);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertForbidden();
    });

    it('responds with forbidden when the user has the COACH global role', function () {
        ($this->withGlobalRole)(RoleEnum::COACH);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertForbidden();
    });

    it('responds with forbidden when the user has the ATHLETE global role', function () {
        ($this->withGlobalRole)(RoleEnum::ATHLETE);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertForbidden();
    });

    it('responds with forbidden when the user has no global role', function () {
        $this->authenticatedUser = User::factory()->create();

        $token = JWTAuth::fromUser($this->authenticatedUser);
        $this->withToken($token);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertForbidden();
    });
});
