<?php
use App\Models\Role; // Correct
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\OwnerRegisterController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\OwnerPropertyController;
use App\Http\Controllers\Owner\OwnerUnitController;
use App\Http\Controllers\Owner\TenantController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Owner\TenantRentController;
use App\Http\Controllers\Owner\RentPaymentController;
use App\Http\Controllers\Owner\InvoiceController;
Route::get('/', function () {
    return view('welcome');
});



Route::get('/register/owner', [OwnerRegisterController::class, 'showForm'])->name('owner.register.form');
Route::post('/register/owner', [OwnerRegisterController::class, 'register'])->name('owner.register');



//Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Owner routes
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/property/create', [OwnerPropertyController::class, 'create'])->name('property.create');
    Route::get('/property/index', [OwnerPropertyController::class, 'index'])->name('property.index');
    Route::get('/properties/{property}/edit', [OwnerPropertyController::class, 'edit'])->name('property.edit');
    Route::post('/properties/{property}', [OwnerPropertyController::class, 'update'])->name('property.update');
    Route::post('/properties', [OwnerPropertyController::class, 'store'])->name('property.store');
    Route::get('units', [OwnerUnitController::class, 'index'])->name('units.index');
    Route::get('units/{unit}/edit', [OwnerUnitController::class, 'edit'])->name('units.edit');
    Route::delete('units/{unit}', [OwnerUnitController::class, 'destroy'])->name('units.destroy');
    Route::put('units/{unit}', [OwnerUnitController::class, 'update'])->name('units.update');
    Route::get('units/setup/{property}', [OwnerUnitController::class, 'setup'])->name('units.setup');
    Route::post('units/generate/{property}', [OwnerUnitController::class, 'generate'])->name('units.generate');
    Route::post('units/saveFees/{property}', [OwnerUnitController::class, 'saveFees'])->name('units.saveFees');
    Route::get('/units-by-building/{id}', [TenantController::class, 'getUnitsByBuilding'])->name('units.byBuilding');
    Route::post('/tenants/store', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{tenant}/assign-rent', [TenantRentController::class, 'create'])->name('rents.create');
    Route::post('/tenants/{tenant}/assign-rent', [TenantRentController::class, 'store'])->name('rents.store');
    Route::get('/units-by-building/{id}', [TenantController::class, 'getUnitsByBuilding'])->name('units.byBuilding');
    Route::get('/unit-fees/{unit}', [OwnerUnitController::class, 'getFees'])->name('units.fees');
    Route::get('rent-payments/create',[RentPaymentController::class, 'create'])->name('rent_payments.create');
    Route::post('rent-payments', [RentPaymentController::class, 'store'])->name('rent_payments.store');
    Route::get('rent-payments/fees-dues', [RentPaymentController::class, 'getFeesAndDues'])->name('rent_payments.fees_dues');
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

});



// Supper Admin routes
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/owners', [OwnerController::class, 'index'])->name('admin.owners.index');
        Route::get('/owners/create', [OwnerController::class, 'create'])->name('owners.create');
        Route::post('/owners', [OwnerController::class, 'store'])->name('owners.store');
        Route::get('/owners/{owner}/edit', [OwnerController::class, 'edit'])->name('admin.owners.edit');
        Route::post('/owners/{owner}', [OwnerController::class, 'update'])->name('owners.update');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');



});







require __DIR__.'/auth.php';
