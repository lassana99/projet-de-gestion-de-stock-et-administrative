<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\EmployeeExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    // Listes d'options pour les menus déroulants
    private $diplomas = ['Bac', 'BTS', 'DUT', 'Licence', 'Maîtrise', 'Master', 'Doctorat'];
    
    private $experienceLevels = [
        'Moins de 1 an', '1 an', '2 ans', '3 ans', '4 ans', '5 ans', 
        '6 ans', '7 ans', '8 ans', '9 ans', '10 ans', 'Plus de 10 ans'
    ];

    // Nouvelle liste pour la situation matrimoniale
    private $maritalStatuses = [
        'Célibataire', 'Marié(e)', 'Divorcé(e)', 'Veuf(ve)', 'Union libre'
    ];

    /**
     * Liste des employés avec recherche globale
     */
    public function index(Request $request) {
        $query = Employee::query();
        
        if ($request->filled('searchKey')) {
            $s = $request->searchKey;
            $query->where(function($q) use ($s) {
                $q->where('full_name', 'like', "%$s%")
                  ->orWhere('matricule', 'like', "%$s%")
                  ->orWhere('position', 'like', "%$s%")
                  ->orWhere('specialty', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('diploma', 'like', "%$s%");
            });
        }
        
        $employees = $query->orderBy('matricule', 'asc')->paginate(10);
        return view('admin.rh.employee.list', compact('employees'));
    }

    /**
     * Formulaire de création
     */
    public function create() {
        return view('admin.rh.employee.create', [
            'diplomas'         => $this->diplomas,
            'experienceLevels' => $this->experienceLevels,
            'maritalStatuses'  => $this->maritalStatuses // Ajouté
        ]);
    }

    /**
     * Enregistrement d'un nouveau personnel
     */
    public function store(Request $request) {
        $data = $request->validate([
            'full_name'         => 'required|string|max:255',
            'phone'             => 'required|string|max:255', // Nouveau
            'email'             => 'required|email|unique:employees,email', // Nouveau
            'position'          => 'required|string|max:255',
            'diploma'           => 'required|string',
            'specialty'         => 'required|string|max:255',
            'marital_status'    => 'required|string', // Nouveau
            'children_count'    => 'required|integer|min:0', // Nouveau
            'experience_years'  => 'required|string',
            'id_number'         => 'required|string|max:255',
            'id_type'           => 'required|string',
            'id_type_other'     => 'required_if:id_type,Autres',
            'emergency_contact' => 'required|string|max:255', // Nouveau
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Génération automatique du Matricule MAT-001
        $last = Employee::orderBy('id', 'desc')->first();
        $nextNum = $last ? ((int) str_replace('APPRO-', '', $last->matricule)) + 1 : 1;
        $data['matricule'] = 'APPRO-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        // Gestion de la photo
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        Employee::create($data);

        return redirect()->route('employeeList')->with('success', 'Employé enregistré avec succès sous le matricule : ' . $data['matricule']);
    }

    /**
     * Formulaire de modification
     */
    public function edit($id) {
        $employee = Employee::findOrFail($id);
        return view('admin.rh.employee.edit', [
            'employee'         => $employee,
            'diplomas'         => $this->diplomas,
            'experienceLevels' => $this->experienceLevels,
            'maritalStatuses'  => $this->maritalStatuses // Ajouté
        ]);
    }

    /**
     * Mise à jour des informations du personnel
     */
    public function update(Request $request, $id) {
        $employee = Employee::findOrFail($id);
        
        $data = $request->validate([
            'full_name'         => 'required|string|max:255',
            'phone'             => 'required|string|max:255', // Nouveau
            'email'             => 'required|email|unique:employees,email,' . $id, // Nouveau (ignore l'actuel)
            'position'          => 'required|string|max:255',
            'diploma'           => 'required|string',
            'specialty'         => 'required|string|max:255',
            'marital_status'    => 'required|string', // Nouveau
            'children_count'    => 'required|integer|min:0', // Nouveau
            'experience_years'  => 'required|string',
            'id_number'         => 'required|string|max:255',
            'id_type'           => 'required|string',
            'id_type_other'     => 'required_if:id_type,Autres',
            'emergency_contact' => 'required|string|max:255', // Nouveau
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Mise à jour de la photo
        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        $employee->update($data);

        return redirect()->route('employeeList')->with('success', 'Les informations de ' . $employee->full_name . ' ont été mises à jour.');
    }

    /**
     * Détails d'un employé
     */
    public function show($id) {
        $employee = Employee::with('leaves')->findOrFail($id);
        return view('admin.rh.employee.details', compact('employee'));
    }

    /**
     * Suppression d'un employé
     */
    public function destroy($id) {
        try {
            $employee = Employee::findOrFail($id);
            
            // Supprimer la photo physiquement si elle existe
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            
            $employee->delete();
            return back()->with('success', 'Employé supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * Exportation Excel
     */
    public function exportExcel(Request $request) {
        return Excel::download(new EmployeeExport($request->searchKey), 'personnel_'.date('d_m_Y').'.xlsx');
    }
}