<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;

class Logout
{
    /** @param  array{}  $args */
    public function __invoke($_, array $args)
    {
        $user = User::find($args['id']);

        return $user->tokens()->delete();
    }
}
