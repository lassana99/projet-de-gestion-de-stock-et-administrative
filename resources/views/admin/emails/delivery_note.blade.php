@if($recipientName)
    <p>Bonjour {{ $recipientName }},</p>
@else
    <p>Bonjour La team {{ $deliveryNote->client_name }},</p>
@endif

<p>Veuillez trouver ci-joint le Bordereau de Livraison <strong>{{ $deliveryNote->delivery_note_number }}</strong> concernant votre commande chez Approlog.</p>

<p>Nous vous souhaitons une bonne réception.</p>

<p>Cordialement,<br>
L'équipe Approlog</p>