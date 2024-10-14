<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\CashRegisterClosure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesReportController extends Controller
{
    public function index()
    {
        $activeClosure = CashRegisterClosure::whereNull('end_time')->latest()->first();
        $recentSales = Sale::with('items.product')->orderBy('created_at', 'desc')->take(10)->get();
        $previousClosures = CashRegisterClosure::whereNotNull('end_time')->orderBy('end_time', 'desc')->take(10)->get();

        return view('reports.sales_report', compact('activeClosure', 'recentSales', 'previousClosures'));
    }

    public function storeClosure(Request $request)
    {
        $validated = $request->validate([
            'initial_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $closure = new CashRegisterClosure($validated);
        $closure->start_time = now();
        $closure->user_id = Auth::id();
        $closure->save();

        return redirect()->route('sales.report')->with('success', 'Turno iniciado correctamente');
    }


    public function closeClosure(Request $request)
    {
        $activeClosure = CashRegisterClosure::whereNull('end_time')->latest()->firstOrFail();

        $validated = $request->validate([
            'final_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $totalSales = Sale::whereBetween('created_at', [$activeClosure->start_time, now()])->sum('total');
        $expectedCash = $activeClosure->initial_cash + $totalSales;
        $difference = $validated['final_cash'] - $expectedCash;

        $activeClosure->update([
            'end_time' => now(),
            'final_cash' => $validated['final_cash'],
            'total_sales' => $totalSales,
            'expected_cash' => $expectedCash,
            'difference' => $difference,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('sales.report')->with('success', 'Turno cerrado correctamente');
    }


    public function getSaleDetails(Sale $sale)
    {
        $sale->load('items.product');
        return view('reports.sale_details', compact('sale'));
    }
}