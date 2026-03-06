<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    // Page de changement de mot de passe (pour l'utilisateur connecté lui-même)
    public function changePasswordPage(){
        return view('admin.password.changePassword');
    }

    public function changePassword(Request $request){
        $request->validate([
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6',
            'confirmPassword' => 'required|same:newPassword',
        ]);

        $user = Auth::user();
        
        if (Hash::check($request->oldPassword, $user->password)) {
            User::where('id', $user->id)->update([
                'password' => Hash::make($request->newPassword)
            ]);

            Alert::success('Succès', 'Votre mot de passe a été mis à jour avec succès.');
            return back();
        } else {
            Alert::error('Erreur', 'L\'ancien mot de passe est incorrect.');
            return back();
        }
    }

    // Page de réinitialisation (généralement utilisée par l'admin pour un compte utilisateur)
    public function resetPasswordPage(){
        return view('admin.password.resetPassword');
    }

    /**
     * Réinitialisation avec saisie manuelle du nouveau mot de passe
     */
    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'newPassword' => 'required|min:6', // Possibilité de saisir son propre mot de passe
            'confirmPassword' => 'required|same:newPassword',
        ]);

        $user = User::where('email', $request->email)->first();

        if($user){
            $user->password = Hash::make($request->newPassword);
            $user->save();

            Alert::success('Succès', 'Le mot de passe de l\'utilisateur a été réinitialisé avec succès.');
            return back();
        }

        Alert::error('Erreur', 'Utilisateur non trouvé.');
        return back();
    }
}