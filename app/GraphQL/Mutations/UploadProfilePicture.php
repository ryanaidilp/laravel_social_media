<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;

class UploadProfilePicture
{
    /** @param  array{}  $args */
    public function __invoke($_, array $args)
    {
        $file = $args['photo'];
        $path = $file->storePublicly('public/uploads');
        $user = User::find($args['id']);
        $user->photo = $path;
        $user->save();
        return $user->refresh();
    }
}
