<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert; // Importation de la façade SweetAlert
// Importations pour l'exportation Excel
use App\Exports\PaymentExport;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    /**
     * Nettoyage des montants (retrait des espaces pour la base de données)
     */
    protected function cleanAmount($value)
    {
        if (!$value) return 0;
        return str_replace(' ', '', $value);
    }

    /**
     * Liste des paiements avec filtrage
     */
    public function list(Request $request)
    {
        $query = Payment::query();

        if ($request->filled('searchKey')) {
            $key = $request->searchKey;
            $query->where('invoice_number', 'like', "%$key%")
                  ->orWhere('client_name', 'like', "%$key%");
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(10);

        return view('admin.paiement.list', compact('payments'));
    }

    /**
     * EXPORT EXCEL UNIQUEMENT
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        // Génération du fichier Excel en passant la clé de recherche à la classe Export
        return Excel::download(new PaymentExport($searchKey), 'liste_paiements_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $invoices = Invoice::orderBy('invoice_number', 'desc')->get();
        return view('admin.paiement.create', compact('invoices'));
    }

    /**
     * Enregistrement du paiement
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'      => 'required', // ID réel ou "other"
            'amount_htva'     => 'required', 
            'payment_method'  => 'required|string',
            'payment_date'    => 'required|date',
            'manual_invoice_number' => 'required_if:invoice_id,other',
            'manual_client_name'    => 'required_if:invoice_id,other',
        ]);

        $amountHtva = $this->cleanAmount($request->amount_htva);
        
        // Logique de récupération des infos facture / client (Gestion du cas "Autres")
        if ($request->invoice_id === 'other') {
            $invoiceId     = null;
            $invoiceNumber = $request->manual_invoice_number;
            $clientName    = $request->manual_client_name;
        } else {
            $invoice       = Invoice::findOrFail($request->invoice_id);
            $invoiceId     = $invoice->id;
            $invoiceNumber = $invoice->invoice_number;
            $clientName    = $invoice->client;
        }

        // Logique pour le mode de paiement "Autres"
        $method = $request->payment_method;
        if ($method === 'Autres' && $request->filled('other_method')) {
            $method = $request->other_method;
        }

        Payment::create([
            'invoice_id'     => $invoiceId,
            'invoice_number' => $invoiceNumber,
            'client_name'    => $clientName,
            'amount_htva'    => $amountHtva,
            'payment_method' => $method,
            'payment_date'   => $request->payment_date,
        ]);

        Alert::success('Succès', 'Le paiement a été enregistré avec succès.');
        return redirect()->route('paymentList');
    }

    /**
     * Détails d'un paiement
     */
    public function details($id)
    {
        $payment = Payment::findOrFail($id);
        return view('admin.paiement.details', compact('payment'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        $payment = Payment::findOrFail($id);
        $invoices = Invoice::all();
        return view('admin.paiement.edit', compact('payment', 'invoices'));
    }

    /**
     * Mise à jour du paiement
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        
        $request->validate([
            'invoice_id'      => 'required',
            'amount_htva'     => 'required',
            'payment_method'  => 'required|string',
            'payment_date'    => 'required|date',
            'manual_invoice_number' => 'required_if:invoice_id,other',
            'manual_client_name'    => 'required_if:invoice_id,other',
        ]);

        $amountHtva = $this->cleanAmount($request->amount_htva);

        if ($request->invoice_id === 'other') {
            $invoiceId     = null;
            $invoiceNumber = $request->manual_invoice_number;
            $clientName    = $request->manual_client_name;
        } else {
            $invoice       = Invoice::findOrFail($request->invoice_id);
            $invoiceId     = $invoice->id;
            $invoiceNumber = $invoice->invoice_number;
            $clientName    = $invoice->client;
        }

        $method = $request->payment_method;
        if ($method === 'Autres' && $request->filled('other_method')) {
            $method = $request->other_method;
        }

        $payment->update([
            'invoice_id'     => $invoiceId,
            'invoice_number' => $invoiceNumber,
            'client_name'    => $clientName,
            'amount_htva'    => $amountHtva,
            'payment_method' => $method,
            'payment_date'   => $request->payment_date,
        ]);

        Alert::success('Mis à jour', 'Les informations du paiement ont été modifiées.');
        return redirect()->route('paymentList');
    }

    /**
     * Suppression d'un paiement
     */
    public function delete($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        Alert::success('Supprimé', 'Le paiement a été supprimé de la base de données.');
        return back();
    }
}