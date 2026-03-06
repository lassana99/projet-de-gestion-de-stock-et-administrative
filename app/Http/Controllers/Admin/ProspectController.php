<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Models\Customer;
use App\Models\ProspectContact;
use App\Models\CustomerContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
// Importations pour l'exportation Excel
use App\Exports\ProspectExport;
use Maatwebsite\Excel\Facades\Excel;

class ProspectController extends Controller
{
    /**
     * Centralisation de la logique de recherche globale (Web + Excel)
     */
    private function applySearchFilters($query, $searchKey)
    {
        if ($searchKey) {
            $query->where(function($q) use ($searchKey) {
                $q->where('name', 'like', '%' . $searchKey . '%')
                  ->orWhere('address', 'like', '%' . $searchKey . '%')
                  ->orWhere('domain', 'like', '%' . $searchKey . '%')
                  ->orWhere('contact', 'like', '%' . $searchKey . '%')
                  ->orWhere('email', 'like', '%' . $searchKey . '%')
                  ->orWhere('website', 'like', '%' . $searchKey . '%')
                  ->orWhere('need', 'like', '%' . $searchKey . '%')
                  ->orWhere('comment', 'like', '%' . $searchKey . '%')
                  ->orWhere('statut_achat', 'like', '%' . $searchKey . '%')
                  ->orWhere('date', 'like', '%' . $searchKey . '%')
                  // Recherche dans les contacts associés (relation)
                  ->orWhereHas('contacts', function($cq) use ($searchKey) {
                      $cq->where('name', 'like', '%' . $searchKey . '%')
                        ->orWhere('position', 'like', '%' . $searchKey . '%')
                        ->orWhere('phone', 'like', '%' . $searchKey . '%')
                        ->orWhere('email', 'like', '%' . $searchKey . '%');
                  });
            });
        }
        return $query;
    }

    /**
     * Liste des prospects avec RECHERCHE GLOBALE sur tous les champs.
     */
    public function list(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $query = Prospect::with('contacts');

        // Application de la logique de recherche
        $this->applySearchFilters($query, $searchKey);

        $prospects = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.partner.prospect.list', compact('prospects'));
    }

    /**
     * EXPORT EXCEL UNIQUEMENT
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        // Génération du fichier Excel en passant la clé de recherche à la classe Export
        return Excel::download(new ProspectExport($searchKey), 'liste_prospects_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * Page de création.
     */
    public function createPage()
    {
        $date = Carbon::now()->format('Y-m-d');
        return view('admin.partner.prospect.create', compact('date'));
    }

