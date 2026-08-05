<?php

use App\Enums\RoleEnum;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    Model::unguard();

    foreach (RoleEnum::cases() as $roleEnum) {
        Role::create(['name' => $roleEnum->value]);
    }

    $this->validateOriginalContent = function (): void {
        assertDatabaseCount(User::class, 1);
        assertDatabaseCount(Organization::class, 1);
        assertDatabaseCount('organization_user', 1);
    };

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

    $this->route = route('dashboard.users.create');
});

describe('route', function () {
    it('responds with unauthorized when not authenticated', function () {
        $this->flushHeaders();

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertUnauthorized();
    });
});

describe('action', function () {
    it('responds with an exception if the authenticated user does not belong to any organization', function () {
        $this->authenticatedUser = User::factory()->create();

        $token = JWTAuth::fromUser($this->authenticatedUser);
        $this->withToken($token);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertJson([
            'message' => 'User does not belong to any organization.',
        ]);
    });

    it('responds with an exception if the role is SUPER_ADMIN', function () {
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

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::SUPER_ADMIN->value,
        ]);

        $response->assertJson([
            'message' => 'The selected role is invalid.',
        ]);
    });
});

describe('request', function () {
    it('responds with unprocessable if fields have invalid values', function () {
        $response = postJson($this->route, [
            'name' => [1, 2, 3],
            'email' => 'not_an_email',
            'password' => str_repeat('a', 7),
            'role' => 'INVALID_ROLE',
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['name' => ['The name field must be a string.']])
            ->assertJsonFragment(['email' => ['The email field must be a valid email address.']])
            ->assertJsonFragment(['password' => ['The password field must be at least 8 characters.']])
            ->assertJsonFragment(['role' => ['The selected role is invalid.']]);

        ($this->validateOriginalContent)();
    });

    it('responds with unprocessable if name is missing', function () {
        $response = postJson($this->route, [
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['name' => ['The name field is required.']]);

        ($this->validateOriginalContent)();
    });

    it('responds with unprocessable if email is missing', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['email' => ['The email field is required.']]);

        ($this->validateOriginalContent)();
    });

    it('responds with unprocessable if password is missing', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['password' => ['The password field is required.']]);

        ($this->validateOriginalContent)();
    });

    it('responds with unprocessable if role is missing', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['role' => ['The role field is required.']]);

        ($this->validateOriginalContent)();
    });

    it('responds with unprocessable if email already exists', function () {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'existing@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['email' => ['The email has already been taken.']]);
    });

    it('responds with unprocessable if name exceeds 255 characters', function () {
        $response = postJson($this->route, [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['name' => ['The name field must not be greater than 255 characters.']]);

        ($this->validateOriginalContent)();
    });

    it('responds with unprocessable if email exceeds 255 characters', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => str_repeat('a', 256).'@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['email' => ['The email field must not be greater than 255 characters.']]);

        ($this->validateOriginalContent)();
    });

    it('responds with unprocessable if role is SUPER_ADMIN', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::SUPER_ADMIN->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['role' => ['The selected role is invalid.']]);

        ($this->validateOriginalContent)();
    });

    it('responds with unprocessable if role is COACH', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::COACH->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['role' => ['The selected role is invalid.']]);

        ($this->validateOriginalContent)();
    });
});

describe('controller', function () {
    it('successfully creates an organization user', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertCreated();

        assertDatabaseCount(User::class, 2);
        assertDatabaseHas(User::class, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
        ]);

        $createdUser = User::where('email', 'test@example.com')->first();

        expect(Hash::check('__test_password__', $createdUser->password))->toBeTrue();

        assertDatabaseCount('organization_user', 2);
        assertDatabaseHas('organization_user', [
            'user_id' => $createdUser->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::where('name', RoleEnum::MANAGER->value)->first()->id,
        ]);
    });

    it('creates a user with ATHLETE role', function () {
        $response = postJson($this->route, [
            'name' => '__athlete_name__',
            'email' => 'athlete@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::ATHLETE->value,
        ]);

        $response->assertCreated();

        assertDatabaseCount(User::class, 2);
        assertDatabaseHas(User::class, [
            'name' => '__athlete_name__',
            'email' => 'athlete@example.com',
        ]);

        $createdUser = User::where('email', 'athlete@example.com')->first();

        assertDatabaseHas('organization_user', [
            'user_id' => $createdUser->id,
            'organization_id' => $this->organization->id,
            'role_id' => Role::where('name', RoleEnum::ATHLETE->value)->first()->id,
        ]);
    });

    it('creates a user with OWNER role', function () {
        $response = postJson($this->route, [
            'name' => '__owner_name__',
            'email' => 'owner@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::OWNER->value,
        ]);

        $response->assertCreated();

        assertDatabaseCount(User::class, 2);
        assertDatabaseHas(User::class, [
            'name' => '__owner_name__',
            'email' => 'owner@example.com',
        ]);
    });

    it('stores the password as a bcrypt hash', function () {
        postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ])->assertCreated();

        $user = User::where('email', 'test@example.com')->first();

        expect(Hash::check('__test_password__', $user->password))->toBeTrue();
    });

    it('does not store the plain-text password', function () {
        postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ])->assertCreated();

        assertDatabaseMissing(User::class, [
            'password' => '__test_password__',
        ]);
    });

    it('returns the created user in the response', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertCreated();

        $createdUser = User::where('email', 'test@example.com')->first();

        expect($response->json('data'))->toMatchArray([
            'id' => $createdUser->id,
            'name' => '__test_name__',
            'email' => 'test@example.com',
        ]);

        expect($response->json('data'))->toHaveKeys(['id', 'name', 'email', 'organization', 'role']);
    });

    it('assigns a database-generated id to the created user', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertCreated();

        $userId = $response->json('data.id');

        expect($userId)->not->toBeNull();
        assertDatabaseHas(User::class, ['id' => $userId]);
    });

    it('can create multiple users with different emails', function () {
        $response1 = postJson($this->route, [
            'name' => '__test_name_1__',
            'email' => 'test1@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response1->assertCreated();

        $response2 = postJson($this->route, [
            'name' => '__test_name_2__',
            'email' => 'test2@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::ATHLETE->value,
        ]);

        $response2->assertCreated();

        assertDatabaseCount(User::class, 3);
        assertDatabaseHas(User::class, ['email' => 'test1@example.com']);
        assertDatabaseHas(User::class, ['email' => 'test2@example.com']);

        expect($response1->json('data.id'))->not->toBe($response2->json('data.id'));
    });

    it('returns the correct response structure', function () {
        $response = postJson($this->route, [
            'name' => '__test_name__',
            'email' => 'test@example.com',
            'password' => '__test_password__',
            'role' => RoleEnum::MANAGER->value,
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'organization' => [
                        'id',
                        'name',
                    ],
                    'role' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    });
});
