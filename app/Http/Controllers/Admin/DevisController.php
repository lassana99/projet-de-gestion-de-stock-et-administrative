<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Mail; // Pour l'envoi d'email
use App\Models\Devis;
use App\Models\DevisLine;
use App\Models\Customer;
use App\Models\CustomerContact; // Pour récupérer les contacts
use App\Models\Product;
use App\Mail\SendDevisMail; // Classe Mailable
use Illuminate\Validation\Rule;
// Importations pour l'exportation Excel
use App\Exports\DevisExport;
use Maatwebsite\Excel\Facades\Excel;
// Importation pour l'exportation PDF
use Barryvdh\DomPDF\Facade\Pdf;

class DevisController extends Controller
{
    /**
     * Mappage des statuts FR (Frontend) vers les statuts DB (EN)
     */
    private const STATUS_MAP = [
        'Envoyé'     => 'sent',
        'Accepté'    => 'accepted',
        'Annulé'     => 'rejected',
        'Abandonné'  => 'abandoned',
        'Facturé'    => 'invoiced',
    ];

    /**
     * Conversion FR → DB
     */
    protected function getDbStatus(?string $frenchStatus): string
    {
        return self::STATUS_MAP[$frenchStatus] ?? self::STATUS_MAP['Envoyé'];
    }

    /**
     * Statuts autorisés pour les formulaires (sans Facturé)
     */
    protected function getValidFrenchStatuses(): array
    {
        return array_keys(array_filter(self::STATUS_MAP, fn($v) => $v !== 'invoiced'));
    }

    /**
     * Génération du numéro de devis
     */
    protected function generateDevisNumber(int $id): string
    {
        $day = now()->format('d');
        $month = now()->format('m');
        $sequence = str_pad($id, 4, '0', STR_PAD_LEFT);
        return "Facture Proforma APR{$day}{$month}-{$sequence}";
    }

    /**
     * Marquage automatique comme Facturé
     */
    public function markAsInvoiced(Devis $devis): void
    {
        if ($devis->status !== self::STATUS_MAP['Facturé']) {
            DB::beginTransaction();
            try {
                $devis->status = self::STATUS_MAP['Facturé'];
                $devis->save();
                DB::commit();
                Log::info("Devis {$devis->devis_number} marqué comme Facturé.");
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Erreur facturation devis : " . $e->getMessage());
            }
        }
    }

    //---------------------------------------------------------
    // LISTE AVEC FILTRAGE PAR NUMÉRO OU ÉTAT
    //---------------------------------------------------------
    public function index(Request $request)
    {
        $query = Devis::with('lines');

        if ($request->filled('searchKey')) {
            $searchKey = $request->searchKey;

            $mappedStatus = null;
            foreach (self::STATUS_MAP as $fr => $en) {
                if (mb_strtolower($searchKey) === mb_strtolower($fr)) {
                    $mappedStatus = $en;
                    break;
                }
            }

            if ($mappedStatus) {
                $query->where('status', $mappedStatus);
            } else {
                $query->where(function ($q) use ($searchKey) {
                    $q->where('devis_number', 'like', "%{$searchKey}%")
                      ->orWhere('client', 'like', "%{$searchKey}%");
                });
            }
        }

        $devis = $query->orderByDesc('id')->paginate(10);
        return view('admin.devis.list', compact('devis'));
    }

