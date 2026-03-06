@extends('admin.layouts.master')

@section('content')
<div class="container-fluid"> <!-- Cette balise protège votre menu latéral -->

    <style>
        /* ==========================================================================
           PARAMÈTRES LOCAUX
           ========================================================================== */
        .invoice-scope {
            --font-main: 'Times New Roman', Times, serif;
            --color-text: #000000;
            --color-primary: #1e3a8a;    /* Bleu roi */
            --color-bg-header: #f3f4f6;
            --color-border: #9ca3af;
            
            font-family: var(--font-main);
        }

        /* ==========================================================================
           STYLE D'ÉCRAN
           ========================================================================== */
        .document-wrapper {
            background-color: #e5e7eb;
            padding: 30px;
            width: 100%;
            min-height: 80vh; 
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 8px;
        }

        .sheet {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 10mm 15mm 15mm 15mm; 
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            box-sizing: border-box;
            font-size: 12pt; 
            color: var(--color-text);
            margin-bottom: 20px;
        }

        /* ==========================================================================
           EN-TÊTE & INFO
           ========================================================================== */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px; 
            border-bottom: 2px solid var(--color-primary);
            padding-bottom: 5px;
        }
        .logo img { max-width: 180px; height: auto; }
        
        .doc-info { text-align: right; }
        .doc-info h1 { 
            color: var(--color-primary); 
            margin: 0; font-size: 20pt; 
            text-transform: uppercase; 
        }
        .doc-info p { margin: 1px 0; font-size: 11pt; }

        .addresses {
            display: flex;
            gap: 20px;
            margin-bottom: 20px; 
        }
        .addr-block {
            flex: 1;
            border: 1px solid var(--color-border);
            border-radius: 4px;
        }
        .addr-title {
            background: var(--color-bg-header);
            padding: 5px 10px;
            font-size: 11pt;
            text-transform: uppercase;
            border-bottom: 1px solid var(--color-border);
            font-weight: bold !important;
            color: var(--color-primary) !important;
            -webkit-print-color-adjust: exact;
        }
        .addr-text {
            padding: 8px 10px;
            font-size: 12pt; 
            line-height: 1.3;
        }

        /* ==========================================================================
           TABLEAU DES ARTICLES
           ========================================================================== */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            table-layout: fixed;
        }
        table.items-table th {
            background-color: var(--color-primary) !important;
            color: white !important;
            padding: 8px 6px;
            font-size: 11pt; 
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid var(--color-primary);
            -webkit-print-color-adjust: exact;
        }
        table.items-table td {
            border: 1px solid var(--color-border);
            font-size: 11pt;
            padding: 6px 4px; 
            vertical-align: middle; 
            line-height: 1.2;
            word-wrap: break-word;
        }
        
        /* Ajustement dynamique des colonnes */
        .c-ref  { width: 12%; text-align: left; font-size: 11pt !important; }
        .c-pu   { width: 15%; text-align: right; }
        .c-qty  { width: 8%;  text-align: center; }
        .c-tot  { width: 20%; text-align: right; font-weight: bold; }

        .product-img {
            max-width: 90px;
            max-height: 90px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* ==========================================================================
           BLOCS TOTAUX
           ========================================================================== */
        .totals-wrapper {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-top: 10px;
        }
        table.totals {
            width: 50%;
            border-collapse: collapse;
        }
        table.totals td {
            padding: 4px 10px;
            text-align: right;
            border-bottom: 1px solid #eee;
            font-size: 12pt;
        }
        .t-label { font-weight: bold; color: #444; }
        .t-value { font-weight: bold; color: #000; }
        
        .row-ttc td {
            background-color: var(--color-primary) !important;
            color: white !important;
            font-size: 13pt;
            font-weight: bold;
            border: none;
            padding: 8px 10px;
            -webkit-print-color-adjust: exact;
        }

        .amount-in-words {
            margin-top: 10px;
            font-size: 11pt;
            font-style: italic;
            text-align: left;
            width: 100%;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .conditions-signatures-wrapper { margin-top: 15px; }

        .conditions {
            padding: 8px;
            border: 1px dashed var(--color-border);
            background: #fdfdfd;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 12pt;
            line-height: 1.4;
        }
        .cond-col { flex: 1; min-width: 200px; }

        .signatures {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 10px;
            font-size: 12pt;
        }
        .sign-block { width: 40%; }
        .sign-header { font-weight: bold; margin-bottom: 5px; text-decoration: underline; }
        .sign-label { font-style: italic; margin-bottom: 50px; }

        /* Footer Fixe */
        .footer-fixed {
            position: absolute;
            bottom: 8mm;
            left: 0; right: 0;
            text-align: center;
            border-top: 1px solid var(--color-primary);
            padding-top: 4px;
            background: white;
            font-size: 8pt; 
            width: 90%; 
            margin: 0 auto;
        }
        .footer-line {
            display: flex;
            justify-content: center;
            flex-wrap: nowrap;
            gap: 8px;
        }

        @media print {
            body * { visibility: hidden; }
            .invoice-scope, .invoice-scope * { visibility: visible; }
            .invoice-scope { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
            .document-wrapper { background: white; padding: 0; margin: 0; display: block; }
            .sheet { width: 100% !important; box-shadow: none; margin: 0; border: none; page-break-after: always; padding: 10mm 20mm 10mm 20mm !important; }
            .sheet:last-child { page-break-after: auto; }
            .no-print { display: none !important; }
            .footer-fixed { position: fixed; bottom: 5mm; left: 20mm; right: 20mm; width: auto; }
            .page-break { page-break-before: always !important; display: block; height: 1px; }
            .print-top-spacing { display: block; height: 25mm; width: 100%; }
        }

        /* BOUTONS */
        .btn-group { display: flex; gap: 10px; margin-bottom: 20px; }
        .btn-custom { padding: 8px 15px; border-radius: 5px; color: white; text-decoration: none; font-weight: bold; border:none; cursor: pointer; font-family: sans-serif; font-size: 14px; }
        .btn-edit { background: #2563eb; }
        .btn-print { background: #4b5563; }
        .btn-back { background: #dc2626; }
        .btn-pdf { background: #e74c3c; } 
        /* Nouveau style bouton envoyer */
        .btn-send { background: #059669; }

    </style>

    <div class="invoice-scope">
        <div class="document-wrapper">

            <div class="no-print btn-group">
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn-custom btn-edit">✏️ Modifier</a>
                
                {{-- BOUTON ENVOYER (Ouvre la modale) --}}
                <button type="button" class="btn-custom btn-send" data-toggle="modal" data-target="#sendInvoiceModal">
                    ✈️ Envoyer
                </button>

                {{-- BOUTON EXPORT PDF AVEC PRÉVISUALISATION PROFESSIONNELLE --}}
                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn-custom btn-pdf" target="_blank">📄 Exporter PDF</a>
                
                <button onclick="window.print()" class="btn-custom btn-print">🖨️ Imprimer</button>
                <a href="{{ route('invoices.list') }}" class="btn-custom btn-back">🔙 Retour</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success no-print w-100 mb-2">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger no-print w-100 mb-2">{{ session('error') }}</div>
            @endif

            <div class="sheet">
                
                <div class="header">
                    <div class="logo">
                        <img src="{{ asset('adminProfile/company-logo.png') }}" alt="Approlog">
                    </div>
                    <div class="doc-info">
                        <h1>FACTURE</h1>
                        <p><strong>N° :</strong> {{ $invoice->invoice_number }}</p>
                        <p><strong>Date :</strong> {{ $invoice->date_invoice->format('d/m/Y') }}</p>
                        <p><strong>Client Code :</strong> {{ $invoice->code_client ?? '—' }}</p>
                    </div>
                </div>

                <div class="addresses">
                    <div class="addr-block">
                        <div class="addr-title">Émetteur</div>
                        <div class="addr-text">
                            <strong>Approlog</strong><br>
                            Hamdallaye ACI 2000, Rue 1691, Porte 360 <br>
                            Immeuble Kanté, Bamako-Mali<br>
                            Tél: (00223) 66 75 05 73
                        </div>
                    </div>
                    <div class="addr-block">
                        <div class="addr-title">Adressé à</div>
                        <div class="addr-text">
                            <strong>{{ $invoice->client }}</strong><br>
                            {!! str_replace([', ', ','], '<br>', $invoice->client_address ?? '—') !!}
                        </div>
                    </div>
                </div>

                @php
                    $lines = $invoice->lines;

                    // DÉTECTION IMAGE : Au moins une image dans toute la facture ?
                    $hasAnyImage = $lines->whereNotNull('image')->where('image', '!=', '')->count() > 0;
                    $descWidth = $hasAnyImage ? '30%' : '45%';

                    $page1 = $lines->take(25);
                    $others = $lines->skip(25)->chunk(31);

                    /**
                     * CONVERTISSEUR DE NOMBRES EN LETTRES
                     */
                    if (!function_exists('formaterEnLettres')) {
                        function formaterEnLettres($nb) {
                            $nb = round($nb);
                            if ($nb == 0) return 'zéro';
                            $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
                            $dizaines = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
                            $speciaux = [11 => 'onze', 12 => 'douze', 13 => 'treize', 14 => 'quatorze', 15 => 'quinze', 16 => 'seize'];

                            if ($nb >= 1000000000) {
                                $milliards = intdiv($nb, 1000000000); $reste = $nb % 1000000000;
                                return ($milliards > 1 ? formaterEnLettres($milliards) . ' ' : 'un ') . 'milliard' . ($milliards > 1 ? 's' : '') . ($reste > 0 ? ' ' . formaterEnLettres($reste) : '');
                            }
                            if ($nb >= 1000000) {
                                $millions = intdiv($nb, 1000000); $reste = $nb % 1000000;
                                return ($millions > 1 ? formaterEnLettres($millions) . ' ' : 'un ') . 'million' . ($millions > 1 ? 's' : '') . ($reste > 0 ? ' ' . formaterEnLettres($reste) : '');
                            }
                            if ($nb >= 1000) {
                                $mille = intdiv($nb, 1000); $reste = $nb % 1000;
                                return ($mille > 1 ? formaterEnLettres($mille) . ' ' : '') . 'mille' . ($reste > 0 ? ' ' . formaterEnLettres($reste) : '');
                            }
                            if ($nb >= 100) {
                                $cent = intdiv($nb, 100); $reste = $nb % 100;
                                return ($cent > 1 ? $unites[$cent] . ' ' : '') . 'cent' . ($cent > 1 && $reste == 0 ? 's' : '') . ($reste > 0 ? ' ' . formaterEnLettres($reste) : '');
                            }
                            if ($nb <= 9) return $unites[$nb];
                            if ($nb >= 11 && $nb <= 16) return $speciaux[$nb];
                            $d = intdiv($nb, 10); $u = $nb % 10;
                            if ($d == 7 || $d == 9) {
                                $prefixe = $dizaines[$d-1]; $u_spec = $u + 10;
                                return $prefixe . ($u == 1 && $d == 7 ? ' et onze' : (isset($speciaux[$u_spec]) ? '-' . $speciaux[$u_spec] : '-dix-' . $unites[$u]));
                            }
                            return $dizaines[$d] . ($u == 0 ? ($d == 8 ? 's' : '') : ($u == 1 ? ' et un' : '-' . $unites[$u]));
                        }
                    }

                    $montantEnLettres = ucfirst(formaterEnLettres($invoice->total_ttc));
                @endphp

                <!-- TABLEAU PAGE 1 -->
                @if($page1->count() > 0)
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="c-ref">Réf.</th>
                            <th style="width: {{ $descWidth }};">Désignation</th>
                            @if($hasAnyImage)
                                <th style="width: 15%;">Image</th>
                            @endif
                            <th class="c-pu">P.U. HT</th>
                            <th class="c-qty">Qté</th>
                            <th class="c-tot">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($page1 as $line)
                        <tr>
                            <td class="c-ref">{{ $line->reference ?? '—' }}</td>
                            <td style="text-align: left;">{{ $line->product_name }}</td>
                            @if($hasAnyImage)
                                <td style="text-align: center;">
                                    @if($line->image)
                                        @php
                                            $path = str_starts_with($line->image, 'catalog:') 
                                                ? asset('purchaseImages/' . str_replace('catalog:', '', $line->image)) 
                                                : asset('storage/' . $line->image);
                                        @endphp
                                        <img src="{{ $path }}" class="product-img">
                                    @else
                                        —
                                    @endif
                                </td>
                            @endif
                            <td class="c-pu">{{ number_format($line->unit_price_ht, 0, ',', ' ') }}</td>
                            <td class="c-qty">{{ $line->quantity }}</td>
                            <td class="c-tot">{{ number_format($line->total_ht, 0, ',', ' ') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <!-- PAGES SUIVANTES -->
                @foreach($others as $chunk)
                    <div class="page-break"></div>
                    <div class="print-top-spacing"></div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th class="c-ref">Réf.</th>
                                <th style="width: {{ $descWidth }};">Désignation (Suite)</th>
                                @if($hasAnyImage)
                                    <th style="width: 15%;">Image</th>
                                @endif
                                <th class="c-pu">P.U. HT</th>
                                <th class="c-qty">Qté</th>
                                <th class="c-tot">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chunk as $line)
                            <tr>
                                <td class="c-ref">{{ $line->reference ?? '—' }}</td>
                                <td style="text-align: left;">{{ $line->product_name }}</td>
                                @if($hasAnyImage)
                                    <td style="text-align: center;">
                                        @if($line->image)
                                            @php
                                                $path = str_starts_with($line->image, 'catalog:') 
                                                    ? asset('purchaseImages/' . str_replace('catalog:', '', $line->image)) 
                                                    : asset('storage/' . $line->image);
                                            @endphp
                                            <img src="{{ $path }}" class="product-img">
                                        @else
                                            —
                                        @endif
                                    </td>
                                @endif
                                <td class="c-pu">{{ number_format($line->unit_price_ht, 0, ',', ' ') }}</td>
                                <td class="c-qty">{{ $line->quantity }}</td>
                                <td class="c-tot">{{ number_format($line->total_ht, 0, ',', ' ') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach

                <!-- BLOC TOTAUX -->
                <div class="totals-wrapper">
                    <table class="totals">
                        <tr>
                            <td class="t-label">Montant Total</td>
                            <td class="t-value">{{ number_format($invoice->total_ht, 0, ',', ' ') }}</td>
                        </tr>

                        @if(($invoice->discount ?? 0) > 0)
                            @php
                                $valeurRemise = ($invoice->total_ht * $invoice->discount) / 100;
                                $netHT = ($invoice->total_htva > 0) ? $invoice->total_htva : ($invoice->total_ht - $valeurRemise);
                            @endphp
                            <tr>
                                <td class="t-label">Remise ({{ number_format($invoice->discount, 0, ',', ' ') }}%)</td>
                                <td class="t-value"> {{ number_format($valeurRemise, 0, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td class="t-label">Montant HTVA</td>
                                <td class="t-value">{{ number_format($netHT, 0, ',', ' ') }}</td>
                            </tr>
                        @endif

                        <tr>
                            <td class="t-label">TVA ({{ $invoice->tax_rate * 100 }}%)</td>
                            <td class="t-value">{{ number_format($invoice->total_tva, 0, ',', ' ') }}</td>
                        </tr>
                        <tr class="row-ttc">
                            <td class="t-label" style="color:white; font-weight:bold;">TOTAL TTC</td>
                            <td class="t-value" style="color:white; font-weight:bold;">{{ number_format($invoice->total_ttc, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </table>

                    <div class="amount-in-words">
                        Arrêté à la somme de : <strong>{{ $montantEnLettres }}</strong> Francs CFA.
                    </div>
                </div>

                <div class="conditions-signatures-wrapper">
                    <div class="conditions">
                        <div class="cond-col">
                            <strong>Délai de livraison :</strong> {{ $invoice->delivery_terms ?? 'Disponible' }}<br>
                            <strong>Lieu de livraison :</strong> {{ $invoice->delivery_location ?? 'Bamako' }}
                        </div>
                        <div class="cond-col">
                            <strong>Condition de règlement :</strong> {{ $invoice->payment_terms ?? '30 jours' }}<br>
                            <strong>Validité :</strong> {{ $invoice->validity ?? '30 jours' }}
                        </div>
                    </div>

                    <div class="signatures">
                        <div class="sign-block">
                            <div class="sign-header">Pour le Client</div>
                            <div class="sign-label">Date et Signature</div>
                        </div>
                        <div class="sign-block" style="text-align: right;">
                            <div class="sign-header">Pour Approlog</div>
                            <div class="sign-label">Signature</div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="footer-fixed">
                    <div class="footer-line">
                        <span><strong>RIB:</strong> 82</span> | <span><strong>BIC:</strong> BMSMMLBA</span> | <span><strong>N° CPT:</strong> 102 01001 062237802001-82</span> | <span><strong>RCCM:</strong> MA-BKO-2022-B-4162</span> | <span><strong>NIF:</strong> 084142384R</span>
                    </div>
                </div>

            </div>
        </div>
    </div> <!-- Fin .invoice-scope -->
</div> <!-- Fin .container-fluid -->

{{-- MODALE POUR L'ENVOI DE LA FACTURE (S'inspire de la modale devis) --}}
<div class="modal fade no-print" id="sendInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="sendInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('invoices.sendEmail', $invoice->id) }}" method="POST" id="formSendEmail">
            @csrf
            <div class="modal-content text-dark">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="sendInvoiceModalLabel">Envoyer la facture à un contact</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        // On récupère le client via son code_client pour obtenir ses contacts
                        $customer = \App\Models\Customer::where('code_client', $invoice->code_client)->with('contacts')->first();
                    @endphp

                    @if($customer && $customer->contacts->count() > 0)
                    <p class="text-muted">Sélectionnez les contacts pour l'envoi par <strong>Email</strong> :</p>
                        <div class="table-responsive"></div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <input type="checkbox" id="selectAllContacts" title="Tout sélectionner">
                                        </th>
                                        <th>Nom / Poste</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th class="text-right">WhatsApp (Direct)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->contacts as $contact)
                                    <tr>
                                        <td class="text-center">
                                            @if($contact->email)
                                                <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}" class="contact-checkbox">
                                            @else
                                                <input type="checkbox" disabled title="Pas d'email">
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $contact->name }}</strong><br>
                                            <small class="text-muted">{{ $contact->position ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $contact->email ?? '—' }}</td>
                                        <td>{{ $contact->phone ?? '—' }}</td>
                                        <td class="text-right">
                                            {{-- WhatsApp individuel --}}
                                            @if($contact->phone)
                                                @php
                                                    $cleanPhone = preg_replace('/[^0-9]/', '', $contact->phone);
                                                    $waMessage = "Bonjour {$contact->name}, veuillez trouver ci-joint la facture {$invoice->invoice_number} d'un montant de " . number_format($invoice->total_ttc, 0, ',', ' ') . " FCFA de la part d'Approlog.";
                                                    $waUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($waMessage);
                                                @endphp
                                                <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-success" title="Envoyer par WhatsApp">
                                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> Aucun personnel à contacter n'est associé à ce client ({{ $invoice->client }}). 
                            Veuillez l'ajouter dans la fiche client.
                        </div>
                    @endif
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" style="background-color: rgb(144, 142, 140); " data-dismiss="modal">Fermer</button>
                    @if($customer && $customer->contacts->count() > 0)
                        <button type="submit" class="btn btn-info" id="btnSubmitMultipleEmails" disabled>
                            <i class="fa fa-envelope"></i> Envoyer par Email aux sélectionnés
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllContacts');
    const checkboxes = document.querySelectorAll('.contact-checkbox');
    const submitBtn = document.getElementById('btnSubmitMultipleEmails');

    if(selectAll) {
        // Gérer le "Tout sélectionner"
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateSubmitButton();
        });
    }

    // Gérer l'activation du bouton d'envoi
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSubmitButton);
    });

    function updateSubmitButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if(submitBtn) {
            submitBtn.disabled = !anyChecked;
        }
    }
});
</script>

@endsection