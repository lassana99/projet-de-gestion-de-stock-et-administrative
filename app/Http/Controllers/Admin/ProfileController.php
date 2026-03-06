<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Affiche les détails d'un compte spécifique (Route: account/{id})
     * C'est ici que l'erreur se produisait.
     */
    public function accountProfile($id): View
    {
        $account = User::findOrFail($id);

        // Nom du fichier : resources/views/admin/profile/accountProfile.blade.php
        return view('admin.profile.accountProfile', [
            'account' => $account,
        ]);
    }

    /**
     * Affiche le profil de l'admin connecté (Route: detail)
     */
    public function profileDetails(Request $request): View
    {
        return view('admin.profile.accountProfile', [
            'account' => $request->user(),
        ]);
    }

    /**
     * Affiche le formulaire de création (GET)
     * Nom du fichier : resources/views/admin/profile/createAdminAcct.blade.php
     */
    public function createAdminAccount(): View
    {
        return view('admin.profile.createAdminAcct');
    }

    /**
     * Enregistre le nouveau compte (POST)
     */
    public function storeAdminAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', 'in:admin,superadmin'],
            'password' => ['required', 'min:8'],
            'confirmpassword' => ['required', 'same:password'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role, 
            'password' => Hash::make($request->password),
        ]);

        return Redirect::route('adminDashboard')->with('status', 'account-created');
    }

    /**
     * Méthodes standards de profil (Breeze/Jetstream)
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}