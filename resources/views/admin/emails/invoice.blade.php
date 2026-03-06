@if($recipientName)
    <p>Bonjour {{ $recipientName }},</p>
@else
    <p>Bonjour La team {{ $invoice->client }},</p>
@endif

<p>Veuillez trouver ci-joint la facture <strong>{{ $invoice->invoice_number }}</strong> d'un montant de <strong>{{ number_format($invoice->total_ttc, 0, ',', ' ') }} FCFA</strong>.</p>

<p>Nous vous remercions de votre confiance.</p>

<p>Cordialement,<br>
L'équipe Approlog</p>