<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::all();
        return view('pos.index', compact('categories'));
    }

    public function searchProducts(Request $request)
    {
        $term = $request->input('term');
        $products = Product::where('name', 'LIKE', "%{$term}%")
            ->orWhere('barcode', 'LIKE', "%{$term}%")
            ->with('category')
            ->get();
        return response()->json($products);
    }

    public function getProduct($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    public function processSale(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'items' => 'required|array',
                'items.*.id' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (!Str::startsWith($value, 'ext_') && !Product::find($value)) {
                            $fail('El producto seleccionado no es válido.');
                        }
                    },
                ],
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.name' => 'required|string',
                'total' => 'required|numeric|min:0',
                'amount_paid' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            $sale = Sale::create([
                'total' => $validatedData['total'],
                'amount_paid' => $validatedData['amount_paid'],
                'change' => $validatedData['amount_paid'] - $validatedData['total'],
            ]);

            foreach ($validatedData['items'] as $item) {
                if (Str::startsWith($item['id'], 'ext_')) {
                    // Producto externo
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => null,
                        'external_product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                } else {
                    // Producto regular
                    $product = Product::findOrFail($item['id']);
                    
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("No hay suficiente stock para el producto {$product->name}");
                    }

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                    ]);

                    $product->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'sale' => $sale]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function addExternalProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $externalProduct = new \stdClass();
        $externalProduct->id = 'ext_' . uniqid();
        $externalProduct->name = $request->name;
        $externalProduct->price = $request->price;
        $externalProduct->is_external = true;

        return response()->json($externalProduct);
    }
}