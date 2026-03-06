<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;
use App\Exports\UtilityExport;
use Maatwebsite\Excel\Facades\Excel;

class UtilityController extends Controller
{
    private $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    private $descriptions = ['SOMAGEP', 'EDM', 'MOOV', 'Orange', 'Autres'];

    public function index(Request $request) {
        $query = Utility::query();
        if ($request->filled('searchKey')) {
            $s = $request->searchKey;
            $query->where(function($q) use ($s) {
                $q->where('number', 'like', "%$s%")->orWhere('month', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")->orWhere('description_other', 'like', "%$s%");
            });
        }
        $utilities = $query->orderBy('issue_date', 'desc')->paginate(10);
        return view('admin.expenses.utility.list', compact('utilities'));
    }

    public function exportExcel(Request $request) {
        return Excel::download(new UtilityExport($request->searchKey), 'eau_elec_internet_'.date('d_m_Y').'.xlsx');
    }

    public function create() {
        return view('admin.expenses.utility.create', ['months' => $this->months, 'descriptions' => $this->descriptions]);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'month' => 'required',
            'description' => 'required',
            'description_other' => 'required_if:description,Autres',
            'reference' => 'nullable',
            'issue_date' => 'required|date',
            'amount_fcfa' => 'required|numeric',
            'payment_mode' => 'required',
        ]);

        // Génération UTIL-001
        $last = Utility::orderBy('id', 'desc')->first();
        $nextNum = $last ? ((int) str_replace('UTIL-', '', $last->number)) + 1 : 1;
        $data['number'] = 'UTIL-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        Utility::create($data);
        return redirect()->route('utilityList')->with('success', 'Enregistré : ' . $data['number']);
    }

    public function edit($id) {
        $utility = Utility::findOrFail($id);
        return view('admin.expenses.utility.edit', [
            'utility' => $utility, 
            'months' => $this->months, 
            'descriptions' => $this->descriptions
        ]);
    }

    public function update(Request $request, $id) {
        $utility = Utility::findOrFail($id);
        $data = $request->validate([
            'month' => 'required',
            'description' => 'required',
            'description_other' => 'required_if:description,Autres',
            'reference' => 'nullable',
            'issue_date' => 'required|date',
            'amount_fcfa' => 'required|numeric',
            'payment_mode' => 'required',
        ]);
        $utility->update($data);
        return redirect()->route('utilityList')->with('success', 'Mis à jour avec succès.');
    }

    public function show($id) {
        $utility = Utility::findOrFail($id);
        return view('admin.expenses.utility.details', compact('utility'));
    }

    public function destroy($id) {
        Utility::findOrFail($id)->delete();
        return back()->with('success', 'Supprimé avec succès.');
    }
}