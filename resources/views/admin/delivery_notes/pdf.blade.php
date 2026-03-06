<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BL_{{ $deliveryNote->delivery_note_number }}</title>
    <style>
        @page { margin: 10mm 15mm 15mm 15mm; }
        body { font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.3; margin: 0; padding: 0; font-size: 11pt; }

        /* HEADER */
        .table-header { width: 100%; border-bottom: 2px solid #1e3a8a; margin-bottom: 25px; border-collapse: collapse; }
        .table-header td { vertical-align: top; padding-bottom: 5px; }
        .logo-img { max-width: 180px; height: auto; margin-top: -5px; }
        .doc-info-title { color: #1e3a8a; margin: 0; font-size: 20pt; text-transform: uppercase; font-weight: bold; }

        /* ADRESSES */
        .table-addresses { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .addr-block { width: 48%; border: 1px solid #9ca3af; vertical-align: top; }
        .addr-title { background-color: #f3f4f6; padding: 4px 8px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #9ca3af; color: #1e3a8a; font-size: 10pt; }
        .addr-text { padding: 8px; font-size: 11pt; }

        /* BON DE COMMANDE */
        .po-block { border: 2px solid #000; padding: 5px 10px; font-weight: bold; margin-bottom: 15px; display: inline-block; font-size: 10pt; }

        /* ARTICLES */
        .items-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .items-table th { background-color: #1e3a8a; color: white; padding: 6px 4px; font-size: 10pt; font-weight: bold; text-transform: uppercase; border: 1px solid #1e3a8a; }
        .items-table td { border: 1px solid #9ca3af; padding: 5px 4px; font-size: 10pt; vertical-align: middle; text-align: center; word-wrap: break-word; }

        /* SIGNATURES */
        .table-signatures { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .table-signatures td { width: 33%; text-align: center; vertical-align: top; }
        /* Suppression du text-decoration: underline ici pour le gérer au cas par cas */
        .sign-header { font-weight: bold; margin-bottom: 40px; display: block; }
        .u-line { text-decoration: underline; }

        /* FOOTER */
        .footer-fixed { position: fixed; bottom: -10mm; left: 0; right: 0; text-align: center; border-top: 1px solid #1e3a8a; padding-top: 5px; font-size: 8pt; }
        .page-break { page-break-before: always; }
        .spacer { height: 20mm; }
    </style>
</head>
<body>

    @php
        $lines = $deliveryNote->lines;
        $page1 = $lines->take(25);
        $others = $lines->skip(25)->chunk(35);
    @endphp

    <!-- HEADER -->
    <table class="table-header">
        <tr>
            <td style="width: 50%;">
                <img src="{{ public_path('adminProfile/company-logo.png') }}" class="logo-img">
            </td>
            <td style="width: 50%; text-align: right;">
                <h1 class="doc-info-title">BORDEREAU DE LIVRAISON</h1>
                <p style="margin:0;"><strong>N°:</strong> {{ $deliveryNote->delivery_note_number }}</p>
                <p style="margin:0;"><strong>Date :</strong> {{ $deliveryNote->date_delivery->format('d/m/Y') }}</p>
                <p style="margin:0;"><strong>Code client :</strong> {{ $deliveryNote->code_client ?? '—' }}</p>
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
                    Immeuble Saibou SYLLA, ACI 2000<br>
                    Bamako-Mali<br>
                    Tél: (00223) 66 75 05 73
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td class="addr-block">
                <div class="addr-title">Adressé à</div>
                <div class="addr-text">
                    <strong>{{ $deliveryNote->client_name }}</strong><br>
                    {!! str_replace(["\r\n", "\r", "\n"], '<br>', $deliveryNote->client_address ?? '—') !!}
                </div>
            </td>
        </tr>
    </table>

    @if($deliveryNote->purchase_order_number)
    <div class="po-block">BON DE COMMANDE N°{{ $deliveryNote->purchase_order_number }}</div>
    @endif

    <!-- TABLEAU ARTICLES PAGE 1 -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">Référence</th>
                <th style="width: 45%; text-align: left;">Désignation</th>
                <th style="width: 10%;">Qté Cmd</th>
                <th style="width: 10%;">Qté Liv</th>
                <th style="width: 20%;">Observation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($page1 as $line)
            <tr>
                <td>{{ $line->reference ?? '—' }}</td>
                <td style="text-align: left;">{{ $line->product_name }}</td>
                <td>{{ $line->quantity_ordered }}</td>
                <td>{{ $line->quantity_delivered }}</td>
                <td style="text-align: left;">{{ $line->observation ?? '' }}</td>
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
                    <th style="width: 15%;">Référence</th>
                    <th style="width: 45%; text-align: left;">Désignation (Suite)</th>
                    <th style="width: 10%;">Qté Cmd</th>
                    <th style="width: 10%;">Qté Liv</th>
                    <th style="width: 20%;">Observation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $line)
                <tr>
                    <td>{{ $line->reference ?? '—' }}</td>
                    <td style="text-align: left;">{{ $line->product_name }}</td>
                    <td>{{ $line->quantity_ordered }}</td>
                    <td>{{ $line->quantity_delivered }}</td>
                    <td style="text-align: left;">{{ $line->observation ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <!-- SIGNATURES -->
    <table class="table-signatures">
        <tr>
            <td>
                <span class="sign-header u-line">Nom et Signature</span>
            </td>
            <td>
                {{-- Ici seul le mot "Date" est souligné grâce à la balise u --}}
                <span class="sign-header"><span class="u-line">Date</span> : {{ $deliveryNote->date_delivery->format('d/m/Y') }}</span>
            </td>
            <td>
                <span class="sign-header u-line">Signature</span>
            </td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer-fixed">
        RIB: 82 | BIC: BMSMMLBA | N° CPT: 102 01001 062237802001-82 | RCCM: MA-BKO-2022-B-4162 | NIF: 084142384R
    </div>

</body>
</html>