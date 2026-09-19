<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::latest()
        ->where('role', 'user')
        ->paginate(10);
        return \Inertia\Inertia::render('Admin/Users/Index', ['users' => $users]);
    }
    public function edit(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        return \Inertia\Inertia::render('Admin/Users/Edit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        $rules = [];
        if ($request->has('name')) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $user->id;
        }
        if ($request->has('status')) {
            $rules['status'] = 'required|in:active,inactive';
        }
        
        $request->validate($rules);

        $user->update($request->only(['name', 'email', 'status']));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // Ensure not deleting the last admin or something, but this is a user module so it's mainly for 'user' role.
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete an admin account from here.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
