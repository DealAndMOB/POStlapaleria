<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReportController;

// Ruta raíz redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Auth::routes();

// Redireccionar /home a /pos
Route::get('/home', function () {
    return redirect()->route('pos.index');
})->name('home');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Ruta predeterminada después del login
    Route::redirect('/dashboard', '/pos')->name('dashboard');
    
    // Rutas para el sistema de punto de venta
    Route::prefix('pos')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('pos.index');
        Route::get('/search', [POSController::class, 'searchProducts'])->name('pos.search');
        Route::post('/process-sale', [POSController::class, 'processSale'])->name('pos.process-sale');
        Route::post('/add-external-product', [POSController::class, 'addExternalProduct'])->name('pos.add-external-product');
        
        // Rutas para manejo de tickets
        Route::get('/print-ticket/{saleId}', [POSController::class, 'printTicket'])->name('pos.print-ticket');
        Route::get('/reprint-ticket/{saleId}', [POSController::class, 'reprintTicket'])->name('pos.reprint-ticket');
        Route::post('/print-thermal/{saleId}', [POSController::class, 'printToThermal'])->name('pos.print-thermal');
        
        // Historial de ventas en POS
        Route::get('/sales-history', [POSController::class, 'getSalesHistory'])->name('pos.sales-history');
    });

    // Rutas para productos
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/find-by-barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('products.find-by-barcode');
        Route::post('/add-inventory', [ProductController::class, 'addInventory'])->name('products.add-inventory');
    });

    // Rutas para categorías
    Route::resource('categories', CategoryController::class);

    // Rutas para ventas y reportes
    Route::prefix('sales')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::get('/report', [SalesReportController::class, 'index'])->name('sales.report');
        Route::get('/{sale}/details', [SalesReportController::class, 'getSaleDetails'])->name('sales.details');
    });

    // Rutas para el cierre de caja
    Route::prefix('cash-register')->group(function () {
        Route::post('/closures', [SalesReportController::class, 'storeClosure'])->name('cash_register_closures.store');
        Route::post('/closures/close', [SalesReportController::class, 'closeClosure'])->name('cash_register_closures.close');
    });
});