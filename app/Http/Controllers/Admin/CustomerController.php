<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Prospect; // Ajouté pour la suppression après conversion
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
// Importations pour l'exportation Excel
use App\Exports\CustomerExport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    /**
     * Centralisation de la logique de recherche globale (Web + Excel)
     */
    private function applySearchFilters($query, $searchKey)
    {
        if ($searchKey) {
            $query->where(function($q) use ($searchKey) {
                $q->where('name', 'like', '%' . $searchKey . '%')
                  ->orWhere('code_client', 'like', '%' . $searchKey . '%')
                  ->orWhere('address', 'like', '%' . $searchKey . '%')
                  ->orWhere('domain', 'like', '%' . $searchKey . '%')
                  ->orWhere('contact', 'like', '%' . $searchKey . '%')
                  ->orWhere('email', 'like', '%' . $searchKey . '%')
                  ->orWhere('payment_deadline', 'like', '%' . $searchKey . '%')
                  ->orWhere('payment_method', 'like', '%' . $searchKey . '%')
                  ->orWhere('nif', 'like', '%' . $searchKey . '%')
                  ->orWhere('rccm', 'like', '%' . $searchKey . '%')
                  ->orWhere('date', 'like', '%' . $searchKey . '%')
                  // Recherche dans les contacts associés
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
     * Liste des clients avec RECHERCHE GLOBALE sur tous les champs
     */
    public function list(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $query = Customer::with('contacts');

        // Application des filtres
        $this->applySearchFilters($query, $searchKey);

        $customers = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.partner.customer.list', compact('customers'));
    }

    /**
     * EXPORT EXCEL UNIQUEMENT
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        // Génération du fichier Excel en passant la clé de recherche à la classe Export
        return Excel::download(new CustomerExport($searchKey), 'liste_clients_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * Page de création
     * Adaptée pour pré-remplir les informations générales ET les contacts
     */
    public function createPage(Request $request)
    {
        $data = [
            // Données générales
            'client_name'       => $request->query('client_name', ''),
            'address'           => $request->query('address', ''),
            'domain'            => $request->query('domain', ''),
            'contact'           => $request->query('contact', ''),
            'email'             => $request->query('email', ''),
            'date'              => $request->query('date', Carbon::now()->format('Y-m-d')),
            
            // ID du prospect pour suppression finale
            'prospect_id'       => $request->query('prospect_id', ''),

            // Tableaux des contacts (pré-remplissage conversion)
            'contact_names'     => $request->query('contact_names', []),
            'contact_positions' => $request->query('contact_positions', []),
            'contact_phones'    => $request->query('contact_phones', []),
            'contact_emails'    => $request->query('contact_emails', []),
        ];

        return view('admin.partner.customer.create', $data);
    }

    // Création client
    public function customerCreate(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'address'          => 'required|string|max:255',
            'domain'           => 'required|string|max:255',
            'contact'          => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'payment_deadline' => 'nullable|string|max:50',
            'payment_method'   => 'nullable|string|max:100',
            'nif'              => 'nullable|string|max:100',
            'rccm'             => 'nullable|string|max:100',
            'date'             => 'nullable|date',
            'prospect_id'      => 'nullable', // Champ caché venant du formulaire
            // Validation des contacts
            'contact_names.*'     => 'nullable|string|max:255',
            'contact_positions.*' => 'nullable|string|max:255',
            'contact_phones.*'    => 'nullable|string|max:255',
            'contact_emails.*'    => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Génération du code client : CUJJMM-XXX
            $dateNow = Carbon::now()->format('dm');
            $lastCustomer = Customer::where('code_client', 'like', "CU{$dateNow}-%")
                ->orderBy('id', 'desc')
                ->first();

            if ($lastCustomer) {
                $lastNumber = intval(substr($lastCustomer->code_client, -3));
                $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '001';
            }

            $validated['code_client'] = "CU{$dateNow}-{$nextNumber}";

            if (empty($validated['contact']) && !empty($request->contact_names[0])) {
                $validated['contact'] = $request->contact_names[0];
            }

            // Création du client
            $customer = Customer::create($validated);

            // Enregistrement des contacts multiples
            if ($request->has('contact_names')) {
                foreach ($request->contact_names as $key => $name) {
                    if (!empty($name) || !empty($request->contact_phones[$key])) {
                        $customer->contacts()->create([
                            'name'     => $name,
                            'position' => $request->contact_positions[$key] ?? null,
                            'phone'    => $request->contact_phones[$key] ?? null,
                            'email'    => $request->contact_emails[$key] ?? null,
                        ]);
                    }
                }
            }

            // Suppression automatique du prospect s'il s'agit d'une conversion
            if ($request->filled('prospect_id')) {
                $prospect = Prospect::find($request->prospect_id);
                if ($prospect) {
                    $prospect->delete(); // Supprime le prospect et ses contacts (cascade)
                }
            }

            DB::commit();
            Alert::success('Insertion réussie', 'Client enregistré et conversion finalisée avec succès');
            return redirect()->route('customerList');

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', "Erreur lors de la création : {$e->getMessage()}");
            return redirect()->back()->withInput();
        }
    }

    // Détails client
    public function details($id)
    {
        $customer = Customer::with('contacts')->findOrFail($id);
        return view('admin.partner.customer.details', compact('customer'));
    }

    // Page édition
    public function edit($id)
    {
        $customer = Customer::with('contacts')->findOrFail($id);
        return view('admin.partner.customer.edit', compact('customer'));
    }

    // Mise à jour
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'               => 'required|exists:customers,id',
            'name'             => 'required|string|max:255',
            'address'          => 'required|string|max:255',
            'domain'           => 'required|string|max:255',
            'contact'          => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'payment_deadline' => 'nullable|string|max:50',
            'payment_method'   => 'nullable|string|max:100',
            'nif'              => 'nullable|string|max:100',
            'rccm'             => 'nullable|string|max:100',
            'date'             => 'nullable|date',
            // Validation des contacts
            'contact_names.*'     => 'nullable|string|max:255',
            'contact_positions.*' => 'nullable|string|max:255',
            'contact_phones.*'    => 'nullable|string|max:255',
            'contact_emails.*'    => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($validated['id']);
            $customer->update($validated);

            // Mise à jour des contacts (Suppression et re-création)
            $customer->contacts()->delete();

            if ($request->has('contact_names')) {
                foreach ($request->contact_names as $key => $name) {
                    if (!empty($name) || !empty($request->contact_phones[$key])) {
                        $customer->contacts()->create([
                            'name'     => $name,
                            'position' => $request->contact_positions[$key],
                            'phone'    => $request->contact_phones[$key],
                            'email'    => $request->contact_emails[$key],
                        ]);
                    }
                }
            }

            DB::commit();
            Alert::success('Mise à jour réussie', 'Client et contacts mis à jour avec succès');
            return redirect()->route('customerList');

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Erreur', "Erreur lors de la mise à jour : {$e->getMessage()}");
            return redirect()->back()->withInput();
        }
    }

    // Suppression
    public function delete($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete(); // Cascade supprime les contacts en DB

            Alert::success('Suppression réussie', 'Client supprimé avec succès');
            return redirect()->route('customerList');

        } catch (\Exception $e) {
            Alert::error('Erreur', "Erreur lors de la suppression : {$e->getMessage()}");
            return redirect()->route('customerList');
        }
    }
}