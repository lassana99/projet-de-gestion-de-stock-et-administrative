<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Invoice; 
use App\Models\Customer;
use App\Models\CustomerContact; // Ajouté pour l'envoi
use App\Models\Product;
use App\Models\InvoiceLine;
use App\Mail\SendInvoiceMail; // Ajouté pour l'envoi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail; // Ajouté pour l'envoi
use Illuminate\Validation\Rule;
use Carbon\Carbon;
// Importations pour l'exportation Excel
use App\Exports\InvoiceExport;
use Maatwebsite\Excel\Facades\Excel;
// Importation pour l'exportation PDF
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Génère le numéro de facture au format "Facture APRjjmm-xxxx"
     */
    protected function generateFactureNumber(int $id): string
    {
        $now = Carbon::now();
        $day = $now->format('d');
        $month = $now->format('m');
        $sequence = str_pad($id, 4, '0', STR_PAD_LEFT);

        return "Facture APR{$day}{$month}-{$sequence}";
    }

    //---------------------------------------------------------

    public function index()
    {
        $searchKey = request('searchKey');
        $query = Invoice::query();

        if ($searchKey) {
            $query->where('invoice_number', 'like', '%' . $searchKey . '%')
                  ->orWhere('client', 'like', '%' . $searchKey . '%');
        }

        $invoices = $query->latest()
                          ->paginate(10)
                          ->appends(['searchKey' => $searchKey]);

        return view('admin.invoices.list', compact('invoices'));
    }

    /**
     * EXPORT EXCEL UNIQUEMENT
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        // Génération du fichier Excel en passant la clé de recherche à la classe Export
        return Excel::download(new InvoiceExport($searchKey), 'liste_factures_' . date('d_m_Y_Hi') . '.xlsx');
    }

    //---------------------------------------------------------

    public function create()
    {
        $validDevis = Devis::where('status', 'accepted')
                            ->whereDoesntHave('invoice')
                            ->get();

        $customers = Customer::all();

        return view('admin.invoices.create', compact('validDevis', 'customers'));
    }

    //---------------------------------------------------------

    public function store(Request $request)
    {
        $request->validate([
            'devis_id'     => 'required|exists:devis,id',
            'date_invoice' => 'required|date',
            'status'       => 'required|string|in:pending,sent,paid',
        ]);

        $devis = Devis::with('lines')->findOrFail($request->devis_id);

        // 🔹 SÉCURITÉ : Calcul manuel si le devis source est mal renseigné
        $total_ht = $devis->total_ht;
        $discount_percent = $devis->discount ?? 0;

        $total_htva = ($devis->total_htva > 0) 
            ? $devis->total_htva 
            : ($total_ht - ($total_ht * $discount_percent / 100));

        $invoice = null;

        DB::transaction(function () use ($request, $devis, $total_htva, &$invoice) {

            /* 1️⃣ Création de la facture */
            $invoice = Invoice::create([
                'devis_id'         => $devis->id,
                'invoice_number'   => 'TEMP_ID_' . $devis->id,
                'date_invoice'     => $request->date_invoice,
                'code_client'      => $devis->code_client,
                'client'           => $devis->client,
                'client_address'   => $devis->client_address,
                'total_ht'         => $devis->total_ht,
                'discount'         => $devis->discount ?? 0,
                'total_htva'       => $total_htva,
                'total_tva'        => $devis->total_tva,
                'total_ttc'        => $devis->total_ttc,
                'tax_rate'         => $devis->tax_rate,
                'delivery_terms'   => $devis->delivery_terms,
                'payment_terms'    => $devis->payment_terms,
                'delivery_location'=> $devis->delivery_location,
                'signatory_name'   => $devis->signatory_name,
                'status'           => $request->status,
            ]);

            /* 2️⃣ Numéro définitif */
            $invoice->update([
                'invoice_number' => $this->generateFactureNumber($invoice->id)
            ]);

            /* 3️⃣ Copie des lignes (Inclusion de reference et image du devis) */
            foreach ($devis->lines as $line) {
                $invoice->lines()->create([
                    'product_name'  => $line->product_name,
                    'reference'     => $line->reference,
                    'image'         => $line->image,
                    'quantity'      => $line->quantity,
                    'unit_price_ht' => $line->unit_price_ht,
                    'total_ht'      => $line->total_ht,
                ]);
            }

            /* 4️⃣ Marquer le devis comme facturé */
            $devis->update(['status' => 'invoiced']);
        });

        return redirect()
            ->route('invoices.details', $invoice->id)
            ->with('success', 'Facture créée avec succès : ' . $invoice->invoice_number);
    }

    //---------------------------------------------------------

    public function edit(Invoice $invoice)
    {
        $invoice->load(['lines', 'devis']);
        $validDevis = Devis::where('status', 'accepted')
            ->whereDoesntHave('invoice')
            ->orWhere('id', $invoice->devis_id)
            ->get();
        $customers = Customer::all();
        
        // Ajout de la liste des produits pour le menu déroulant des références dans edit
        $products = Product::orderBy('reference')->get();

        return view('admin.invoices.edit', compact('invoice', 'customers', 'validDevis', 'products'));
    }

    //---------------------------------------------------------

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'devis_id'     => ['nullable', 'exists:devis,id', Rule::unique('invoices')->ignore($invoice->id)],
            'date_invoice' => 'required|date',
            'status'       => ['required', Rule::in(['pending','sent','paid','partially_paid','cancelled','canceled','overdue','draft'])],
            'lines'        => 'nullable|array',
        ]);

        $oldDevisId = $invoice->devis_id;
        $newDevisId = $request->devis_id;

        DB::transaction(function () use ($request, $invoice, $oldDevisId, $newDevisId) {

            // CAS 1 : Changement de Devis source
            if ($newDevisId && $newDevisId != $oldDevisId) {
                if ($oldDevisId) {
                    Devis::where('id', $oldDevisId)->update(['status' => 'accepted']);
                }

                $newDevis = Devis::with('lines')->findOrFail($newDevisId);
                $newDevis->update(['status' => 'invoiced']);

                $calc_htva = ($newDevis->total_htva > 0) 
                    ? $newDevis->total_htva 
                    : ($newDevis->total_ht - ($newDevis->total_ht * ($newDevis->discount ?? 0) / 100));

                $invoice->update([
                    'devis_id'       => $newDevis->id,
                    'code_client'    => $newDevis->code_client,
                    'client'         => $newDevis->client,
                    'client_address' => $newDevis->client_address,
                    'total_ht'       => $newDevis->total_ht,
                    'discount'       => $newDevis->discount ?? 0,
                    'total_htva'     => $calc_htva,
                    'total_tva'      => $newDevis->total_tva,
                    'total_ttc'      => $newDevis->total_ttc,
                    'tax_rate'       => $newDevis->tax_rate,
                ]);

                // Suppression des images physiques liées à la facture avant de supprimer les lignes
                foreach ($invoice->lines as $line) {
                    if ($line->image && !str_starts_with($line->image, 'catalog:')) {
                        Storage::disk('public')->delete($line->image);
                    }
                }

                $invoice->lines()->delete();
                foreach ($newDevis->lines as $line) {
                    $invoice->lines()->create([
                        'product_name'  => $line->product_name,
                        'reference'     => $line->reference,
                        'image'         => $line->image,
                        'quantity'      => $line->quantity,
                        'unit_price_ht' => $line->unit_price_ht,
                        'total_ht'      => $line->total_ht,
                    ]);
                }
            } 
            // CAS 2 : Mise à jour manuelle des lignes (si pas de changement de devis)
            elseif ($request->has('lines')) {
                
                $imagesToKeep = [];
                foreach ($request->lines as $l) {
                    if (!empty($l['old_image'])) $imagesToKeep[] = $l['old_image'];
                }

                foreach ($invoice->lines as $oldLine) {
                    if ($oldLine->image && !str_starts_with($oldLine->image, 'catalog:') && !in_array($oldLine->image, $imagesToKeep)) {
                        Storage::disk('public')->delete($oldLine->image);
                    }
                }

                $invoice->lines()->delete();

                foreach ($request->lines as $index => $line) {
                    $finalRef = ($line['ref_choice'] === 'other') ? ($line['reference'] ?? 'REF') : $line['ref_choice'];
                    $imagePath = null;
                    
                    if ($request->hasFile("lines.{$index}.image")) {
                        $imagePath = $request->file("lines.{$index}.image")->store('invoice_images', 'public');
                    } elseif (!empty($line['old_image'])) {
                        $imagePath = $line['old_image'];
                    }

                    $invoice->lines()->create([
                        'product_name'  => $line['product_name'],
                        'reference'     => $finalRef,
                        'image'         => $imagePath,
                        'quantity'      => (int)$line['quantity'],
                        'unit_price_ht' => (float)$line['unit_price_ht'],
                        'total_ht'      => (float)$line['unit_price_ht'] * (int)$line['quantity'],
                    ]);
                }
            }

            // Mise à jour des informations générales
            $invoice->update([
                'date_invoice' => $request->date_invoice,
                'status'       => $request->status,
            ]);

            if (method_exists($invoice, 'calculateTotals')) {
                $invoice->calculateTotals();
            }
        });

        return redirect()
            ->route('invoices.details', $invoice)
            ->with('success', 'Facture mise à jour avec succès.');
    }

    //---------------------------------------------------------

    public function show(Invoice $invoice)
    {
        $invoice->load(['lines', 'devis']);
        return view('admin.invoices.details', compact('invoice'));
    }

    //---------------------------------------------------------

    public function destroy(Invoice $invoice)
    {
        DB::beginTransaction();
        try {
            if ($invoice->devis) {
                $invoice->devis->update(['status' => 'accepted']);
            }

            // Suppression des images physiques
            foreach ($invoice->lines as $line) {
                if ($line->image && !str_starts_with($line->image, 'catalog:')) {
                    Storage::disk('public')->delete($line->image);
                }
            }

            $invoice->lines()->delete();
            $invoice->delete();
            
            DB::commit();
            return redirect()->route('invoices.list')->with('success', 'Facture supprimée.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    //---------------------------------------------------------

    /**
     * Génération professionnelle du PDF (Ouverture en navigateur)
     */
    public function pdf(Invoice $invoice)
    {
        $invoice->load('lines');
        
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'))
                  ->setPaper('a4', 'portrait');

        /**
         * On utilise stream() au lieu de download().
         * Cela ouvre le PDF dans le lecteur natif du navigateur. 
         * C'est plus professionnel et évite l'interception forcée par les download managers.
         */
        return $pdf->stream('Facture_' . $invoice->invoice_number . '.pdf');
    }

    //---------------------------------------------------------

    public function print(Invoice $invoice)
    {
        $invoice->load('lines');
        return view('admin.invoices.print', compact('invoice'));
    }

    /**
     * NOUVEAU : Envoi de la facture par email à un ou plusieurs contacts sélectionnés
     */
    public function sendEmail(Request $request, Invoice $invoice)
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
            $invoice->load('lines');
            $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'))
                      ->setPaper('a4', 'portrait');
            $pdfContent = $pdf->output();

            // 2. Envoyer l'email groupé
            Mail::to($recipients)->send(new SendInvoiceMail($invoice, $pdfContent, $recipientName));

            return back()->with('success', "La facture a été envoyée avec succès à " . count($recipients) . " contact(s).");

        } catch (\Exception $e) {
            Log::error("Erreur envoi email facture : " . $e->getMessage());
            return back()->with('error', "Une erreur est survenue lors de l'envoi de l'email.");
        }
    }
}