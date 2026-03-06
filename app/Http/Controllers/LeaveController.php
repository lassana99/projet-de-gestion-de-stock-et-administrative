<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use App\Models\User; // Ajouté pour récupérer les superadmins
use Illuminate\Http\Request;
use App\Exports\LeaveExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail; // Ajouté pour l'envoi de mail
use App\Mail\NewLeaveRequestMail;    // La classe mailable pour l'admin
use App\Mail\LeaveStatusUpdatedMail; // La classe mailable pour l'employé

class LeaveController extends Controller
{
    /**
     * Liste des demandes de congés avec recherche globale
     */
    public function index(Request $request) {
        $query = Leave::with('employee');
        
        if ($request->filled('searchKey')) {
            $s = $request->searchKey;
            $query->whereHas('employee', function($q) use ($s) {
                $q->where('full_name', 'like', "%$s%")
                  ->orWhere('matricule', 'like', "%$s%");
            })->orWhere('leave_type', 'like', "%$s%")
              ->orWhere('status', 'like', "%$s%");
        }
        
        $leaves = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.rh.leave.list', compact('leaves'));
    }

    /**
     * Formulaire de création d'une demande
     */
    public function create() {
        $employees = Employee::orderBy('full_name')->get();
        return view('admin.rh.leave.create', compact('employees'));
    }

    /**
     * Enregistrement d'une nouvelle demande
     */
    public function store(Request $request) {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'nullable|string',
        ]);

        // Calcul automatique des jours (incluant le jour de fin)
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $data['days_count'] = $start->diffInDays($end) + 1;
        $data['status'] = 'En attente';

        // Création du congé
        $leave = Leave::create($data);

        // --- NOTIFICATION AUTOMATIQUE AU SUPER-ADMIN ---
        $superAdmins = User::where('role', 'superadmin')->get();
        if ($superAdmins->isNotEmpty()) {
            // On envoie le mail à tous les superadmins
            Mail::to($superAdmins)->send(new NewLeaveRequestMail($leave));
        }

        return redirect()->route('leaveList')->with('success', 'Demande de congé enregistrée avec succès et les Super-Administrateurs ont été notifiés.');
    }

    /**
     * Affichage des détails d'un congé
     */
    public function show($id) {
        $leave = Leave::with('employee')->findOrFail($id);
        return view('admin.rh.leave.details', compact('leave'));
    }

    /**
     * Formulaire de modification d'une demande
     */
    public function edit($id) {
        $leave = Leave::findOrFail($id);
        $employees = Employee::orderBy('full_name')->get();
        return view('admin.rh.leave.edit', compact('leave', 'employees'));
    }

    /**
     * Mise à jour d'une demande existante
     */
    public function update(Request $request, $id) {
        $leave = Leave::with('employee')->findOrFail($id);

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'nullable|string',
            'status'      => 'required|in:En attente,Approuvé,Refusé'
        ]);

        // Recalcul du nombre de jours
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $data['days_count'] = $start->diffInDays($end) + 1;

        // On vérifie si le statut va changer pour notifier l'employé
        $oldStatus = $leave->status;
        $leave->update($data);

        // --- NOTIFICATION AU PERSONNEL SI LE STATUT A CHANGÉ ---
        if ($oldStatus !== $request->status && in_array($request->status, ['Approuvé', 'Refusé'])) {
            if ($leave->employee && $leave->employee->email) {
                Mail::to($leave->employee->email)->send(new LeaveStatusUpdatedMail($leave));
            }
        }

        return redirect()->route('leaveList')->with('success', 'La demande de congé a été mise à jour.');
    }

    /**
     * Mise à jour rapide du statut (Approuver/Refuser)
     */
    public function updateStatus(Request $request, $id) {
        $request->validate(['status' => 'required|in:En attente,Approuvé,Refusé']);
        
        $leave = Leave::with('employee')->findOrFail($id);
        $leave->update(['status' => $request->status]);

        // --- NOTIFICATION AU PERSONNEL ---
        if (in_array($request->status, ['Approuvé', 'Refusé'])) {
            if ($leave->employee && $leave->employee->email) {
                Mail::to($leave->employee->email)->send(new LeaveStatusUpdatedMail($leave));
            }
        }
        
        return back()->with('success', 'Le statut du congé a été mis à jour avec succès et le personnel a été notifié par mail.');
    }

    /**
     * Suppression d'une demande
     */
    public function destroy($id) {
        try {
            $leave = Leave::findOrFail($id);
            $leave->delete();
            return back()->with('success', 'La demande de congé a été supprimée.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Exportation Excel
     */
    public function exportExcel(Request $request) {
        $searchKey = $request->get('searchKey');
        return Excel::download(new LeaveExport($searchKey), 'liste_conges_'.date('d_m_Y').'.xlsx');
    }
}