<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture_{{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 10mm 15mm 15mm 15mm; }
        body { font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.3; margin: 0; padding: 0; font-size: 11pt; }

        /* HEADER */
        .table-header { width: 100%; border-bottom: 2px solid #1e3a8a; margin-bottom: 30px; border-collapse: collapse; }
        .table-header td { vertical-align: top; padding-bottom: 5px; }
        .logo-img { max-width: 180px; height: auto; margin-top: -5px; }
        .doc-info-title { color: #1e3a8a; margin: 0; font-size: 24pt; text-transform: uppercase; font-weight: bold; }

        /* ADRESSES */
        .table-addresses { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .addr-block { width: 48%; border: 1px solid #9ca3af; vertical-align: top; }
        .addr-title { background-color: #f3f4f6; padding: 4px 8px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #9ca3af; color: #1e3a8a; font-size: 10pt; }
        .addr-text { padding: 8px; font-size: 11pt; }

        /* ARTICLES */
        .items-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .items-table th { background-color: #1e3a8a; color: white; padding: 6px 4px; font-size: 10pt; font-weight: bold; text-transform: uppercase; border: 1px solid #1e3a8a; }
        .items-table td { border: 1px solid #9ca3af; padding: 5px 4px; font-size: 10pt; vertical-align: middle; text-align: center; word-wrap: break-word; }
        .product-img { max-width: 70px; max-height: 70px; }

        /* TOTAUX */
        .totals-table { width: 45%; margin-left: 55%; border-collapse: collapse; margin-top: 10px; }
        .totals-table td { padding: 4px 8px; text-align: right; border-bottom: 1px solid #eee; font-size: 11pt; }
        .row-ttc td { background-color: #1e3a8a; color: white !important; font-weight: bold; font-size: 12pt; }

        .amount-in-words { margin-top: 10px; font-size: 10pt; font-style: italic; border-top: 1px solid #eee; padding-top: 5px; }

        /* CONDITIONS */
        .conditions-box { margin-top: 15px; padding: 8px; border: 1px dashed #9ca3af; background-color: #fdfdfd; }
        .table-conditions { width: 100%; border-collapse: collapse; }
        .table-conditions td { width: 50%; font-size: 10.5pt; padding: 2px 0; vertical-align: top; }

        /* SIGNATURES */
        .table-signatures { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .sign-header { font-weight: bold; text-decoration: underline; margin-bottom: 50px; }

        /* FOOTER */
        .footer-fixed { position: fixed; bottom: -10mm; left: 0; right: 0; text-align: center; border-top: 1px solid #1e3a8a; padding-top: 5px; font-size: 8pt; }
        .page-break { page-break-before: always; }
        .spacer { height: 20mm; }
    </style>
</head>
<body>

    @php
        $lines = $invoice->lines;
        $hasAnyImage = $lines->whereNotNull('image')->where('image', '!=', '')->count() > 0;

        $page1 = $lines->take(25);
        $others = $lines->skip(25)->chunk(31);

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
    @endphp

    <!-- HEADER -->
    <table class="table-header">
        <tr>
            <td style="width: 50%;">
                <img src="{{ public_path('adminProfile/company-logo.png') }}" class="logo-img">
            </td>
            <td style="width: 50%; text-align: right;">
                <h1 class="doc-info-title">FACTURE</h1>
                <p style="margin:0;"><strong>N° :</strong> {{ $invoice->invoice_number }}</p>
                <p style="margin:0;"><strong>Date :</strong> {{ $invoice->date_invoice->format('d/m/Y') }}</p>
                <p style="margin:0;"><strong>Client Code :</strong> {{ $invoice->code_client ?? '—' }}</p>
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
                    Hamdallaye ACI 2000, Rue 1691, Porte 360 <br>
                    Immeuble Kanté, Bamako-Mali<br>
                    Tél: (00223) 66 75 05 73
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td class="addr-block">
                <div class="addr-title">Adressé à</div>
                <div class="addr-text">
                    <strong>{{ $invoice->client }}</strong><br>
                    {!! str_replace([', ', ','], '<br>', $invoice->client_address ?? '—') !!}
                </div>
            </td>
        </tr>
    </table>

    <!-- ARTICLES -->
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
                        @php
                            $img_path = str_starts_with($line->image, 'catalog:') 
                                ? public_path('purchaseImages/' . str_replace('catalog:', '', $line->image)) 
                                : public_path('storage/' . $line->image);
                        @endphp
                        <img src="{{ $img_path }}" class="product-img">
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
                            @php
                                $img_path = str_starts_with($line->image, 'catalog:') 
                                    ? public_path('purchaseImages/' . str_replace('catalog:', '', $line->image)) 
                                    : public_path('storage/' . $line->image);
                            @endphp
                            <img src="{{ $img_path }}" class="product-img">
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
            <td>{{ number_format($invoice->total_ht, 0, ',', ' ') }}</td>
        </tr>
        @if(($invoice->discount ?? 0) > 0)
            @php $valeurRemise = ($invoice->total_ht * $invoice->discount) / 100; @endphp
            <tr>
                <td style="text-align: left;">Remise ({{ number_format($invoice->discount, 0, ',', ' ') }}%)</td>
                <td>{{ number_format($valeurRemise, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td style="text-align: left; font-weight: bold;">Montant HTVA</td>
                <td>{{ number_format($invoice->total_htva, 0, ',', ' ') }}</td>
            </tr>
        @endif
        <tr>
            <td style="text-align: left;">TVA ({{ $invoice->tax_rate * 100 }}%)</td>
            <td>{{ number_format($invoice->total_tva, 0, ',', ' ') }}</td>
        </tr>
        <tr class="row-ttc">
            <td style="text-align: left;">TOTAL TTC</td>
            <td>{{ number_format($invoice->total_ttc, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    <div class="amount-in-words">
        Arrêté à la somme de : <strong>{{ ucfirst(formaterEnLettres($invoice->total_ttc)) }}</strong> Francs CFA.
    </div>

    <!-- CONDITIONS -->
    <div class="conditions-box">
        <table class="table-conditions">
            <tr>
                <td><strong>Délai de livraison :</strong> {{ $invoice->delivery_terms ?? 'Disponible' }}</td>
                <td><strong>Condition de règlement :</strong> {{ $invoice->payment_terms ?? '30 jours' }}</td>
            </tr>
            <tr>
                <td><strong>Lieu de livraison :</strong> {{ $invoice->delivery_location ?? 'Bamako' }}</td>
                <td><strong>Validité :</strong> {{ $invoice->validity ?? '30 jours' }}</td>
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