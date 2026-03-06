@extends('admin.layouts.master')

@section('content')
<div class="container-fluid"> <!-- Protège le menu latéral -->

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
            min-height: 297mm; /* A4 */
            padding: 10mm 15mm 15mm 15mm; 
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            box-sizing: border-box;
            
            font-size: 11pt; 
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
            margin: 0; font-size: 18pt; 
            text-transform: uppercase; 
        }
        .doc-info p { margin: 1px 0; font-size: 11pt; }

        /* Adresses */
        .addresses {
            display: flex;
            gap: 20px;
            margin-bottom: 15px; 
        }
        .addr-block {
            flex: 1;
            border: 1px solid var(--color-border);
            border-radius: 4px;
        }
        .addr-title {
            background: var(--color-bg-header);
            padding: 5px 10px;
            font-size: 10pt;
            text-transform: uppercase;
            border-bottom: 1px solid var(--color-border);
            font-weight: bold !important;
            color: var(--color-primary) !important;
            -webkit-print-color-adjust: exact;
        }
        .addr-text {
            padding: 8px 10px;
            font-size: 11pt; 
            line-height: 1.3;
        }

        /* Bloc Bon de Commande */
        .po-block {
            border: 2px solid #000;
            padding: 5px 10px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 15px;
            font-size: 10pt;
            text-transform: uppercase;
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
            padding: 8px 4px;
            font-family: var(--font-main);
            font-size: 10pt; 
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid var(--color-primary);
            -webkit-print-color-adjust: exact;
            text-align: center;
        }
        table.items-table td {
            border: 1px solid var(--color-border);
            font-family: var(--font-main);
            font-size: 10pt;
            padding: 6px 4px; 
            vertical-align: middle; 
            line-height: 1.2;
            overflow: hidden;
            word-wrap: break-word;
        }
        
        /* Colonnes BL (Sans Image) */
        .c-ref     { width: 15%; text-align: center; }
        .c-desc    { width: 45%; text-align: left; }
        .c-qty-ord { width: 10%; text-align: center; }
        .c-qty-del { width: 10%; text-align: center; }
        .c-obs     { width: 20%; text-align: left; }

        /* ==========================================================================
           BLOCS SIGNATURES
           ========================================================================== */
        .signatures-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-family: var(--font-main);
            font-size: 11pt;
        }
        .sign-col {
            flex: 1;
            text-align: center;
        }
        .sign-label {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .u-text {
            text-decoration: underline;
        }

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

        /* ==========================================================================
           IMPRESSION
           ========================================================================== */
        @media print {
            body * { visibility: hidden; }
            .invoice-scope, .invoice-scope * { visibility: visible; }
            
            .invoice-scope {
                position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0;
            }
            .document-wrapper {
                background: white; padding: 0; margin: 0; display: block;
            }
            .sheet {
                width: 100% !important; box-shadow: none; margin: 0; border: none;
                page-break-after: always; padding: 10mm 15mm 10mm 15mm !important; 
            }
            .sheet:last-child { page-break-after: auto; }
            .no-print { display: none !important; }
            
            .footer-fixed {
                position: fixed; bottom: 5mm; left: 15mm; right: 15mm; width: auto;
            }
            .page-break { page-break-before: always !important; display: block; height: 1px; }
            .print-top-spacing { display: block; height: 20mm; width: 100%; }
        }

        /* BOUTONS */
        .btn-group { display: flex; gap: 10px; margin-bottom: 20px; }
        .btn-custom { 
            padding: 8px 15px; 
            border-radius: 5px; 
            color: white; 
            text-decoration: none; 
            font-weight: bold; 
            border:none; 
            cursor: pointer; 
            font-family: sans-serif;
            font-size: 14px;
        }
        .btn-edit { background: #2563eb; }
        .btn-edit:hover { background: #1d4ed8; color:white; }
        .btn-pdf { background: #e74c3c; } /* Style professionnel Rouge pour le PDF */
        .btn-pdf:hover { background: #c0392b; color:white; }
        .btn-print { background: #4b5563; }
        .btn-print:hover { background: #374151; color:white; }
        .btn-back { background: #dc2626; }
        .btn-back:hover { background: #b91c1c; color:white; }
        /* Nouveau bouton envoyer */
        .btn-send { background: #059669; }
        .btn-send:hover { background: #047857; color:white; }

    </style>

    <!-- WRAPPER GLOBAL -->
    <div class="invoice-scope">

        <div class="document-wrapper">

            <!-- Boutons -->
            <div class="no-print btn-group">
                <a href="{{ route('delivery_notes.edit', $deliveryNote->id) }}" class="btn-custom btn-edit">✏️ Modifier</a>
                
                {{-- BOUTON ENVOYER --}}
                <button type="button" class="btn-custom btn-send" data-toggle="modal" data-target="#sendBLModal">
                    ✈️ Envoyer
                </button>

                <a href="{{ route('delivery_notes.pdf', $deliveryNote->id) }}" class="btn-custom btn-pdf" target="_blank">📄 Exporter PDF</a>
                
                <button onclick="window.print()" class="btn-custom btn-print">🖨️ Imprimer</button>
                <a href="{{ route('delivery_notes.list') }}" class="btn-custom btn-back">🔙 Retour</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success no-print w-100 mb-2">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger no-print w-100 mb-2">{{ session('error') }}</div>
            @endif

            <!-- FEUILLE DE DOCUMENT -->
            <div class="sheet">
                
                <!-- HEADER -->
                <div class="header">
                    <div class="logo">
                        <img src="{{ asset('adminProfile/company-logo.png') }}" alt="Approlog">
                    </div>
                    <div class="doc-info">
                        <h1>BORDEREAU DE LIVRAISON</h1>
                        <p><strong>N°:</strong> {{ $deliveryNote->delivery_note_number }}</p>
                        <p><strong>Date :</strong> {{ $deliveryNote->date_delivery->format('d/m/Y') }}</p>
                        <p><strong>Code client :</strong> {{ $deliveryNote->code_client ?? '—' }}</p>
                    </div>
                </div>

                <!-- ADRESSES -->
                <div class="addresses">
                    <div class="addr-block">
                        <div class="addr-title">Émetteur</div>
                        <div class="addr-text">
                            <strong>Approlog</strong><br>
                            Immeuble Saibou SYLLA, ACI 2000<br>
                            Bamako-Mali<br>
                            Tél: (00223) 66 75 05 73
                        </div>
                    </div>
                    <div class="addr-block">
                        <div class="addr-title">Adressé à</div>
                        <div class="addr-text">
                            <strong>{{ $deliveryNote->client_name }}</strong><br>
                            {!! str_replace(["\r\n", "\r", "\n"], '<br>', $deliveryNote->client_address ?? '—') !!}
                        </div>
                    </div>
                </div>

                <!-- BLOC BON DE COMMANDE -->
                @if($deliveryNote->purchase_order_number)
                <div class="po-block">
                    BON DE COMMANDE N°{{ $deliveryNote->purchase_order_number }}
                </div>
                @endif

                <!-- LOGIQUE PAGINATION -->
                @php
                    $lines = $deliveryNote->lines;
                    $limitTableP1 = 25;    
                    $limitTableNext = 35;    

                    $page1 = $lines->take($limitTableP1);
                    $others = $lines->skip($limitTableP1)->chunk($limitTableNext);
                    
                    $thresholdSignP1 = 20; 
                    $thresholdSignNext = 30; 

                    $isMultiPage = $others->count() > 0;
                    
                    if (!$isMultiPage) {
                        $linesOnLastPage = $page1->count();
                        $limitSign = $thresholdSignP1;
                    } else {
                        $lastChunk = $others->last();
                        $linesOnLastPage = $lastChunk->count();
                        $limitSign = $thresholdSignNext;
                    }

                    $breakSignature = ($linesOnLastPage > $limitSign);
                @endphp

                <!-- TABLEAU PAGE 1 -->
                @if($page1->count() > 0)
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="c-ref">Référence</th>
                            <th class="c-desc">Désignation</th>
                            <th class="c-qty-ord">Qté Cmd</th>
                            <th class="c-qty-del">Qté Liv</th>
                            <th class="c-obs">Observation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($page1 as $line)
                        <tr>
                            <td class="c-ref">{{ $line->reference ?? '—' }}</td>
                            <td class="c-desc">{{ $line->product_name }}</td>
                            <td class="c-qty-ord">{{ $line->quantity_ordered }}</td>
                            <td class="c-qty-del">{{ $line->quantity_delivered }}</td>
                            <td class="c-obs">{{ $line->observation ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <!-- TABLES PAGES SUIVANTES -->
                @foreach($others as $chunk)
                    <div class="page-break"></div>
                    <div class="print-top-spacing"></div>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th class="c-ref">Référence</th>
                                <th class="c-desc">Désignation (Suite)</th>
                                <th class="c-qty-ord">Qté Cmd</th>
                                <th class="c-qty-del">Qté Liv</th>
                                <th class="c-obs">Observation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chunk as $line)
                            <tr>
                                <td class="c-ref">{{ $line->reference ?? '—' }}</td>
                                <td class="c-desc">{{ $line->product_name }}</td>
                                <td class="c-qty-ord">{{ $line->quantity_ordered }}</td>
                                <td class="c-qty-del">{{ $line->quantity_delivered }}</td>
                                <td class="c-obs">{{ $line->observation ?? '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach

                <!-- SAUT SIGNATURE SI NÉCESSAIRE -->
                @if($breakSignature)
                    <div class="page-break"></div>
                    <div class="print-top-spacing"></div>
                @endif

                <!-- BLOC SIGNATURES -->
                <div class="signatures-wrapper">
                    <div class="sign-col">
                        <div class="sign-label"><span class="u-text">Nom et Signature</span></div>
                    </div>

                    <div class="sign-col">
                        <div class="sign-label">
                            <span class="u-text">Date</span><span> :</span> {{ $deliveryNote->date_delivery->format('d/m/Y') }}
                        </div>
                    </div>
                    
                    <div class="sign-col">
                        <div class="sign-label"><span class="u-text">Signature</span></div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="footer-fixed">
                    <div class="footer-line">
                        <span><strong>RIB:</strong> 82</span>
                        <span>|</span>
                        <span><strong>BIC:</strong> BMSMMLBA</span>
                        <span>|</span>
                        <span><strong>N° CPT:</strong> 102 01001 062237802001-82</span>
                        <span>|</span>
                        <span><strong>RCCM:</strong> MA-BKO-2022-B-4162</span>
                        <span>|</span>
                        <span><strong>NIF:</strong> 084142384R</span>
                    </div>
                </div>

            </div>
        </div>

    </div> <!-- Fin .invoice-scope -->
</div> <!-- Fin .container-fluid -->

{{-- MODALE POUR L'ENVOI DU BORDEREAU (Inspirée du devis) --}}
<div class="modal fade no-print" id="sendBLModal" tabindex="-1" role="dialog" aria-labelledby="sendBLModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('delivery_notes.sendEmail', $deliveryNote->id) }}" method="POST" id="formSendBLMultiple">
            @csrf
            <div class="modal-content text-dark">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="sendBLModalLabel">Envoyer le Bordereau : {{ $deliveryNote->delivery_note_number }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        // On récupère le client via son code_client pour obtenir ses contacts
                        $customer = \App\Models\Customer::where('code_client', $deliveryNote->code_client)->with('contacts')->first();
                    @endphp

                    @if($customer && $customer->contacts->count() > 0)
                        <p class="text-muted">Sélectionnez les contacts pour l'envoi par <strong>Email</strong> :</p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">
                                            <input type="checkbox" id="selectAllBLContacts" title="Tout sélectionner">
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
                                                <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}" class="contact-checkbox-bl">
                                            @else
                                                <input type="checkbox" disabled title="Pas d'email renseigné">
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $contact->name }}</strong><br>
                                            <small class="text-muted small">{{ $contact->position ?? 'Personnel' }}</small>
                                        </td>
                                        <td>{{ $contact->email ?? '—' }}</td>
                                        <td>{{ $contact->phone ?? '—' }}</td>
                                        <td class="text-right">
                                            {{-- WhatsApp individuel avec nom personnalisé --}}
                                            @if($contact->phone)
                                                @php
                                                    $cleanPhone = preg_replace('/[^0-9]/', '', $contact->phone);
                                                    $waMessage = "Bonjour {$contact->name}, veuillez trouver ci-joint le Bordereau de Livraison {$deliveryNote->delivery_note_number} de la part d'Approlog.";
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
                            <i class="fa fa-exclamation-triangle"></i> Aucun personnel à contacter n'est associé à ce client ({{ $deliveryNote->client_name }}). 
                            Veuillez l'ajouter dans la fiche client.
                        </div>
                    @endif
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" style="background-color: rgb(144, 142, 140); " data-dismiss="modal">Fermer</button>
                    @if($customer && $customer->contacts->count() > 0)
                        <button type="submit" class="btn btn-info" id="btnSendBLMultiple" disabled>
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
    const selectAll = document.getElementById('selectAllBLContacts');
    const checkboxes = document.querySelectorAll('.contact-checkbox-bl');
    const submitBtn = document.getElementById('btnSendBLMultiple');

    if(selectAll) {
        // Gérer le "Tout sélectionner"
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                if(!cb.disabled) cb.checked = selectAll.checked;
            });
            updateSubmitButton();
        });
    }

    // Gérer l'activation du bouton d'envoi et la cohérence de "Tout sélectionner"
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSubmitButton);
    });

    function updateSubmitButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if(submitBtn) submitBtn.disabled = !anyChecked;
    }
});
</script>

@endsection