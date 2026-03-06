<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis_{{ $devis->devis_number }}</title>
    <style>
        /* CONFIGURATION GÉNÉRALE */
        @page {
            margin: 5mm 15mm 15mm 15mm; 
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            font-size: 11pt;
        }

        /* HEADER : LOGO ET TITRE ALIGNÉS EN HAUT */
        .table-header {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            /* AUGMENTATION DE L'ESPACE ICI (entre l'entête et le contenu) */
            margin-bottom: 40px; 
            border-collapse: collapse;
        }
        .table-header td {
            vertical-align: top; 
            padding-top: 0;
        }
        .logo-img {
            max-width: 180px;
            height: auto;
            margin-top: -5px; /* Garde le logo bien en haut */
        }
        .doc-info-title {
            color: #1e3a8a;
            margin: 0;
            font-size: 24pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        /* ADRESSES */
        .table-addresses {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .addr-block {
            width: 48%;
            border: 1px solid #9ca3af;
            vertical-align: top;
        }
        .addr-title {
            background-color: #f3f4f6;
            padding: 4px 8px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #9ca3af;
            color: #1e3a8a;
            font-size: 10pt;
        }
        .addr-text {
            padding: 8px;
            font-size: 11pt;
        }

        /* TABLEAU DES ARTICLES */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .items-table th {
            background-color: #1e3a8a;
            color: white;
            padding: 6px 4px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #1e3a8a;
        }
        .items-table td {
            border: 1px solid #9ca3af;
            padding: 5px 4px;
            font-size: 10pt;
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }

        .product-img {
            max-width: 70px;
            max-height: 70px;
        }

        /* TOTAUX */
        .totals-table {
            width: 45%;
            margin-left: 55%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 4px 8px;
            text-align: right;
            border-bottom: 1px solid #eee;
            font-size: 11pt;
        }
        .row-ttc td {
            background-color: #1e3a8a;
            color: white !important;
            font-weight: bold;
            font-size: 12pt;
        }

        .amount-in-words {
            margin-top: 10px;
            font-size: 10pt;
            font-style: italic;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        /* CONDITIONS BOX */
        .conditions-box {
            margin-top: 15px;
            padding: 8px;
            border: 1px dashed #9ca3af;
            background-color: #fdfdfd;
        }
        .table-conditions {
            width: 100%;
            border-collapse: collapse;
        }
        .table-conditions td {
            width: 50%;
            font-size: 10.5pt;
            padding: 3px 0;
            vertical-align: top;
        }

        /* SIGNATURES */
        .table-signatures {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .sign-header {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 50px;
        }

        /* FOOTER */
        .footer-fixed {
            position: fixed;
            bottom: -5mm;
            left: 0;
            right: 0;
            text-align: center;
            border-top: 1px solid #1e3a8a;
            padding-top: 5px;
            font-size: 8pt;
        }

        .page-break { page-break-before: always; }
        .spacer { height: 20mm; }
    </style>
</head>
<body>

    @php
        $lines = $devis->lines;
        $hasAnyImage = $lines->whereNotNull('image')->where('image', '!=', '')->count() > 0;

        $limitTableP1 = 25;      
        $limitTableNext = 31;    
        $page1 = $lines->take($limitTableP1);
        $others = $lines->skip($limitTableP1)->chunk($limitTableNext);

        if (!function_exists('convertirEnLettres')) {
            function convertirEnLettres($nb) {
                $nb = round($nb);
                if ($nb == 0) return 'zéro';
                $unites = array('', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf');
                $dizaines = array('', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix');
                $exceptions = array(11 => 'onze', 12 => 'douze', 13 => 'treize', 14 => 'quatorze', 15 => 'quinze', 16 => 'seize');

                if ($nb >= 1000000000) {
                    $milliards = (int)($nb / 1000000000); $reste = $nb % 1000000000;
                    $txt = ($milliards > 1 ? convertirEnLettres($milliards) . ' ' : 'un ') . 'milliard' . ($milliards > 1 ? 's' : '');
                    return $txt . ($reste > 0 ? ' ' . convertirEnLettres($reste) : '');
                }
                if ($nb >= 1000000) {
                    $millions = (int)($nb / 1000000); $reste = $nb % 1000000;
                    $txt = ($millions > 1 ? convertirEnLettres($millions) . ' ' : 'un ') . 'million' . ($millions > 1 ? 's' : '');
                    return $txt . ($reste > 0 ? ' ' . convertirEnLettres($reste) : '');
                }
                if ($nb >= 1000) {
                    $mille = (int)($nb / 1000); $reste = $nb % 1000;
                    $txt = ($mille > 1 ? convertirEnLettres($mille) . ' ' : '') . 'mille';
                    return $txt . ($reste > 0 ? ' ' . convertirEnLettres($reste) : '');
                }
                if ($nb >= 100) {
                    $cent = (int)($nb / 100); $reste = $nb % 100;
                    $txt = ($cent > 1 ? $unites[$cent] . ' ' : '') . 'cent' . ($cent > 1 && $reste == 0 ? 's' : '');
                    return $txt . ($reste > 0 ? ' ' . convertirEnLettres($reste) : '');
                }
                if ($nb <= 9) return $unites[$nb];
                if ($nb >= 11 && $nb <= 16) return $exceptions[$nb];
                $d = (int)($nb / 10); $u = $nb % 10;
                if ($d == 7 || $d == 9) {
                    $prefixe = $dizaines[$d-1]; $unite_speciale = $u + 10;
                    if ($u == 1 && $d == 7) return $prefixe . ' et onze';
                    if (isset($exceptions[$unite_speciale])) return $prefixe . '-' . $exceptions[$unite_speciale];
                    return $prefixe . '-dix-' . $unites[$u];
                } else {
                    $prefixe = $dizaines[$d];
                    if ($u == 0) return ($d == 8) ? 'quatre-vingts' : $prefixe;
                    if ($u == 1 && $d != 8) return $prefixe . ' et un';
                    return $prefixe . '-' . $unites[$u];
                }
            }
        }
    @endphp

    <!-- HEADER -->
    <table class="table-header">
        <tr>
            <td style="width: 50%;">
                <img src="{{ public_path('adminProfile/company-logo.png') }}" class="logo-img">
            </td>
            <td style="width: 50%; text-align: right;">
                <h1 class="doc-info-title">DEVIS</h1>
                <p style="margin:0;"><strong>N° :</strong> {{ $devis->devis_number }}</p>
                <p style="margin:0;"><strong>Date :</strong> {{ $devis->date_devis->format('d/m/Y') }}</p>
                <p style="margin:0;"><strong>Client Code :</strong> {{ $devis->code_client ?? '—' }}</p>
            </td>
        </tr>
    </table>

    <!-- ADRESSES -->
    <table class="table-addresses">
        <tr>
            <td class="addr-block">
                <div class="addr-title">Émetteur</div>
                <div class="addr-text">
                    <strong>Approlog</strong><br>
                    Hamdallaye ACI 2000, Rue 1691, Porte 360<br>
                    Immeuble Kanté, Bamako-Mali<br>
                    Tél: (00223) 66 75 05 73
                </div>
            </td>
            <td style="width: 4%;"></td> 
            <td class="addr-block">
                <div class="addr-title">Adressé à</div>
                <div class="addr-text">
                    <strong>{{ $devis->client }}</strong><br>
                    {!! str_replace([', ', ','], '<br>', $devis->client_address ?? '—') !!}
                </div>
            </td>
        </tr>
    </table>

    <!-- ARTICLES PAGE 1 -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">Réf.</th>
                <th style="width: {{ $hasAnyImage ? '30%' : '45%' }}; text-align: left;">Désignation</th>
                @if($hasAnyImage) <th style="width: 15%;">Image</th> @endif
                <th style="width: 15%;">P.U. HT</th>
                <th style="width: 10%;">Qté</th>
                <th style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($page1 as $line)
            <tr>
                <td>{{ $line->reference ?? '—' }}</td>
                <td style="text-align: left;">{{ $line->product_name }}</td>
                @if($hasAnyImage)
                <td>
                    @if($line->image)
                        <img src="{{ public_path('storage/' . $line->image) }}" class="product-img">
                    @else — @endif
                </td>
                @endif
                <td>{{ number_format($line->unit_price_ht, 0, ',', ' ') }}</td>
                <td>{{ $line->quantity }}</td>
                <td>{{ number_format($line->total_ht, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- PAGES SUIVANTES -->
    @foreach($others as $chunk)
        <div class="page-break"></div>
        <div class="spacer"></div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Réf.</th>
                    <th style="width: {{ $hasAnyImage ? '30%' : '45%' }}; text-align: left;">Désignation (Suite)</th>
                    @if($hasAnyImage) <th style="width: 15%;">Image</th> @endif
                    <th style="width: 15%;">P.U. HT</th>
                    <th style="width: 10%;">Qté</th>
                    <th style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $line)
                <tr>
                    <td>{{ $line->reference ?? '—' }}</td>
                    <td style="text-align: left;">{{ $line->product_name }}</td>
                    @if($hasAnyImage)
                    <td>
                        @if($line->image)
                            <img src="{{ public_path('storage/' . $line->image) }}" class="product-img">
                        @else — @endif
                    </td>
                    @endif
                    <td>{{ number_format($line->unit_price_ht, 0, ',', ' ') }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td>{{ number_format($line->total_ht, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <!-- TOTAUX -->
    <table class="totals-table">
        <tr>
            <td style="text-align: left; font-weight: bold;">Montant Total</td>
            <td>{{ number_format($devis->total_ht, 0, ',', ' ') }}</td>
        </tr>
        @if(($devis->discount ?? 0) > 0)
            @php $montantRemise = ($devis->total_ht * $devis->discount) / 100; @endphp
            <tr>
                <td style="text-align: left;">Remise ({{ number_format($devis->discount, 0, ',', ' ') }}%)</td>
                <td>{{ number_format($montantRemise, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td style="text-align: left; font-weight: bold;">Montant HT</td>
                <td>{{ number_format($devis->total_htva, 0, ',', ' ') }}</td>
            </tr>
        @endif
        <tr>
            <td style="text-align: left;">TVA ({{ $devis->tax_rate * 100 }}%)</td>
            <td>{{ number_format($devis->total_tva, 0, ',', ' ') }}</td>
        </tr>
        <tr class="row-ttc">
            <td style="text-align: left;">TOTAL TTC</td>
            <td>{{ number_format($devis->total_ttc, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    <div class="amount-in-words">
        Arrêté à la somme de : <strong>{{ ucfirst(convertirEnLettres($devis->total_ttc)) }}</strong> Francs CFA.
    </div>

    <!-- CONDITIONS -->
    <div class="conditions-box">
        <table class="table-conditions">
            <tr>
                <td><strong>Délai de livraison :</strong> {{ $devis->delivery_terms ?? 'Disponible' }}</td>
                <td><strong>Condition de règlement :</strong> {{ $devis->payment_terms ?? '30 jours' }}</td>
            </tr>
            <tr>
                <td><strong>Lieu de livraison :</strong> {{ $devis->delivery_location ?? 'Bamako' }}</td>
                <td><strong>Validité :</strong> {{ $devis->validity ?? '30 jours' }}</td>
            </tr>
        </table>
    </div>

    <!-- SIGNATURES -->
    <table class="table-signatures">
        <tr>
            <td style="width: 50%;">
                <div class="sign-header">Pour le Client</div>
                <div style="font-style: italic; font-size: 9pt;">Date et Signature</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="sign-header">Pour Approlog</div>
                <div style="font-style: italic; font-size: 9pt;">Signature</div>
            </td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer-fixed">
        RIB: 82 | BIC: BMSMMLBA | N° CPT: 102 01001 062237802001-82 | RCCM: MA-BKO-2022-B-4162 | NIF: 084142384R
    </div>

</body>
</html>