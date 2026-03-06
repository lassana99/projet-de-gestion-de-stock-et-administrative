@if($recipientName)
    <p>Bonjour {{ $recipientName }},</p>
@else
    <p>Bonjour La team {{ $devis->client }},</p>
@endif

<p>Veuillez trouver ci-joint le devis <strong>{{ $devis->devis_number }}</strong> d'un montant de <strong>{{ number_format($devis->total_ttc, 0, ',', ' ') }} FCFA</strong>.</p>

<p>Nous restons à votre entière disposition pour toute information complémentaire.</p>

<p>Cordialement,<br>
L'équipe Approlog</p>