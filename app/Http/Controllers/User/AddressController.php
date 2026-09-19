<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => ['required', 'string', 'regex:/^[0-9]{5}(?:-[0-9]{4})?$/'], // Basic pin code validation
            'country' => 'required|string|max:255',
            'address_type' => 'required|in:home,work',
            'is_default' => 'boolean'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        // If this is the first address, make it default. Or if user checked default.
        if ($validated['is_default'] ?? false || $user->addresses()->count() === 0) {
            $user->addresses()->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $user->addresses()->create($validated);

        return redirect()->back()->with('success', 'Address added successfully.');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => ['required', 'string', 'regex:/^[0-9]{5}(?:-[0-9]{4})?$/'],
            'country' => 'required|string|max:255',
            'address_type' => 'required|in:home,work',
            'is_default' => 'boolean'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $address = $user->addresses()->findOrFail($id);

        if ($validated['is_default'] ?? false) {
            $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        } else if ($address->is_default) {
            // Cannot uncheck default if it's the only one
            if ($user->addresses()->count() === 1) {
                $validated['is_default'] = true;
            }
        }

        $address->update($validated);

        return redirect()->back()->with('success', 'Address updated successfully.');
    }

    public function destroy(string $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $address = $user->addresses()->findOrFail($id);

        if ($address->is_default) {
            $otherAddress = $user->addresses()->where('id', '!=', $id)->first();
            if ($otherAddress) {
                $otherAddress->update(['is_default' => true]);
            }
        }

        $address->delete();

        return redirect()->back()->with('success', 'Address deleted successfully.');
    }
}
