<h1>Mise à jour de votre demande de congé</h1>
<p>Bonjour {{ $leave->employee->full_name }},</p>
<p>La décision concernant votre demande de congé du <strong>{{ $leave->start_date->format('d/m/Y') }}</strong> a été prise.</p>
<p>Statut final : <strong style="color: {{ $leave->status == 'Approuvé' ? 'green' : 'red' }}">{{ $leave->status }}</strong></p>
<p>Merci,</p>
<p>L'administration.</p>