    /**
     * EXPORT EXCEL UNIQUEMENT
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        // On génère le fichier Excel en passant la clé de recherche à la classe Export
        return Excel::download(new DevisExport($searchKey), 'liste_devis_' . date('d_m_Y_Hi') . '.xlsx');
    }

    //---------------------------------------------------------

    public function create()
    {
        $statuses = $this->getValidFrenchStatuses();
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('reference')->get(); 
        
        return view('admin.devis.create', compact('statuses', 'customers', 'products'));
    }

    //---------------------------------------------------------

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_devis' => 'required|date',
            'client' => 'required|string',
            'code_client' => 'nullable|string|max:50',
            'client_address' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'validity' => 'nullable|string|max:100',
            'delivery_location' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:1',
            'discount' => 'nullable|numeric|min:0|max:100', 
            'status' => 'nullable|string|in:' . implode(',', $this->getValidFrenchStatuses()),
            'lines' => 'required|array|min:1',
            'lines.*.ref_choice' => 'required|string',
            'lines.*.reference' => 'required_if:lines.*.ref_choice,other|nullable|string|max:255',
            'lines.*.product_name' => 'required|string',
            'lines.*.unit_price_ht' => 'required|numeric|min:0',
            'lines.*.quantity' => 'required|integer|min:1',
            'lines.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $dbStatus = $this->getDbStatus($request->status);
            $taxRate = $request->tax_rate ?? 0.18;

            $devis = Devis::create([
                'devis_number' => null,
                'date_devis' => $request->date_devis,
                'client' => $request->client,
                'code_client' => $request->code_client,
                'client_address' => $request->client_address,
                'delivery_terms' => $request->delivery_terms,
                'payment_terms' => $request->payment_terms,
                'validity' => $request->validity,
                'delivery_location' => $request->delivery_location,
                'tax_rate' => $taxRate,
                'discount' => $request->discount ?? 0,
                'status' => $dbStatus,
            ]);

            $devis->devis_number = $this->generateDevisNumber($devis->id);
            $devis->save();

            foreach ($request->lines as $index => $line) {
                // Déterminer la référence finale
                $finalRef = ($line['ref_choice'] === 'other') ? $line['reference'] : $line['ref_choice'];

                // Gestion de l'image (Uniquement si un fichier est uploadé)
                $imagePath = null;
                if ($request->hasFile("lines.{$index}.image")) {
                    $imagePath = $request->file("lines.{$index}.image")->store('devis_images', 'public');
                }

                $devis->lines()->create([
                    'product_name' => $line['product_name'],
                    'reference' => $finalRef,
                    'image' => $imagePath,
                    'unit_price_ht' => (float)$line['unit_price_ht'],
                    'quantity' => (int)$line['quantity'],
                    'total_ht' => (float)$line['unit_price_ht'] * (int)$line['quantity'],
                ]);
            }

            if (method_exists($devis, 'calculateTotals')) {
                $devis->calculateTotals();
            }

            DB::commit();
            return redirect()->route('devis.list')->with('success', 'Devis créé avec succès.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Erreur création devis : " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création')->withInput();
        }
    }

    //---------------------------------------------------------

    public function show(Devis $devis)
    {
        $devis->load('lines');
        return view('admin.devis.details', compact('devis'));
    }

    //---------------------------------------------------------

    public function edit(Devis $devis)
    {
        if ($devis->status === 'invoiced') {
            return redirect()->route('devis.details', $devis)
                ->with('error', 'Ce devis est déjà facturé.');
        }

        $devis->load('lines');
        $statuses = $this->getValidFrenchStatuses();
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('reference')->get(); 

        return view('admin.devis.edit', compact('devis', 'statuses', 'customers', 'products'));
    }

    //---------------------------------------------------------

    public function update(Request $request, Devis $devis)
    {
        if ($devis->status === 'invoiced') {
            return redirect()->route('devis.details', $devis)
                ->with('error', 'Modification impossible : devis facturé.');
        }

        $validator = Validator::make($request->all(), [
            'date_devis' => 'required|date',
            'client' => 'required|string',
            'status' => ['required', Rule::in($this->getValidFrenchStatuses())],
            'lines' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $devis->update([
                'date_devis' => $request->date_devis,
                'client' => $request->client,
                'code_client' => $request->code_client,
                'client_address' => $request->client_address,
                'delivery_terms' => $request->delivery_terms,
                'payment_terms' => $request->payment_terms,
                'validity' => $request->validity,
                'delivery_location' => $request->delivery_location,
                'tax_rate' => $request->tax_rate ?? 0.18,
                'discount' => $request->discount ?? 0,
                'status' => $this->getDbStatus($request->status),
            ]);

            if ($request->has('lines')) {
                // Collecte des images à conserver
                $imagesToKeep = [];
                foreach ($request->lines as $l) {
                    if (!empty($l['old_image'])) $imagesToKeep[] = $l['old_image'];
                }

                // Nettoyage des images physiques qui ne sont plus utilisées
                foreach ($devis->lines as $oldLine) {
                    if ($oldLine->image && !in_array($oldLine->image, $imagesToKeep)) {
                        Storage::disk('public')->delete($oldLine->image);
                    }
                }
                
                $devis->lines()->delete();

                foreach ($request->lines as $index => $line) {
                    $finalRef = ($line['ref_choice'] === 'other') ? ($line['reference'] ?? 'SANS_REF') : $line['ref_choice'];
                    $imagePath = null;
                    
                    if ($request->hasFile("lines.{$index}.image")) {
                        // Nouveau fichier uploadé
                        $imagePath = $request->file("lines.{$index}.image")->store('devis_images', 'public');
                    } elseif (!empty($line['old_image'])) {
                        // On garde l'ancienne image du devis si pas de nouvel upload
                        $imagePath = $line['old_image'];
                    }

                    $devis->lines()->create([
                        'product_name' => $line['product_name'],
                        'reference' => $finalRef,
                        'image' => $imagePath,
                        'unit_price_ht' => (float)$line['unit_price_ht'],
                        'quantity' => (int)$line['quantity'],
                        'total_ht' => (float)$line['unit_price_ht'] * (int)$line['quantity'],
                    ]);
                }
            }

            if (method_exists($devis, 'calculateTotals')) {
                $devis->calculateTotals();
            }

            DB::commit();
            return redirect()->route('devis.list')->with('success', 'Devis mis à jour avec succès.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Erreur mise à jour devis : " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la mise à jour')->withInput();
        }
    }

    //---------------------------------------------------------

    public function destroy(Devis $devis)
    {
        if ($devis->status === 'invoiced') {
            return back()->with('error', 'Suppression impossible : devis facturé.');
        }

        DB::beginTransaction();
        try {
            foreach ($devis->lines as $line) {
                if ($line->image) {
                    Storage::disk('public')->delete($line->image);
                }
            }

            $devis->lines()->delete();
            $devis->delete();
            DB::commit();

            return redirect()->route('devis.list')->with('success', 'Devis supprimé avec succès.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Erreur suppression devis : " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    //---------------------------------------------------------

    public function pdf(Devis $devis)
    {
        $devis->load('lines');
        
        // On génère le PDF à partir de la vue 'admin.devis.pdf'
        $pdf = Pdf::loadView('admin.devis.pdf', compact('devis'))
                  ->setPaper('a4', 'portrait');

        // On retourne le fichier pour téléchargement
        return $pdf->download('Devis_' . $devis->devis_number . '.pdf');
    }

    //---------------------------------------------------------

    public function print(Devis $devis)
    {
        $devis->load('lines');
        return view('admin.devis.print', compact('devis'));
    }

    /**
     * Envoi du devis par email à un ou plusieurs contacts sélectionnés
     */
    public function sendEmail(Request $request, Devis $devis)
    {
        $request->validate([
            'contact_ids' => 'required|array|min:1',
            'contact_ids.*' => 'exists:customer_contacts,id',
        ]);

        try {
            // Récupération des contacts sélectionnés ayant une adresse email
            $contacts = CustomerContact::whereIn('id', $request->contact_ids)->get();
            $recipients = $contacts->pluck('email')->filter()->toArray();
            
            if (empty($recipients)) {
                return back()->with('error', "Aucun des contacts sélectionnés n'a d'adresse email renseignée.");
            }

            // Détermination du nom à afficher dans le message
            // Si 1 seul contact est sélectionné, on utilise son nom. 
            // Si plusieurs sont sélectionnés, on passe null pour afficher "Madame, Monsieur"
            $recipientName = ($contacts->count() === 1) ? $contacts->first()->name : null;

            // 1. Générer le PDF en mémoire une seule fois
            $devis->load('lines');
            $pdf = Pdf::loadView('admin.devis.pdf', compact('devis'))
                      ->setPaper('a4', 'portrait');
            $pdfContent = $pdf->output();

            // 2. Envoyer l'email groupé
            Mail::to($recipients)->send(new SendDevisMail($devis, $pdfContent, $recipientName));

            return back()->with('success', "Le devis a été envoyé avec succès à " . count($recipients) . " contact(s).");

        } catch (\Exception $e) {
            Log::error("Erreur envoi email devis : " . $e->getMessage());
            return back()->with('error', "Une erreur est survenue lors de l'envoi de l'email.");
        }
    }
}