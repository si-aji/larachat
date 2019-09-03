<?php

namespace App\Http\Controllers;

use Auth;
use App\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('content.profile.index', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:users,name,'.$id],
            'username' => ['required', 'string', 'min:5', 'unique:users,email,'.$id]
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->username;
        $user->password = Hash::make($request->username);
        $user->save();

        return redirect('/profile')->with([
            'status' => 'success',
            'message' => 'User Updated'
        ]);
    }
}
