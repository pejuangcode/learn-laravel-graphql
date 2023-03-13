<?php

namespace App\GraphQL\Mutations;

use App\Models\User;

final class CreateUserMutation
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function createUser($_, array $args)
    {
        $user = new User();
        $user->name = $args['name'];
        $user->email = $args['email'];
        $user->password = 'dfsjfd';
        $user->save();

        return $user;
    }
}