    /**
     * Enregistrement d'un nouveau prospect.
     */
    public function prospectCreate(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'address'             => 'required|string|max:255',
            'domain'              => 'required|string|max:255',
            'contact'             => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255',
            'website'             => 'nullable|string|max:255',
            'need'                => 'nullable|string|max:255',
            'comment'             => 'nullable|string',
            'statut_achat'        => 'required|in:OUI,NON',
            'date'                => 'nullable|date',
            'contact_names.*'     => 'nullable|string|max:255',
            'contact_positions.*' => 'nullable|string|max:255',
            'contact_phones.*'    => 'nullable|string|max:255',
            'contact_emails.*'    => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();
        try {
            if (empty($validated['contact']) && !empty($request->contact_names[0])) {
                $validated['contact'] = $request->contact_names[0];
            }

            // Si création directe en tant que client (Statut OUI)
            if ($validated['statut_achat'] === 'OUI') {
                DB::commit();
                return redirect()->route('customerCreate', [
                    'client_name'       => $validated['name'],
                    'address'           => $validated['address'],
                    'domain'            => $validated['domain'],
                    'email'             => $validated['email'],
                    'contact'           => $validated['contact'],
                    'date'              => $validated['date'] ?? Carbon::now()->format('Y-m-d'),
                    'contact_names'     => $request->contact_names,
                    'contact_positions' => $request->contact_positions,
                    'contact_phones'    => $request->contact_phones,
                    'contact_emails'    => $request->contact_emails,
                ]);
            }

            $prospect = Prospect::create($validated);

            if ($request->has('contact_names')) {
                foreach ($request->contact_names as $key => $name) {
                    if (!empty($name) || !empty($request->contact_phones[$key])) {
                        $prospect->contacts()->create([
                            'name'     => $name,
                            'position' => $request->contact_positions[$key],
                            'phone'    => $request->contact_phones[$key],
                            'email'    => $request->contact_emails[$key],
                        ]);
                    }
                }
            }

            DB::commit();
            Alert::success('Insertion réussie', 'Prospect créé avec succès');
            return redirect()->route('prospectList');

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Page de détails.
     */
    public function details($id)
    {
        $prospect = Prospect::with('contacts')->findOrFail($id);
        return view('admin.partner.prospect.details', compact('prospect'));
    }

    /**
     * Page d'édition.
     */
    public function edit($id)
    {
        $prospect = Prospect::with('contacts')->findOrFail($id);
        return view('admin.partner.prospect.edit', compact('prospect'));
    }

    /**
     * Mise à jour du prospect et conversion si OUI.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'                  => 'required|exists:prospects,id',
            'name'                => 'required|string|max:255',
            'address'             => 'required|string|max:255',
            'domain'              => 'required|string|max:255',
            'contact'             => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255',
            'website'             => 'nullable|string|max:255',
            'need'                => 'nullable|string|max:255',
            'comment'             => 'nullable|string',
            'statut_achat'        => 'required|in:OUI,NON',
            'date'                => 'nullable|date',
            'contact_names.*'     => 'nullable|string|max:255',
            'contact_positions.*' => 'nullable|string|max:255',
            'contact_phones.*'    => 'nullable|string|max:255',
            'contact_emails.*'    => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();
        try {
            $prospect = Prospect::findOrFail($validated['id']);
            
            // Mise à jour des informations de base
            $prospect->update($validated);

            // Mise à jour des contacts (Suppression et re-création)
            $prospect->contacts()->delete();
            if ($request->has('contact_names')) {
                foreach ($request->contact_names as $key => $name) {
                    if (!empty($name) || !empty($request->contact_phones[$key])) {
                        $prospect->contacts()->create([
                            'name'     => $name,
                            'position' => $request->contact_positions[$key],
                            'phone'    => $request->contact_phones[$key],
                            'email'    => $request->contact_emails[$key],
                        ]);
                    }
                }
            }

            // ACTION SPÉCIFIQUE : SI STATUT PASSE À OUI
            if ($validated['statut_achat'] === 'OUI') {
                
                // Préparation des données pour le formulaire client
                $params = [
                    'client_name'       => $prospect->name,
                    'address'           => $prospect->address,
                    'domain'            => $prospect->domain,
                    'email'             => $prospect->email,
                    'contact'           => $prospect->contact,
                    'date'              => $prospect->date ? $prospect->date->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                    'contact_names'     => $request->contact_names,
                    'contact_positions' => $request->contact_positions,
                    'contact_phones'    => $request->contact_phones,
                    'contact_emails'    => $request->contact_emails,
                ];

                // Suppression automatique du prospect
                $prospect->delete();

                DB::commit();
                
                // Redirection automatique vers la création de client avec les données
                return redirect()->route('customerCreate', $params);
            }

            DB::commit();
            Alert::success('Mise à jour réussie', 'Prospect mis à jour avec succès');
            return redirect()->route('prospectList');

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Suppression d'un prospect.
     */
    public function delete($id)
    {
        try {
            $prospect = Prospect::findOrFail($id);
            $prospect->delete();
            Alert::success('Supprimé', 'Le prospect a été supprimé.');
            return redirect()->route('prospectList');
        } catch (\Exception $e) {
            Alert::error('Erreur', $e->getMessage());
            return redirect()->route('prospectList');
        }
    }

    /**
     * Conversion directe via bouton vers le formulaire client.
     */
    public function convertToClient($id)
    {
        $prospect = Prospect::with('contacts')->findOrFail($id);
        
        $contact_names = $prospect->contacts->pluck('name')->toArray();
        $contact_positions = $prospect->contacts->pluck('position')->toArray();
        $contact_phones = $prospect->contacts->pluck('phone')->toArray();
        $contact_emails = $prospect->contacts->pluck('email')->toArray();

        $params = [
            'client_name'       => $prospect->name,
            'address'           => $prospect->address,
            'domain'            => $prospect->domain,
            'email'             => $prospect->email,
            'contact'           => $prospect->contact,
            'date'              => $prospect->date ? $prospect->date->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
            'contact_names'     => $contact_names,
            'contact_positions' => $contact_positions,
            'contact_phones'    => $contact_phones,
            'contact_emails'    => $contact_emails,
        ];

        // Suppression du prospect avant redirection pour automatiser le nettoyage
        $prospect->delete();

        return redirect()->route('customerCreate', $params);
    }
}