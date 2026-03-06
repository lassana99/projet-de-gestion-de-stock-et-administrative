<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee; // Importation du modèle Employee
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalaryExport;

class SalaryController extends Controller
{
    /**
     * Liste des salaires payés
     */
    public function index(Request $request) {
        $query = Salary::query();
        if ($request->filled('searchKey')) {
            $s = $request->searchKey;
            $query->where(function($q) use ($s) {
                $q->where('number', 'like', "%$s%")
                  ->orWhere('full_name', 'like', "%$s%")
                  ->orWhere('position', 'like', "%$s%")
                  ->orWhere('id_number', 'like', "%$s%");
            });
        }
        $salaries = $query->orderBy('payment_date', 'desc')->paginate(10);
        return view('admin.expenses.salary.list', compact('salaries'));
    }

    /**
     * Formulaire de création : On récupère tous les employés
     */
    public function create() {
        $employees = Employee::orderBy('full_name', 'asc')->get();
        return view('admin.expenses.salary.create', compact('employees'));
    }

    /**
     * Enregistrement d'un salaire
     */
    public function store(Request $request) {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'id_number' => 'required|string|max:255',
            'id_type' => 'required|string',
            'id_type_other' => 'required_if:id_type,Autres',
            'amount_fcfa' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'additional_details' => 'nullable|string',
        ]);

        // Génération SAL-001
        $last = Salary::orderBy('id', 'desc')->first();
        $nextNum = $last ? ((int) str_replace('SAL-', '', $last->number)) + 1 : 1;
        $data['number'] = 'SAL-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        Salary::create($data);
        return redirect()->route('salaryList')->with('success', 'Salaire enregistré : ' . $data['number']);
    }

    public function show($id) {
        $salary = Salary::findOrFail($id);
        return view('admin.expenses.salary.details', compact('salary'));
    }

    /**
     * Formulaire de modification
     */
    public function edit($id) {
        $salary = Salary::findOrFail($id);
        $employees = Employee::orderBy('full_name', 'asc')->get();
        return view('admin.expenses.salary.edit', compact('salary', 'employees'));
    }

    /**
     * Mise à jour
     */
    public function update(Request $request, $id) {
        $salary = Salary::findOrFail($id);
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'id_number' => 'required|string|max:255',
            'id_type' => 'required|string',
            'id_type_other' => 'required_if:id_type,Autres',
            'amount_fcfa' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'additional_details' => 'nullable|string',
        ]);

        $salary->update($data);
        return redirect()->route('salaryList')->with('success', 'Salaire mis à jour.');
    }

    public function destroy($id) {
        Salary::findOrFail($id)->delete();
        return back()->with('success', 'Supprimé avec succès.');
    }

    public function exportExcel(Request $request) {
        return Excel::download(new SalaryExport($request->searchKey), 'salaires_'.date('d_m_Y').'.xlsx');
    }
}