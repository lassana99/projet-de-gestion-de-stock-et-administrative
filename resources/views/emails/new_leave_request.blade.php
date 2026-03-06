<h1>Nouvelle demande de congé</h1>
<p>L'employé <strong>{{ $leave->employee->full_name }}</strong> vient de soumettre une demande.</p>
<ul>
    <li><strong>Type :</strong> {{ $leave->leave_type }}</li>
    <li><strong>Période :</strong> du {{ $leave->start_date->format('d/m/Y') }} au {{ $leave->end_date->format('d/m/Y') }}</li>
    <li><strong>Nombre de jours :</strong> {{ $leave->days_count }}</li>
</ul>
<p>Veuillez vous connecter au système pour traiter cette demande.</p>