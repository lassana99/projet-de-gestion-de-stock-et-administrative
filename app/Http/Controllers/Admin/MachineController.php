<?php

namespace App\Http\Controllers\Admin;

use App\Models\Machine;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
// Importations pour l'exportation Excel
use App\Exports\MachineExport;
use Maatwebsite\Excel\Facades\Excel;

class MachineController extends Controller
{
    /**
     * Helper pour formater l'affichage (utilisé pour la liste)
     */
    protected function formatNumberNoDecimal(?float $number): ?string
    {
        if ($number === null) return null;
        return number_format($number, 0, ',', ' ');
    }

    /**
     * Centralisation de la logique de filtrage pour la réutiliser (Web + Excel)
     */
    private function applyFilters($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_reference', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Liste des rentabilités
     */
    public function list(Request $request)
    {
        $query = Machine::with('purchase');

        // Application des filtres
        $this->applyFilters($query, $request->input('searchKey'));

        // Tri par date de rentabilité (la plus récente en premier)
        $machines = $query->orderBy('date_profitability', 'desc')->paginate(10);

        // On ajoute des propriétés virtuelles (formatted)
        foreach ($machines as $machine) {
            $machine->profit_formatted = $this->formatNumberNoDecimal($machine->profit);
            $machine->selling_price_formatted = $this->formatNumberNoDecimal($machine->selling_price);
            $machine->tva_formatted = $this->formatNumberNoDecimal($machine->tva);
            $machine->selling_price_ttc_formatted = $this->formatNumberNoDecimal($machine->selling_price_ttc);
        }

        return view('admin.profitability.machine.list', compact('machines'));
    }

    /**
     * EXPORT EXCEL UNIQUEMENT
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        // On génère le fichier Excel en passant la clé de recherche à la classe Export
        return Excel::download(new MachineExport($searchKey), 'rentabilite_machines_' . date('d_m_Y_Hi') . '.xlsx');
    }

    public function createPage()
    {
        $purchases = Purchase::all();
        return view('admin.profitability.machine.create', compact('purchases'));
    }

    /**
     * Création d'une rentabilité
     */
    public function machineCreate(Request $request)
    {
        $this->cleanNumberFields($request);

        $data = $request->validate([
            'purchase_reference'       => 'required|string|max:255',
            'unit_purchase_price'      => 'required|numeric|min:0',
            'weight'                   => 'nullable|numeric|min:0',
            'brand'                    => 'required|string|max:255',
            'status'                   => 'required|string|max:255',
            'date_profitability'       => 'required|date', 
            'conversion_rate'          => 'nullable|numeric|min:0',
            'urban_transport'          => 'nullable|numeric|min:0',
            'concierge'                => 'nullable|numeric|min:0',
            'transport_source_bko'     => 'nullable|numeric|min:0',
            'customs'                  => 'nullable|numeric|min:0',
            'land_transport'           => 'nullable|numeric|min:0',
            'margin'                   => 'required|numeric|min:0',
            'funding'                  => 'nullable|numeric|min:0',
            'suppliername'             => 'nullable|string|max:255',
            'request'                  => 'nullable|string|max:255',
            'global_urbain_transport'  => 'nullable|numeric|min:0',
            'quantity'                 => 'nullable|integer|min:1',
        ]);

        $calcResults = $this->performCalculations($data);
        $data = array_merge($data, $calcResults);

        Machine::create($data);

        Alert::success('Succès', 'Rentabilité ajoutée avec succès');
        return redirect()->route('machineList');
    }

    public function details($id)
    {
        $machine = Machine::findOrFail($id);
        return view('admin.profitability.machine.details', compact('machine'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        $profitability = Machine::findOrFail($id);
        $purchases = Purchase::all();

        return view('admin.profitability.machine.edit', compact('profitability', 'purchases'));
    }

    /**
     * Mise à jour de la rentabilité
     */
    public function update(Request $request)
    {
        $this->cleanNumberFields($request);

        $data = $request->validate([
            'id'                      => 'required|exists:machines_profitabilities,id',
            'purchase_reference'       => 'required|string|max:255',
            'unit_purchase_price'      => 'required|numeric|min:0',
            'weight'                   => 'nullable|numeric|min:0',
            'brand'                    => 'required|string|max:255',
            'status'                   => 'required|string|max:255',
            'date_profitability'       => 'required|date',
            'conversion_rate'          => 'nullable|numeric|min:0',
            'urban_transport'          => 'nullable|numeric|min:0',
            'concierge'                => 'nullable|numeric|min:0',
            'transport_source_bko'     => 'nullable|numeric|min:0',
            'customs'                  => 'nullable|numeric|min:0',
            'land_transport'           => 'nullable|numeric|min:0',
            'margin'                   => 'required|numeric|min:0',
            'funding'                  => 'nullable|numeric|min:0',
            'suppliername'             => 'nullable|string|max:255',
            'request'                  => 'nullable|string|max:255',
            'global_urbain_transport' => 'nullable|numeric|min:0',
            'quantity'                => 'nullable|integer|min:1',
        ]);

        $machine = Machine::findOrFail($data['id']);

        $calcResults = $this->performCalculations($data);
        $data = array_merge($data, $calcResults);

        $machine->update($data);

        Alert::success('Succès', 'Rentabilité modifiée avec succès');
        return redirect()->route('machineList');
    }

    /**
     * Centralisation des calculs financiers
     */
    private function performCalculations(array $data): array
    {
        $quantity = $data['quantity'] ?? 1;
        if ($quantity <= 0) $quantity = 1;

        $globalUrban = $data['global_urbain_transport'] ?? 0;
        $urbanTransportItem = $globalUrban / $quantity;

        $priceUnit = $data['unit_purchase_price'];
        $weight = $data['weight'] ?? 0;
        $conversionRate = $data['conversion_rate'] ?? 1;
        $concierge = $data['concierge'] ?? 0;
        $transportSourceBko = $data['transport_source_bko'] ?? 0;
        $customs = $data['customs'] ?? 0;
        $landTransport = $data['land_transport'] ?? 0;
        $marginPercent = $data['margin'];
        $fundingValue = $data['funding'] ?? 0;

        $revienSansTrans = $priceUnit + $urbanTransportItem + $concierge + ($transportSourceBko * $weight) + (($customs / 100) * $priceUnit);
        $reviensConverti = $revienSansTrans * $conversionRate;
        $reviensTotalHt = $reviensConverti + $landTransport;
        
        $profit = ($marginPercent / 100) * $reviensTotalHt;
        $sellingPriceHt = $profit + $reviensTotalHt + $fundingValue;
        
        $tva = $sellingPriceHt * 0.18;
        $sellingPriceTtc = $sellingPriceHt + $tva;

        return [
            'urban_transport'   => $urbanTransportItem,
            'profit'            => $profit,
            'selling_price'     => $sellingPriceHt,
            'tva'               => $tva,
            'selling_price_ttc' => $sellingPriceTtc
        ];
    }

    public function delete($id)
    {
        $machine = Machine::findOrFail($id);
        $machine->delete();

        Alert::success('Succès', 'Rentabilité supprimée avec succès');
        return redirect()->route('machineList');
    }

    protected function cleanNumberFields(Request $request): void
    {
        $numberFields = [
            'unit_purchase_price', 'weight', 'conversion_rate', 'urban_transport',
            'concierge', 'transport_source_bko', 'customs', 'land_transport',
            'margin', 'funding', 'global_urbain_transport'
        ];

        foreach ($numberFields as $field) {
            if ($request->has($field)) {
                $value = $request->input($field);
                if (is_string($value)) {
                    $request->merge([$field => str_replace(' ', '', $value)]);
                }
            }
        }
    }
}