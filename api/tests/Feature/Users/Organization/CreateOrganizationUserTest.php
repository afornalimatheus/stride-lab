<?php

use App\Actions\Admin\Users\CreateUserAction;
use App\DTOs\Admin\User\CreateUserDTO;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new CreateUserAction();
    $this->actor = User::factory()->create();
});

describe('action', function () {
    it('creates a user in the database', function () {
        $dto = new CreateUserDTO(
            name: '__test_name__',
            email: '__test@example.com__',
            password: '__test_password__',
        );

        $this->action->execute($dto, $this->actor);

        assertDatabaseHas(User::class, [
            'name' => '__test_name__',
            'email' => '__test@example.com__',
        ]);
    });

    it('returns the created user instance', function () {
        $dto = new CreateUserDTO(
            name: '__test_name__',
            email: '__test@example.com__',
            password: '__test_password__',
        );

        $user = $this->action->execute($dto, $this->actor);

        expect($user)->toBeInstanceOf(User::class)
            ->and($user->name)->toBe('__test_name__')
            ->and($user->email)->toBe('__test@example.com__');
    });

    it('stores the password as a bcrypt hash', function () {
        $dto = new CreateUserDTO(
            name: '__test_name__',
            email: '__test@example.com__',
            password: '__test_password__',
        );

        $user = $this->action->execute($dto, $this->actor);

        expect(Hash::check('__test_password__', $user->password))->toBeTrue();
    });

    it('does not store the plain-text password', function () {
        $dto = new CreateUserDTO(
            name: '__test_name__',
            email: '__test@example.com__',
            password: '__test_password__',
        );

        $user = $this->action->execute($dto, $this->actor);

        assertDatabaseMissing(User::class, [
            'password' => '__test_password__',
        ]);

        expect($user->password)->not->toBe('__test_password__');
    });

    it('persists the user with the correct count', function () {
        $countBefore = User::count();

        $dto = new CreateUserDTO(
            name: '__test_name__',
            email: '__test@example.com__',
            password: '__test_password__',
        );

        $this->action->execute($dto, $this->actor);

        expect(User::count())->toBe($countBefore + 1);
    });

    it('rolls back the transaction when an exception is thrown', function () {
        $countBefore = User::count();

        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(new \RuntimeException('Simulated failure'));

        expect(fn () => $this->action->execute(
            new CreateUserDTO(
                name: '__test_name__',
                email: '__test@example.com__',
                password: '__test_password__',
            ),
            $this->actor,
        ))->toThrow(\RuntimeException::class, 'Simulated failure');

        expect(User::count())->toBe($countBefore);
    });

    it('assigns a database-generated id to the returned user', function () {
        $dto = new CreateUserDTO(
            name: '__test_name__',
            email: '__test@example.com__',
            password: '__test_password__',
        );

        $user = $this->action->execute($dto, $this->actor);

        expect($user->id)->not->toBeNull();
        assertDatabaseHas(User::class, ['id' => $user->id]);
    });

    it('can create multiple users with different emails', function () {
        $dto1 = new CreateUserDTO(
            name: '__test_name_1__',
            email: '__test1@example.com__',
            password: '__test_password__',
        );

        $dto2 = new CreateUserDTO(
            name: '__test_name_2__',
            email: '__test2@example.com__',
            password: '__test_password__',
        );

        $user1 = $this->action->execute($dto1, $this->actor);
        $user2 = $this->action->execute($dto2, $this->actor);

        expect($user1->id)->not->toBe($user2->id);

        assertDatabaseCount(User::class, 3); // actor + user1 + user2
        assertDatabaseHas(User::class, ['email' => '__test1@example.com__']);
        assertDatabaseHas(User::class, ['email' => '__test2@example.com__']);
    });
});