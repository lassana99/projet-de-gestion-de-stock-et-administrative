<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Report;
use App\Models\Product;
use App\Models\Category; // Laisser l'import pour la flexibilité, même si non utilisé dans index()
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class UserDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'utilisateur avec les données clés.
     */
    public function index(){
        
        // CORRECTION: Utilisation de pluck('brand') pour obtenir une collection simple de chaînes de caractères (noms de marques).
        // Cela évite l'erreur si la vue attendait un tableau simple et non une collection de modèles.
        $brands = Product::whereNotNull('brand')->distinct()->pluck('brand');
        
        // Récupère tous les produits (comme dans l'ancienne version)
        $products = Product::get();

        $customerCount = User::where('role','user')->count();

        // Récupère les évaluations et les informations de l'utilisateur associé
        $rating = Rating::select('ratings.count','users.name','users.nickname','users.profile','ratings.created_at')
                            ->leftJoin('users','ratings.user_id','users.id')
                            ->orderBy('created_at','desc')
                            ->get();

        // Récupère les 3 produits les plus vendus (Top 3)
        $order = Order::select('products.name', 'products.price', 'products.image','products.id')
                            ->selectRaw('SUM(orders.count) as total_sales')
                            ->leftJoin('products', 'orders.product_id', 'products.id')
                            ->groupBy('products.id')
                            // Note: Le groupBy doit inclure toutes les colonnes sélectionnées sauf l'agrégation pour certaines bases de données. 
                            // Pour Laravel/MySQL, ça fonctionne généralement.
                            ->orderBy('total_sales', 'desc')
                            ->limit(3)
                            ->get();

        // Renvoie les données à la vue
        return view('user.home', compact('brands', 'products', 'customerCount', 'rating', 'order'));
    }

    // user profile
    public function profileDetails(){
        return view('user.profile');
    }

    // update profile
    public function profileUpdate(Request $request){
       $request->validate([
           'phone' => ['required','unique:users,phone,' . Auth::user()->id],
           'address' => 'required',
           'image' => ['mimes:png,jpeg,svg,gif,bmp,webp']
       ]);

       $rules = [];
       if(Auth::user()->provider == 'simple'){
            $rules['name'] = 'required';
            $rules['email'] = 'required|unique:users,email,'. Auth::user()->id;
       }
       
       // Re-valider en tenant compte des règles dynamiques
       $request->validate($rules);


        $data =[
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if($request->hasFile('image')){
            // delete old image
            if($request->oldImage != null){
                if(file_exists(public_path('userProfile/'.$request->oldImage))){
                    unlink(public_path('userProfile/'.$request->oldImage));
                }
            }
            // upload new image
            $fileName = uniqid() . '_'. $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path(). '/userProfile/' , $fileName);
            $data['profile'] = $fileName;
        }

        User::where('id',Auth::user()->id)->update($data);
        Alert::success('Update Success', 'Profile Updated Successfully....');
        return to_route('userDashboard');
    }

    // password change page
    public function changePassword(){
        return view ('user.changeUserPassword');
    }

    // password change
    public function changeUserPassword(Request $request){
        $request->validate([
            'oldPassword' => 'required',
            'newPassword' => 'required',
            'confirmPassword' => 'required|same:newPassword',
        ]);
        $dbHashPassword = User::select('password')->where('id',Auth::user()->id)->first();
        $dbHashPassword = $dbHashPassword['password'];
        
        $oldUserPassword = $request->oldPassword;
        if(Hash::check($oldUserPassword, $dbHashPassword)){
            $data = [
                'password' => Hash::make($request->newPassword)
            ];

        User::where('id',Auth::user()->id)->update($data);
        Alert::success('Update Success', 'Password Updated Successfully....');
        return back();
        } else {
             // Ajouter un message d'erreur si l'ancien mot de passe est incorrect
             return back()->withErrors(['oldPassword' => 'L\'ancien mot de passe est incorrect.']);
        }
    }

    // contact page
    public function contactUs(){
        return view('user.contact');
    }

    // send message from user
    public function sendMessage(Request $request){
        $userId = Auth::user()->id;

        $request->validate([
            'subject' => 'required',
            'message' => 'required',
        ]);

        Report::create([
            'user_id' => $userId,
            'title' => $request->subject,
            'message' => $request->message,
        ]);
        return back()->with('success', 'Your message has been sent successfully!');
    }
}