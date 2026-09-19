<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class VendorRegisteredUserController extends Controller
{
    /**
     * Display the vendor registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/VendorRegister');
    }

    /**
     * Handle an incoming vendor registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'vendor',
        ]);

        Vendor::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'slug' => Str::slug($request->store_name) . '-' . uniqid(),
            'description' => $request->description,
            'is_verified' => false,
        ]);

        event(new Registered($user));

        // We do not log them in. We redirect them to the vendor login page with a success message.
        return redirect(route('vendor.login'))->with('success', 'Admin will approve your request then you can login');
    }
}
