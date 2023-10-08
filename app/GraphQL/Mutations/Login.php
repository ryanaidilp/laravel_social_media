<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Exceptions\ValidationException;

class Login
{
    /** @param  array{}  $args */
    public function __invoke($_, array $args)
    {
        $user = User::where('email', $args['identifier'] ?? null)
            ->orWhere('username', $args['identifier'] ?? null)
            ->first();

        if (!$user || Hash::check($args['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        return $user->createToken($args['device_name'])->plainTextToken;
    }
}
