<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    function showByUsername($username)
    {
        $user = \App\Models\User::where('username', $username)->firstOrFail();
        return view('profile.show', ['user' => $user]);
    }

    function showById($id)
    {
        $user = \App\Models\User::where('id', $id)->firstOrFail();
        return view('profile.show', ['user' => $user]);
    }
}