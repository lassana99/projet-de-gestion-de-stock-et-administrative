<?php

namespace App\Exports;

use App\Models\ArticlePrice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ArticlePriceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    /**
     * On reçoit la clé de recherche depuis le contrôleur
     */
    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    /**
     * Récupération de la collection avec la même logique de recherche que le contrôleur
     */
    public function collection()
    {
        $query = ArticlePrice::query();
        $search = $this->searchKey;

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('unit_price', 'like', "%{$search}%")
                  ->orWhere('currency', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%")
                  ->orWhere('incoterm', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // On trie par date de création comme sur la liste
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * En-têtes du fichier Excel
     */
    public function headings(): array
    {
        return [
            'Référence', 
            'Désignation / Machine', 
            'Prix Unitaire', 
            'Devise', 
            'Incoterm', 
            'Pays', 
            'Type', 
            'Fournisseur', 
            'Date'
        ];
    }

    /**
     * Mapping des données pour chaque ligne
     */
    public function map($article): array
    {
        return [
            $article->reference,
            $article->designation,
            round($article->unit_price), // Prix arrondi sans virgule
            $article->currency ?? 'FCFA',
            $article->incoterm ?? '-',
            $article->country ?? '-',
            $article->type,
            $article->supplier_name ?? ($article->supplier?->company_name ?? '-'),
            $article->date ? $article->date->format('d/m/Y') : '-',
        ];
    }
}