<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Create a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   

        $data = $this->validate($request, [
            'email' => ['email', 'unique:users'],
            'password' => ['min:6']
        ]);

        $user = User::create($data);

        return response()->json(['user' => $user]);
    }
}
