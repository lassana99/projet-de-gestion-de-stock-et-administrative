<?php

namespace App\Http\Controllers;

use App\Models\InpsPayment;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Exports\InpsPaymentExport;
use Maatwebsite\Excel\Facades\Excel;

class InpsPaymentController extends Controller {
    public function index(Request $request) {
        $query = InpsPayment::with('employees');
        if ($request->filled('searchKey')) {
            $s = $request->searchKey;
            $query->where('number', 'like', "%$s%")
                  ->orWhereHas('employees', function($q) use ($s) { $q->where('full_name', 'like', "%$s%"); });
        }
        $payments = $query->orderBy('payment_date', 'desc')->paginate(10);
        return view('admin.expenses.inps.list', compact('payments'));
    }

    public function create() {
        $employees = Employee::orderBy('full_name')->get();
        return view('admin.expenses.inps.create', compact('employees'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'employee_ids' => 'required|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'amount_fcfa' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_mode' => 'required',
            'additional_details' => 'nullable',
        ]);

        $last = InpsPayment::orderBy('id', 'desc')->first();
        $nextNum = $last ? ((int) str_replace('INP-', '', $last->number)) + 1 : 1;
        $data['number'] = 'INP-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $payment = InpsPayment::create($data);
        $payment->employees()->attach($request->employee_ids); // Liaison pivot

        return redirect()->route('inpsList')->with('success', 'Paiement INPS enregistré.');
    }

    public function edit($id) {
        $payment = InpsPayment::with('employees')->findOrFail($id);
        $employees = Employee::orderBy('full_name')->get();
        return view('admin.expenses.inps.edit', compact('payment', 'employees'));
    }

    public function update(Request $request, $id) {
        $payment = InpsPayment::findOrFail($id);
        $data = $request->validate([
            'employee_ids' => 'required|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'amount_fcfa' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_mode' => 'required',
            'additional_details' => 'nullable',
        ]);

        $payment->update($data);
        $payment->employees()->sync($request->employee_ids); // Mise à jour pivot

        return redirect()->route('inpsList')->with('success', 'Mis à jour avec succès.');
    }

    public function show($id) {
        $payment = InpsPayment::with('employees')->findOrFail($id);
        return view('admin.expenses.inps.details', compact('payment'));
    }

    public function destroy($id) {
        InpsPayment::findOrFail($id)->delete();
        return back()->with('success', 'Supprimé.');
    }

    public function exportExcel(Request $request) {
        return Excel::download(new InpsPaymentExport($request->searchKey), 'paiements_inps_'.date('d_m_Y').'.xlsx');
    }
}