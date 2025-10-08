<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

}
