<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class RoleChangeController extends Controller
{
    /**
     * Liste des Administrateurs et Super-Administrateurs
     */
    public function adminList()
    {
        $searchKey = request('searchKey');

        // IMPORTANT : Ajout de 'role' dans le select pour l'affichage des badges
        $query = User::select('id', 'name', 'nickname', 'email', 'phone', 'address', 'role')
            ->whereIn('role', ['superadmin', 'admin']);

        if ($searchKey) {
            $query->where(function ($query) use ($searchKey) {
                $query->where('name', 'like', '%' . $searchKey . '%')
                      ->orWhere('email', 'like', '%' . $searchKey . '%');
            });
        }

        // Pagination à 10 pour une meilleure lisibilité
        $data = $query->paginate(10);

        // Compte des utilisateurs simples pour les stats si nécessaire
        $userCount = User::where('role', 'user')->count();

        return view('admin.roleChange.adminList', compact('data', 'userCount'));
    }

    /**
     * Supprimer un compte administrateur
     */
    public function deleteAdminAccount($id)
    {
        User::where('id', $id)->delete();

        Alert::success('Suppression réussie', 'Le compte administrateur a été supprimé.');
        return back();
    }

    /**
     * Promouvoir un Utilisateur simple en Administrateur
     * (Utilisé depuis la liste des utilisateurs)
     */
    public function changeAdminRole($id)
    {
        User::where('id', $id)->update(['role' => 'admin']);

        Alert::success('Promotion réussie', 'L\'utilisateur est désormais Administrateur.');
        return back();
    }

    /**
     * Basculer entre Administrateur et Super-Administrateur
     * (Action du bouton "Sync" dans la liste admin)
     */
    public function changeUserRole($id)
    {
        $user = User::findOrFail($id);

        // Logique de bascule (Toggle) entre les deux rôles admin
        if ($user->role === 'admin') {
            $user->update(['role' => 'superadmin']);
            Alert::success('Changement de rôle', 'Le compte a été promu Super-Administrateur.');
        } else if ($user->role === 'superadmin') {
            $user->update(['role' => 'admin']);
            Alert::success('Changement de rôle', 'Le compte a été rétrogradé Administrateur.');
        } else {
            // Sécurité : Si par erreur on clique sur un 'user'
            Alert::warning('Action impossible', 'Ce compte n\'est pas un administrateur.');
        }

        return back();
    }

    /**
     * Liste des Utilisateurs simples
     */
    public function userList()
    {
        $searchKey = request('searchKey');

        // Ajout de 'role' ici aussi par cohérence
        $query = User::select('id', 'name', 'nickname', 'email', 'phone', 'address', 'role')
            ->where('role', 'user');

        if ($searchKey) {
            $query->where(function ($query) use ($searchKey) {
                $query->where('name', 'like', '%' . $searchKey . '%')
                      ->orWhere('email', 'like', '%' . $searchKey . '%');
            });
        }

        $data = $query->paginate(10);

        $adminCount = User::whereIn('role', ['superadmin', 'admin'])->count();

        return view('admin.roleChange.userList', compact('data', 'adminCount'));
    }
}