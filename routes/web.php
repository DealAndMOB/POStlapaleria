<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReportController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Rutas para el sistema de punto de venta
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [POSController::class, 'searchProducts'])->name('pos.search');
    Route::post('/pos/process-sale', [POSController::class, 'processSale'])->name('pos.process-sale');
    Route::post('/pos/add-external-product', [POSController::class, 'addExternalProduct'])->name('pos.add-external-product');
    Route::post('/pos/print-ticket/{sale}', [POSController::class, 'printTicket'])->name('pos.print-ticket');

    // Rutas para productos, categorías y ventas
    Route::resource('products', ProductController::class);
    Route::get('/products/find-by-barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('products.find-by-barcode');
    Route::post('/products/add-inventory', [ProductController::class, 'addInventory'])->name('products.add-inventory');
    Route::resource('categories', CategoryController::class);
    Route::resource('sales', SaleController::class);

    // Nuevas rutas para el reporte de ventas
    Route::get('/sales-report', [SalesReportController::class, 'index'])->name('sales.report');
    Route::post('/cash-register-closures', [SalesReportController::class, 'storeClosure'])->name('cash_register_closures.store');
    Route::post('/cash-register-closures/close', [SalesReportController::class, 'closeClosure'])->name('cash_register_closures.close');
    Route::get('/sales/{sale}', [SalesReportController::class, 'getSaleDetails'])->name('sales.details');
});