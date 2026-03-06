<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\FundingController;
use App\Http\Controllers\Admin\SaleInfoController;
use App\Http\Controllers\Admin\OrderBoardController;
use App\Http\Controllers\Admin\RoleChangeController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PurchaseInfoController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProspectController;
use App\Http\Controllers\Admin\DevisController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\ArticlePriceController;
use App\Http\Controllers\Admin\SettlementController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\OtherExpenseController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\InpsPaymentController;



//admin
Route::group(['prefix' => 'admin' , 'middleware' => ['auth','admin']], function(){
    Route::get('/home', [AdminDashboardController::class, 'index'])->name('adminDashboard');

    // funding routes
    Route::prefix('funding')->group(function(){
         // Pour l'exportation Excel
        Route::get('export-excel', [FundingController::class, 'exportExcel'])->name('funding.exportExcel');
        Route::get('list', [FundingController::class, 'index'])->name('funding.list');
        Route::get('create', [FundingController::class, 'create'])->name('funding.create');
        Route::post('store', [FundingController::class, 'store'])->name('funding.store');
        Route::get('details/{funding}', [FundingController::class, 'show'])->name('funding.details');
        Route::get('edit/{funding}', [FundingController::class, 'edit'])->name('funding.edit');
        Route::put('update/{funding}', [FundingController::class, 'update'])->name('funding.update');
        Route::delete('delete/{funding}', [FundingController::class, 'destroy'])->name('funding.destroy');
    });

    // Settlement (Règlements / Dettes & Créances)
Route::prefix('reglements')->group(function () {
    Route::get('settlements/export-excel', [SettlementController::class, 'exportExcel'])->name('settlementExportExcel');
    // Liste
    Route::get('/', [SettlementController::class, 'index'])->name('settlementList');
    
    // Création
    Route::get('/nouveau', [SettlementController::class, 'create'])->name('settlementCreate');
    Route::post('/store', [SettlementController::class, 'store'])->name('settlementStore');

    // AJOUT : Détails (indispensable pour fixer votre erreur)
    Route::get('/details/{id}', [SettlementController::class, 'show'])->name('settlementDetails');

    // AJOUT : Modification (Edit & Update)
    Route::get('/modifier/{id}', [SettlementController::class, 'edit'])->name('settlementEdit');
    Route::put('/update/{id}', [SettlementController::class, 'update'])->name('settlementUpdate');

    // Statut & Suppression
    Route::patch('/update-status/{id}', [SettlementController::class, 'updateStatus'])->name('settlementUpdateStatus');
    Route::delete('/delete/{id}', [SettlementController::class, 'destroy'])->name('settlementDelete');
});
    
    //product
    Route::prefix('product')->group(function(){
        Route::get('product/export-excel', [ProductController::class, 'exportExcel'])->name('productExportExcel');
        Route::get('list',[ProductController::class, 'list'])->name('productList');
        Route::get('create',[ProductController::class, 'createPage'])->name('productCreate');
        Route::post('create',[ProductController::class, 'productCreate'])->name('product#Create');
        Route::get('delete/{id}',[ProductController::class, 'delete'])->name('productDelete');
        Route::get('details/{id}',[ProductController::class, 'details'])->name('productDetails');
        Route::get('edit/{id}',[ProductController::class, 'edit'])->name('productEdit');
        Route::post('update',[ProductController::class, 'update'])->name('productUpdate');

    });

    // Livraisons
    Route::prefix('deliveries')->group(function () {
        Route::get('list', [DeliveryController::class, 'list'])->name('deliveryList');
        Route::get('create', [DeliveryController::class, 'createPage'])->name('deliveryCreate');
        Route::post('create', [DeliveryController::class, 'deliveryCreate'])->name('delivery#Create');
        Route::get('details/{id}', [DeliveryController::class, 'details'])->name('deliveryDetails');
        Route::get('edit/{id}', [DeliveryController::class, 'edit'])->name('deliveryEdit');
        Route::put('update', [DeliveryController::class, 'update'])->name('deliveryUpdate');
        Route::delete('delete/{id}', [DeliveryController::class, 'delete'])->name('deliveryDelete');
    });

    //payment
        // routes/web.php
    Route::prefix('paiements')->group(function () {
        Route::get('payments/export-excel', [PaymentController::class, 'exportExcel'])->name('paymentExportExcel');
        Route::get('/', [PaymentController::class, 'list'])->name('paymentList');
        Route::get('/nouveau', [PaymentController::class, 'create'])->name('paymentCreate');
        Route::post('/enregistrer', [PaymentController::class, 'store'])->name('paymentStore');
        Route::get('/modifier/{id}', [PaymentController::class, 'edit'])->name('paymentEdit');
        Route::put('/update/{id}', [PaymentController::class, 'update'])->name('paymentUpdate');
        Route::get('/details/{id}', [PaymentController::class, 'details'])->name('paymentDetails');
        Route::delete('/supprimer/{id}', [PaymentController::class, 'delete'])->name('paymentDelete');
    });


    //Profitability Machine
    Route::prefix('machines_profitabilities')->group(function () {
        //Exportation en excel
        Route::get('export-excel', [MachineController::class, 'exportExcel'])->name('machineExportExcel');
        Route::get('list', [MachineController::class, 'list'])->name('machineList');
        Route::get('create', [MachineController::class, 'createPage'])->name('machineCreate');
        Route::post('create', [MachineController::class, 'machineCreate'])->name('machine#Create');
        Route::get('details/{id}', [MachineController::class, 'details'])->name('machineDetails');
        Route::get('edit/{id}', [MachineController::class, 'edit'])->name('machineEdit');
        Route::put('update', [MachineController::class, 'update'])->name('machineUpdate');
        Route::delete('delete/{id}', [MachineController::class, 'delete'])->name('machineDelete');
    });

    //Partner Supplier
    Route::prefix('suppliers')->group(function () {
        Route::get('supplier/export-excel', [SupplierController::class, 'exportExcel'])->name('supplierExportExcel');
        Route::get('list', [SupplierController::class, 'list'])->name('supplierList');
        Route::get('create', [SupplierController::class, 'createPage'])->name('supplierCreate');
        Route::post('create', [SupplierController::class, 'supplierCreate'])->name('supplier#Create');
        Route::get('details/{id}', [SupplierController::class, 'details'])->name('supplierDetails');
        Route::get('edit/{id}', [SupplierController::class, 'edit'])->name('supplierEdit');
        Route::put('update', [SupplierController::class, 'update'])->name('supplierUpdate');
        Route::delete('delete/{id}', [SupplierController::class, 'delete'])->name('supplierDelete');
    });

    //Partner Customer
    Route::prefix('customers')->group(function () {
        Route::get('customer/export-excel', [CustomerController::class, 'exportExcel'])->name('customerExportExcel');
        Route::get('list', [CustomerController::class, 'list'])->name('customerList');
        Route::get('create', [CustomerController::class, 'createPage'])->name('customerCreate');
        Route::post('create', [CustomerController::class, 'customerCreate'])->name('customer#Create');
        Route::get('details/{id}', [CustomerController::class, 'details'])->name('customerDetails');
        Route::get('edit/{id}', [CustomerController::class, 'edit'])->name('customerEdit');
        Route::put('update', [CustomerController::class, 'update'])->name('customerUpdate');
        Route::delete('delete/{id}', [CustomerController::class, 'delete'])->name('customerDelete');
    });

    //Partner Prospect
    Route::prefix('prospects')->group(function () {
        Route::get('prospect/export-excel', [ProspectController::class, 'exportExcel'])->name('prospectExportExcel');
        Route::get('list', [ProspectController::class, 'list'])->name('prospectList');
        Route::get('create', [ProspectController::class, 'createPage'])->name('prospectCreate');
        Route::post('create', [ProspectController::class, 'prospectCreate'])->name('prospect#Create');
        Route::get('details/{id}', [ProspectController::class, 'details'])->name('prospectDetails');
        Route::get('edit/{id}', [ProspectController::class, 'edit'])->name('prospectEdit');
        Route::put('update', [ProspectController::class, 'update'])->name('prospectUpdate');
        Route::delete('delete/{id}', [ProspectController::class, 'delete'])->name('prospectDelete');
    });

    // Conversion de prospect en client
    Route::get('prospect/convert/{id}', [ProspectController::class, 'convertToClient'])->name('prospectConvert');


    //password
    Route::prefix('password')->group(function(){
        Route::get('change', [AuthController::class, 'changePasswordPage'])->name('passwordChange');
        Route::post('change', [AuthController::class, 'changePassword'])->name('changePassword');
        Route::get('reset', [AuthController::class, 'resetPasswordPage'])->name('resetPasswordPage');
        Route::post('resetPassword', [AuthController::class, 'resetPassword'])->name('resetPassword');

    });

  // Profile Admin Routes
Route::prefix('profile')->group(function() {
    // Affichage et mise à jour du profil existant
    Route::get('detail', [ProfileController::class, 'profileDetails'])->name('profileDetails');
    Route::post('update', [ProfileController::class, 'update'])->name('adminProfileUpdate');
    Route::get('account/{id}', [ProfileController::class, 'accountProfile'])->name('accountProfile');

    // Création de nouveaux comptes administrateurs
    // GET: Affiche le formulaire
    Route::get('create/adminAccount', [ProfileController::class, 'createAdminAccount'])->name('createAdminAccount');
    
    // POST: Traite les données envoyées par le formulaire
    // Note : J'utilise 'storeAdminAccount' pour correspondre à la logique Laravel (Store = Sauvegarder)
    Route::post('create/adminAccount', [ProfileController::class, 'storeAdminAccount'])->name('createAdmin');
});

    //role admin and user
    Route::prefix('role')->group(function(){
        Route::get('list',[RoleChangeController::class, 'adminList'])->name('adminList');
        Route::get('deleteAdminAccount/{id}',[RoleChangeController::class, 'deleteAdminAccount'])->name('deleteAdminAccount');
        Route::get('changeUserRole/{id}',[RoleChangeController::class, 'changeUserRole'])->name('changeUserRole');
    //role user
        Route::get('userList',[RoleChangeController::class, 'userList'])->name('userList');
        Route::get('deleteUserAccount/{id}',[RoleChangeController::class, 'deleteUserAccount'])->name('deleteUserAccount');
        Route::get('changeAdminRole/{id}',[RoleChangeController::class, 'changeAdminRole'])->name('changeAdminRole');

    });

    //Order Board Page
    Route::prefix('order')->group(function(){
        Route::get('list',[OrderBoardController::class, 'orderListPage'])->name('orderListPage');
        Route::get('details/{orderCode}',[OrderBoardController::class, 'userOrderDetails'])->name('userOrderDetails');
        Route::get('change/status',[OrderBoardController::class, 'changeStatus'])->name('changeStatus');
        Route::post('update/status', [OrderBoardController::class, 'updateStatus'])->name('updateOrderStatus');
        Route::post('reject', [OrderBoardController::class, 'rejectOrder']);
        Route::post('removereject', [OrderBoardController::class, 'removeRejectReason']);
    });

    //Sale Info
    Route::prefix('saleinfo')->group(function(){
        Route::get('list', [SaleInfoController::class, 'saleInfoList'])->name('saleInfoList');
    });

    // Reports
    Route::prefix('reports')->group(function(){
        Route::get('salesReportPage', [SaleInfoController::class, 'salesReportPage'])->name('salesReportPage');
        Route::get('sales',[SaleInfoController::class, 'salesReport'])->name('salesReport');
        Route::get('productReportPage',[SaleInfoController::class, 'productReportPage'])->name('productReportPage');
        Route::get('productReport',[SaleInfoController::class, 'productReport'])->name('productReport');
        Route::get('profitlossreportpage',[SaleInfoController::class, 'profitlossreportpage'])->name('profitlossreportpage');
        Route::get('profitlossReport',[SaleInfoController::class, 'profitlossReport'])->name('profitlossReport');

    });
        // Purchase Info

    Route::prefix('admin/purchases')->group(function() {
        // Ajoutez cette ligne pour l'exportation Excel
        Route::get('export-excel', [PurchaseInfoController::class, 'exportExcel'])->name('purchaseExportExcel');
        Route::get('list', [PurchaseInfoController::class, 'index'])->name('purchaseList');
        Route::get('create', [PurchaseInfoController::class, 'create'])->name('purchaseCreate');
        Route::post('store', [PurchaseInfoController::class, 'store'])->name('purchaseStore');
        Route::get('details/{id}', [PurchaseInfoController::class, 'show'])->name('purchaseDetails');
        Route::get('edit/{id}', [PurchaseInfoController::class, 'edit'])->name('purchaseEdit');
        Route::put('update/{id}', [PurchaseInfoController::class, 'update'])->name('purchaseUpdate');
        Route::delete('delete/{id}', [PurchaseInfoController::class, 'destroy'])->name('purchaseDelete');
    });
    // Article Prices
    Route::prefix('admin/article-prices')->group(function() {
        // Liste et Export Excel (Export Word supprimé)
        Route::get('list', [ArticlePriceController::class, 'index'])->name('articlePriceList');
        Route::get('export-excel', [ArticlePriceController::class, 'exportExcel'])->name('articlePriceExportExcel');

        // Création
        Route::get('create', [ArticlePriceController::class, 'create'])->name('articlePriceCreate');
        Route::post('store', [ArticlePriceController::class, 'store'])->name('articlePriceStore');

        // Modification et Détails
        Route::get('edit/{id}', [ArticlePriceController::class, 'edit'])->name('articlePriceEdit');
        Route::put('update/{id}', [ArticlePriceController::class, 'update'])->name('articlePriceUpdate');
        Route::get('details/{id}', [ArticlePriceController::class, 'show'])->name('articlePriceDetails');

        // Suppression
        Route::delete('delete/{id}', [ArticlePriceController::class, 'destroy'])->name('articlePriceDelete');
    });


    // Devis
    Route::prefix('devis')->group(function () {
        Route::get('devis/export-excel', [DevisController::class, 'exportExcel'])->name('devis.exportExcel');
        Route::get('list', [DevisController::class, 'index'])->name('devis.list');
        Route::get('create', [DevisController::class, 'create'])->name('devis.create');
        Route::post('store', [DevisController::class, 'store'])->name('devis.store');
        Route::get('details/{devis}', [DevisController::class, 'show'])->name('devis.details');
        Route::get('edit/{devis}', [DevisController::class, 'edit'])->name('devis.edit');
        Route::put('update/{devis}', [DevisController::class, 'update'])->name('devis.update');
        Route::delete('delete/{devis}', [DevisController::class, 'destroy'])->name('devis.delete');
        Route::get('pdf/{devis}', [DevisController::class, 'pdf'])->name('devis.pdf');
        Route::get('print/{devis}', [DevisController::class, 'print'])->name('devis.print');
        // Route pour envoyer un devis par email
        Route::post('/devis/{devis}/send-email', [DevisController::class, 'sendEmail'])->name('devis.sendEmail');

    });

 // Factures
    Route::prefix('invoices')->group(function () {
        Route::get('invoices/export-excel', [InvoiceController::class, 'exportExcel'])->name('invoices.exportExcel');
        Route::get('list', [InvoiceController::class, 'index'])->name('invoices.list');
        Route::get('create', [InvoiceController::class, 'create'])->name('invoices.create'); 
        Route::post('store', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('details/{invoice}', [InvoiceController::class, 'show'])->name('invoices.details');
        Route::get('pdf/{invoice}', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

        // AJOUT DES ROUTES MANQUANTES POUR L'ÉDITION
        // Route pour afficher le formulaire d'édition
        Route::get('edit/{invoice}', [InvoiceController::class, 'edit'])->name('invoices.edit'); 
        // Route pour soumettre la modification (utilise la méthode PUT ou PATCH)
        Route::put('{invoice}', [InvoiceController::class, 'update'])->name('invoices.update'); 
        // Fin de l'ajout

        Route::delete('delete/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.delete');
        Route::get('pdf/{invoice}', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
        Route::get('print/{invoice}', [InvoiceController::class, 'print'])->name('invoices.print');
    });

    // Bordereaux de Livraison
    Route::group(['prefix' => 'delivery-notes', 'as' => 'delivery_notes.', 'middleware' => ['auth']], function () {
        
        // ICI : On enlève "delivery_notes." du nom car le groupe l'ajoute déjà
        Route::get('export-excel', [DeliveryNoteController::class, 'exportExcel'])->name('exportExcel');

        // 1.1 Route Spécifique pour la création depuis une facture
        Route::get('create-from-invoice/{invoiceId}', [DeliveryNoteController::class, 'createFromInvoice'])->name('create_from_invoice');
        
        // Affiche le formulaire de création d'un nouveau BL
        Route::get('create', [DeliveryNoteController::class, 'create'])->name('create');
        
        // 1.2 Routes CRUD Conventionnelles
        
        // GET /delivery-notes : Liste de tous les BL -> Nom final: delivery_notes.list
        Route::get('/', [DeliveryNoteController::class, 'index'])->name('list'); 
        
        // POST /delivery-notes : Enregistre le nouveau BL -> Nom final: delivery_notes.store
        Route::post('/', [DeliveryNoteController::class, 'store'])->name('store');
        
        // GET /delivery-notes/{deliveryNote} : Détails -> Nom final: delivery_notes.show
        Route::get('{deliveryNote}', [DeliveryNoteController::class, 'show'])->name('show');
        
        // GET /delivery-notes/{deliveryNote}/edit : Edition -> Nom final: delivery_notes.edit
        Route::get('{deliveryNote}/edit', [DeliveryNoteController::class, 'edit'])->name('edit');
        
        // PUT /delivery-notes/{deliveryNote} : Update -> Nom final: delivery_notes.update
        Route::put('{deliveryNote}', [DeliveryNoteController::class, 'update'])->name('update');
        
        // DELETE /delivery-notes/{deliveryNote} : Supprimer -> Nom final: delivery_notes.delete
        Route::delete('{deliveryNote}', [DeliveryNoteController::class, 'destroy'])->name('delete');
        Route::get('{deliveryNote}/pdf', [DeliveryNoteController::class, 'pdf'])->name('pdf');
    });

        //Dépenses/Loyers
    Route::prefix('admin/expenses/rent')->group(function() {
        Route::get('list', [RentController::class, 'index'])->name('rentList');
        Route::get('export', [RentController::class, 'exportExcel'])->name('rentExportExcel');
        Route::get('create', [RentController::class, 'create'])->name('rentCreate');
        Route::post('store', [RentController::class, 'store'])->name('rentStore');
        Route::get('edit/{id}', [RentController::class, 'edit'])->name('rentEdit');
        Route::put('update/{id}', [RentController::class, 'update'])->name('rentUpdate');
        Route::get('details/{id}', [RentController::class, 'show'])->name('rentDetails');
        Route::delete('delete/{id}', [RentController::class, 'destroy'])->name('rentDelete');
    });

        // Dépenses / Impôts
    Route::prefix('admin/expenses/tax')->group(function() {
        // Liste et Export Excel
        Route::get('list', [TaxController::class, 'index'])->name('taxList');
        Route::get('export-excel', [TaxController::class, 'exportExcel'])->name('taxExportExcel');

        // Création
        Route::get('create', [TaxController::class, 'create'])->name('taxCreate');
        Route::post('store', [TaxController::class, 'store'])->name('taxStore');

        // Modification et Détails
        Route::get('edit/{id}', [TaxController::class, 'edit'])->name('taxEdit');
        Route::put('update/{id}', [TaxController::class, 'update'])->name('taxUpdate');
        Route::get('details/{id}', [TaxController::class, 'show'])->name('taxDetails');

        // Suppression
        Route::delete('delete/{id}', [TaxController::class, 'destroy'])->name('taxDelete');
    });
        
     // Dépenses / Charges Utilitaires (Eau, Électricité, Internet)
    Route::prefix('admin/expenses/utility')->group(function() {
        Route::get('list', [UtilityController::class, 'index'])->name('utilityList');
        Route::get('export', [UtilityController::class, 'exportExcel'])->name('utilityExportExcel');
        Route::get('create', [UtilityController::class, 'create'])->name('utilityCreate');
        Route::post('store', [UtilityController::class, 'store'])->name('utilityStore');
        Route::get('edit/{id}', [UtilityController::class, 'edit'])->name('utilityEdit');
        Route::put('update/{id}', [UtilityController::class, 'update'])->name('utilityUpdate');
        Route::get('details/{id}', [UtilityController::class, 'show'])->name('utilityDetails');
        Route::delete('delete/{id}', [UtilityController::class, 'destroy'])->name('utilityDelete');
    });

    // Dépenses / Autres Charges (Frais de transport, fournitures de bureau, etc.)
    Route::prefix('admin/expenses/other')->group(function() {
        Route::get('list', [OtherExpenseController::class, 'index'])->name('otherExpenseList');
        Route::get('export', [OtherExpenseController::class, 'exportExcel'])->name('otherExpenseExportExcel');
        Route::get('create', [OtherExpenseController::class, 'create'])->name('otherExpenseCreate');
        Route::post('store', [OtherExpenseController::class, 'store'])->name('otherExpenseStore');
        Route::get('edit/{id}', [OtherExpenseController::class, 'edit'])->name('otherExpenseEdit');
        Route::put('update/{id}', [OtherExpenseController::class, 'update'])->name('otherExpenseUpdate');
        Route::get('details/{id}', [OtherExpenseController::class, 'show'])->name('otherExpenseDetails');
        Route::delete('delete/{id}', [OtherExpenseController::class, 'destroy'])->name('otherExpenseDelete');
    });

    // Dépenses / Salaires
    Route::prefix('admin/expenses/salary')->group(function() {
        Route::get('list', [SalaryController::class, 'index'])->name('salaryList');
        Route::get('export', [SalaryController::class, 'exportExcel'])->name('salaryExportExcel');
        Route::get('create', [SalaryController::class, 'create'])->name('salaryCreate');
        Route::post('store', [SalaryController::class, 'store'])->name('salaryStore');
        Route::get('edit/{id}', [SalaryController::class, 'edit'])->name('salaryEdit');
        Route::put('update/{id}', [SalaryController::class, 'update'])->name('salaryUpdate');
        Route::get('details/{id}', [SalaryController::class, 'show'])->name('salaryDetails');
        Route::delete('delete/{id}', [SalaryController::class, 'destroy'])->name('salaryDelete');
    });

// =========================================================
// SECTION : RESSOURCES HUMAINES (RH)
// =========================================================
Route::prefix('admin/rh')->group(function() {
    
    // --- GESTION DU PERSONNEL ---
    
    // Liste des employés et Exportation
    Route::get('employees', [EmployeeController::class, 'index'])->name('employeeList');
    Route::get('employees/export-excel', [EmployeeController::class, 'exportExcel'])->name('employeeExportExcel');

    // Création
    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employeeCreate');
    Route::post('employees/store', [EmployeeController::class, 'store'])->name('employeeStore');

    // Modification
    Route::get('employees/edit/{id}', [EmployeeController::class, 'edit'])->name('employeeEdit');
    Route::put('employees/update/{id}', [EmployeeController::class, 'update'])->name('employeeUpdate');

    // Détails (Fiche employé + historique congés)
    Route::get('employees/details/{id}', [EmployeeController::class, 'show'])->name('employeeDetails');

    // Suppression
    Route::delete('employees/delete/{id}', [EmployeeController::class, 'destroy'])->name('employeeDelete');
;
});

// --- GESTION DES CONGÉS ---
Route::prefix('admin/rh')->group(function() {
    // --- ROUTES CONGÉS ---
    Route::get('leaves', [LeaveController::class, 'index'])->name('leaveList');
    Route::get('leaves/create', [LeaveController::class, 'create'])->name('leaveCreate');
    Route::post('leaves/store', [LeaveController::class, 'store'])->name('leaveStore');
    
    // Les routes qui manquaient :
    Route::get('leaves/details/{id}', [LeaveController::class, 'show'])->name('leaveDetails');
    Route::get('leaves/edit/{id}', [LeaveController::class, 'edit'])->name('leaveEdit');
    Route::put('leaves/update/{id}', [LeaveController::class, 'update'])->name('leaveUpdate');
    
    // Autres actions
    Route::patch('leaves/status/{id}', [LeaveController::class, 'updateStatus'])->name('leaveUpdateStatus');
    Route::delete('leaves/delete/{id}', [LeaveController::class, 'destroy'])->name('leaveDelete');
    Route::get('leaves/export', [LeaveController::class, 'exportExcel'])->name('leaveExportExcel');
});
//Salaires / Charges Sociales - INPS
Route::prefix('admin/expenses/inps')->group(function() {
    Route::get('list', [InpsPaymentController::class, 'index'])->name('inpsList');
    Route::get('create', [InpsPaymentController::class, 'create'])->name('inpsCreate');
    Route::post('store', [InpsPaymentController::class, 'store'])->name('inpsStore');
    Route::get('details/{id}', [InpsPaymentController::class, 'show'])->name('inpsDetails');
    Route::get('edit/{id}', [InpsPaymentController::class, 'edit'])->name('inpsEdit');
    Route::put('update/{id}', [InpsPaymentController::class, 'update'])->name('inpsUpdate');
    Route::delete('delete/{id}', [InpsPaymentController::class, 'destroy'])->name('inpsDelete');
    Route::get('export', [InpsPaymentController::class, 'exportExcel'])->name('inpsExportExcel');
});

// Route pour le livre de comptes (bilan financier) et son exportation Excel
Route::get('livre-de-comptes', [App\Http\Controllers\Admin\AccountBookController::class, 'index'])->name('accountBook');
Route::get('livre-de-comptes/export', [App\Http\Controllers\Admin\AccountBookController::class, 'exportExcel'])->name('accountBookExportExcel');

// Route pour envoyer une facture par email
Route::post('/invoices/{invoice}/send-email', [App\Http\Controllers\InvoiceController::class, 'sendEmail'])->name('invoices.sendEmail');

// Route pour envoyer un bordereau de livraison par email
Route::post('/delivery-notes/{deliveryNote}/send-email', [App\Http\Controllers\DeliveryNoteController::class, 'sendEmail'])->name('delivery_notes.sendEmail');
});
