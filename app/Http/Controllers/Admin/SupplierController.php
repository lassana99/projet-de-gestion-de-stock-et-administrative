<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierContact; // Import du modèle de contact
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
// Importations pour l'exportation Excel
use App\Exports\SupplierExport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    /**
     * Liste des pays pour les formulaires.
     */
    private function getCountries(): array
    {
        return [
            "Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", "Andorre",
            "Angola", "Antigua-et-Barbuda", "Arabie Saoudite", "Argentine", "Arménie",
            "Australie", "Autriche", "Azerbaïdjan", "Bahamas", "Bahreïn", "Bangladesh",
            "Barbade", "Belgique", "Bélize", "Bénin", "Bhoutan", "Biélorussie",
            "Birmanie", "Bolivie", "Bosnie-Herzégovine", "Botswana", "Brésil", "Brunei",
            "Bulgarie", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodge", "Cameroun",
            "Canada", "Chili", "Chine", "Chypre", "Colombie", "Comores", "Congo (Brazzaville)",
            "Congo (Kinshasa)", "Corée du Sud", "Costa Rica", "Croatie", "Cuba", "Danemark",
            "Djibouti", "Dominique", "Égypte", "Émirats arabes unis", "Équateur", "Érythrée",
            "Espagne", "Estonie", "États-Unis", "Éthiopie", "Fidji", "Finlande", "France",
            "Gabon", "Gambie", "Géorgie", "Ghana", "Grèce", "Grenade", "Guatemala", "Guinée",
            "Guinée-Bissau", "Guinée équatoriale", "Guyana", "Haïti", "Honduras", "Hongrie",
            "Inde", "Indonésie", "Irak", "Iran", "Irlande", "Islande", "Israël", "Italie",
            "Jamaïque", "Japon", "Jordanie", "Kazakhstan", "Kenya", "Kirghizistan", "Kiribati",
            "Koweït", "Laos", "Lesotho", "Lettonie", "Liban", "Libéria", "Libye", "Liechtenstein",
            "Lituanie", "Luxembourg", "Macédoine du Nord", "Madagascar", "Malaisie", "Malawi",
            "Maldives", "Mali", "Malte", "Maroc", "Marshall", "Maurice", "Mauritanie", "Mexique",
            "Micronésie", "Moldavie", "Monaco", "Mongolie", "Monténégro", "Mozambique", "Namibie",
            "Nauru", "Népal", "Nicaragua", "Niger", "Nigéria", "Norvège", "Nouvelle-Zélande",
            "Oman", "Ouganda", "Ouzbékistan", "Pakistan", "Palaos", "Panama", "Papouasie-Nouvelle-Guinée",
            "Paraguay", "Pays-Bas", "Pérou", "Philippines", "Pologne", "Portugal", "Qatar", "République Tchèque",
            "Roumanie", "Royaume-Uni", "Russie", "Rwanda", "Saint-Christophe-et-Niévès", "Sainte-Lucie",
            "Saint-Vincent-et-les-Grenadines", "Salomon", "Salvador", "Samoa", "Sao Tomé-et-Principe",
            "Sénégal", "Serbie", "Seychelles", "Sierra Leone", "Singapour", "Slovaquie", "Slovénie",
            "Somalie", "Soudan", "Soudan du Sud", "Sri Lanka", "Suède", "Suisse", "Suriname", "Tadjikistan",
            "Tanzanie", "Tchad", "Thaïlande", "Timor oriental", "Togo", "Tonga", "Trinité-et-Trinidad",
            "Tunisie", "Turkménistan", "Turquie", "Tuvalu", "Ukraine", "Uruguay", "Vanuatu", "Vatican",
            "Venezuela", "Viêt Nam", "Yémen", "Zambie", "Zimbabwe"
        ];
    }

    /**
     * Liste des fournisseurs avec recherche GLOBALE sur tous les champs.
     */
    public function list(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $suppliers = Supplier::with('contacts')
            ->when($searchKey, function($query) use ($searchKey) {
                $query->where(function($q) use ($searchKey) {
                    $q->where('company_name', 'like', '%' . $searchKey . '%')
                      ->orWhere('country_origin', 'like', '%' . $searchKey . '%')
                      ->orWhere('specialty', 'like', '%' . $searchKey . '%')
                      ->orWhere('brand', 'like', '%' . $searchKey . '%')
                      ->orWhere('website', 'like', '%' . $searchKey . '%')
                      ->orWhere('payment_deadline', 'like', '%' . $searchKey . '%')
                      ->orWhere('nif', 'like', '%' . $searchKey . '%')
                      ->orWhere('date', 'like', '%' . $searchKey . '%')
                      // Recherche dans les champs des contacts associés
                      ->orWhereHas('contacts', function($cq) use ($searchKey) {
                          $cq->where('name', 'like', '%' . $searchKey . '%')
                            ->orWhere('position', 'like', '%' . $searchKey . '%')
                            ->orWhere('phone', 'like', '%' . $searchKey . '%')
                            ->orWhere('email', 'like', '%' . $searchKey . '%');
                      });
                });
            })
            ->latest() 
            ->paginate(10);

        return view('admin.partner.supplier.list', compact('suppliers'));
    }

    /**
     * EXPORT EXCEL UNIQUEMENT
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        // Génération du fichier Excel en passant la clé de recherche à la classe Export
        return Excel::download(new SupplierExport($searchKey), 'liste_fournisseurs_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * Page de création.
     */
    public function createPage()
    {
        $countries = $this->getCountries();
        return view('admin.partner.supplier.create', compact('countries'));
    }

    /**
     * Enregistrement d'un nouveau fournisseur.
     */
    public function supplierCreate(Request $request)
    {
        $validated = $request->validate([
            'company_name'     => 'required|string|max:255',
            'country_origin'   => 'nullable|string|max:100',
            'date'             => 'nullable|date',
            'specialty'        => 'nullable|string|max:255',
            'brand'            => 'required|string|max:255',
            'other_brand'      => 'nullable|string|max:255',
            'website'          => 'nullable|string|max:255',
            'payment_deadline' => 'nullable|string|max:50',
            'nif'              => 'nullable|string|max:100',
            // Validation des contacts
            'contact_names.*'     => 'nullable|string|max:255',
            'contact_positions.*' => 'nullable|string|max:255', 
            'contact_phones.*'    => 'nullable|string|max:255',
            'contact_emails.*'    => 'nullable|email|max:255',
        ]);

        // Gestion de la marque "Autres"
        if ($validated['brand'] === 'Autres') {
            $validated['brand'] = $validated['other_brand'];
        }

        DB::beginTransaction();
        try {
            // Création du fournisseur
            $supplier = Supplier::create($validated);

            // Enregistrement des contacts multiples
            if ($request->has('contact_names')) {
                foreach ($request->contact_names as $key => $name) {
                    if (!empty($name) || !empty($request->contact_phones[$key])) {
                        $supplier->contacts()->create([
                            'name'     => $name,
                            'position' => $request->contact_positions[$key] ?? null,
                            'phone'    => $request->contact_phones[$key],
                            'email'    => $request->contact_emails[$key],
                        ]);
                    }
                }
            }

            DB::commit();
            Alert::success('Insertion réussie', 'Fournisseur et contacts créés avec succès');
            return redirect()->route('supplierList');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', "Erreur lors de la création : {$e->getMessage()}");
            return redirect()->back()->withInput();
        }
    }

    /**
     * Affichage des détails.
     */
    public function details($id)
    {
        $supplier = Supplier::with('contacts')->findOrFail($id);
        return view('admin.partner.supplier.details', compact('supplier'));
    }

    /**
     * Page d'édition.
     */
    public function edit($id)
    {
        $supplier = Supplier::with('contacts')->findOrFail($id);
        $countries = $this->getCountries();
        return view('admin.partner.supplier.edit', compact('supplier', $countries));
    }

    /**
     * Mise à jour d'un fournisseur.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'               => 'required|exists:suppliers,id',
            'company_name'     => 'required|string|max:255',
            'country_origin'   => 'nullable|string|max:100',
            'date'             => 'nullable|date',
            'specialty'        => 'nullable|string|max:255',
            'brand'            => 'required|string|max:255',
            'other_brand'      => 'nullable|string|max:255',
            'website'          => 'nullable|string|max:255',
            'payment_deadline' => 'nullable|string|max:50',
            'nif'              => 'nullable|string|max:100',
            // Validation des contacts
            'contact_names.*'     => 'nullable|string|max:255',
            'contact_positions.*' => 'nullable|string|max:255', 
            'contact_phones.*'    => 'nullable|string|max:255',
            'contact_emails.*'    => 'nullable|email|max:255',
        ]);

        if ($validated['brand'] === 'Autres') {
            $validated['brand'] = $validated['other_brand'];
        }

        DB::beginTransaction();
        try {
            $supplier = Supplier::findOrFail($validated['id']);
            
            // Mise à jour du fournisseur
            $supplier->update($validated);

            // Mise à jour des contacts (Suppression et re-création)
            $supplier->contacts()->delete();

            if ($request->has('contact_names')) {
                foreach ($request->contact_names as $key => $name) {
                    if (!empty($name) || !empty($request->contact_phones[$key])) {
                        $supplier->contacts()->create([
                            'name'     => $name,
                            'position' => $request->contact_positions[$key] ?? null,
                            'phone'    => $request->contact_phones[$key],
                            'email'    => $request->contact_emails[$key],
                        ]);
                    }
                }
            }

            DB::commit();
            Alert::success('Mise à jour réussie', 'Fournisseur mis à jour avec succès');
            return redirect()->route('supplierList');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', "Erreur lors de la mise à jour : {$e->getMessage()}");
            return back()->withInput();
        }
    }

    /**
     * Suppression d'un fournisseur.
     */
    public function delete($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete(); // Les contacts sont supprimés via cascade en DB
            Alert::success('Suppression réussie', 'Fournisseur supprimé avec succès');
            return redirect()->route('supplierList');
        } catch (\Exception $e) {
            Alert::error('Erreur', "Erreur lors de la suppression : {$e->getMessage()}");
            return redirect()->route('supplierList');
        }
    }
}