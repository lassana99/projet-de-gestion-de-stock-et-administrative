<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteLine;
use App\Models\Customer;
use App\Models\CustomerContact; // Ajouté pour l'envoi
use App\Mail\SendDeliveryNoteMail; // Ajouté (Classe Mailable)
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // Ajouté pour l'envoi
use Carbon\Carbon;
// Importations pour l'exportation Excel
use App\Exports\DeliveryNoteExport;
use Maatwebsite\Excel\Facades\Excel;
// Importation pour l'exportation PDF professionnelle
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryNoteController extends Controller
{
    /**
     * Génération du numéro de Bordereau de Livraison basée sur l'ID
     */
    protected function generateDeliveryNoteNumber(int $id): string
    {
        $day = now()->format('d');
        $month = now()->format('m');
        $sequence = str_pad($id, 4, '0', STR_PAD_LEFT);
        return "APR{$day}{$month}-{$sequence}";
    }

    /**
     * Centralisation de la logique de filtrage (Web + Excel)
     */
    private function applyFilters($query, $search)
    {
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('delivery_note_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('purchase_order_number', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Affiche la liste de tous les BL avec support de recherche.
     */
    public function index(Request $request)
    {
        // On initialise la requête avec la relation facture
        $query = DeliveryNote::with('invoice');

        // Application des filtres
        $this->applyFilters($query, $request->searchKey);

        // Pagination en conservant les paramètres de recherche dans les liens
        $deliveryNotes = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.delivery_notes.list', compact('deliveryNotes'));
    }

    /**
     * EXPORT EXCEL
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        return Excel::download(new DeliveryNoteExport($searchKey), 'liste_bordereaux_livraison_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * EXPORT PDF PROFESSIONNEL (Ouverture dans le navigateur)
     */
    public function pdf(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load('lines');
        
        $pdf = Pdf::loadView('admin.delivery_notes.pdf', compact('deliveryNote'))
                  ->setPaper('a4', 'portrait');

        // On utilise stream() pour que le PDF s'ouvre dans le lecteur natif du navigateur
        return $pdf->stream('BL_' . $deliveryNote->delivery_note_number . '.pdf');
    }

    /**
     * Sélection de la facture pour nouveau BL.
     */
    public function create()
    {
        $invoices = Invoice::orderBy('invoice_number', 'desc')->get(); 
        return view('admin.delivery_notes.select_invoice', compact('invoices'));
    }

    /**
     * Formulaire de création basé sur une facture.
     */
    public function createFromInvoice(int $invoiceId)
    {
        $invoice = Invoice::with('lines')->findOrFail($invoiceId);

        // Récupérer le prochain ID probable pour l'affichage
        $nextId = (DeliveryNote::max('id') ?? 0) + 1;
        $deliveryNoteNumber = $this->generateDeliveryNoteNumber($nextId);
        
        return view('admin.delivery_notes.create', [
            'invoice' => $invoice,
            'deliveryNoteNumber' => $deliveryNoteNumber,
        ]);
    }

    /**
     * Enregistre le BL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'client_name' => 'required|string',
            'client_address' => 'nullable|string',
            'code_client' => 'nullable|string',
            'purchase_order_number' => 'nullable|string',
            'date_delivery' => 'required|date',
            'delivery_location' => 'nullable|string',
            'line_quantities' => 'required|array',
            'line_observations' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // 1. Création de l'entête
            $deliveryNote = DeliveryNote::create([
                'invoice_id' => $request->invoice_id,
                'delivery_note_number' => 'TEMP-' . uniqid(), 
                'client_name' => $request->client_name,
                'client_address' => $request->client_address,
                'code_client' => $request->code_client,
                'purchase_order_number' => $request->purchase_order_number,
                'date_delivery' => $request->date_delivery,
                'delivery_location' => $request->delivery_location,
            ]);

            // 2. Attribution du numéro final
            $deliveryNote->delivery_note_number = $this->generateDeliveryNoteNumber($deliveryNote->id);
            $deliveryNote->save();

            // 3. Création des lignes (Récupération des infos depuis la facture)
            $invoice = Invoice::with('lines')->findOrFail($request->invoice_id);
            foreach ($invoice->lines as $invoiceLine) {
                $quantityDelivered = $request->line_quantities[$invoiceLine->id] ?? $invoiceLine->quantity;
                $observation = $request->line_observations[$invoiceLine->id] ?? null;

                DeliveryNoteLine::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'product_name' => $invoiceLine->product_name,
                    'reference' => $invoiceLine->reference,
                    'image' => $invoiceLine->image,
                    'quantity_ordered' => $invoiceLine->quantity,
                    'quantity_delivered' => (int) $quantityDelivered,
                    'observation' => $observation,
                ]);
            }

            DB::commit();
            return redirect()->route('delivery_notes.show', $deliveryNote)
                             ->with('success', 'Le Bordereau ' . $deliveryNote->delivery_note_number . ' a été généré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création BL : " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération du Bordereau.')->withInput();
        }
    }

    /**
     * Affiche le BL (Détails).
     */
    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load('lines'); 
        return view('admin.delivery_notes.details', compact('deliveryNote'));
    }
    
    /**
     * Formulaire de Modification.
     */
    public function edit(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['lines', 'invoice']); 
        return view('admin.delivery_notes.edit', compact('deliveryNote'));
    }

    /**
     * Mise à jour du BL.
     */
    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        $request->validate([
            'delivery_note_number' => 'required|unique:delivery_notes,delivery_note_number,' . $deliveryNote->id,
            'date_delivery' => 'required|date',
            'delivery_location' => 'nullable|string',
            'line_quantities' => 'required|array', 
            'line_observations' => 'nullable|array',
            'line_ids' => 'required|array', 
        ]);

        DB::beginTransaction();
        try {
            $deliveryNote->update([
                'delivery_note_number' => $request->delivery_note_number,
                'date_delivery' => $request->date_delivery,
                'delivery_location' => $request->delivery_location,
            ]);

            foreach ($request->line_ids as $lineId) {
                $quantityDelivered = $request->line_quantities[$lineId] ?? 0;
                $observation = $request->line_observations[$lineId] ?? null;

                $line = DeliveryNoteLine::findOrFail($lineId);
                
                $line->update([
                    'quantity_delivered' => (int) $quantityDelivered,
                    'observation' => $observation,
                ]);
            }

            DB::commit();
            return redirect()->route('delivery_notes.show', $deliveryNote)->with('success', 'Bordereau mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur mise à jour BL : " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la mise à jour.');
        }
    }
    
    /**
     * Suppression du BL.
     */
    public function destroy(DeliveryNote $deliveryNote)
    {
        try {
            DB::beginTransaction();
            $deliveryNote->lines()->delete();
            $deliveryNote->delete();
            DB::commit();
            return redirect()->route('delivery_notes.list')->with('success', 'Bordereau de Livraison supprimé.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur suppression BL : " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * NOUVEAU : Envoi du bordereau par email à un ou plusieurs contacts sélectionnés
     */
    public function sendEmail(Request $request, DeliveryNote $deliveryNote)
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
            $deliveryNote->load('lines');
            $pdf = Pdf::loadView('admin.delivery_notes.pdf', compact('deliveryNote'))
                      ->setPaper('a4', 'portrait');
            $pdfContent = $pdf->output();

            // 2. Envoyer l'email groupé
            Mail::to($recipients)->send(new SendDeliveryNoteMail($deliveryNote, $pdfContent, $recipientName));

            return back()->with('success', "Le Bordereau a été envoyé avec succès à " . count($recipients) . " contact(s).");

        } catch (\Exception $e) {
            Log::error("Erreur envoi email BL : " . $e->getMessage());
            return back()->with('error', "Une erreur est survenue lors de l'envoi de l'email.");
        }
    }
}