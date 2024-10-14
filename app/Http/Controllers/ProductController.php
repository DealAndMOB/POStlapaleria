<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with('category');
            return Datatables::of($products)
                ->addColumn('action', function ($product) {
                    $editBtn = '<button onclick="editProduct('.$product->id.')" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Editar</button>';
                    $deleteBtn = '<button onclick="deleteProduct('.$product->id.')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Eliminar</button>';
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = Category::all();
        return view('products.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'barcode' => 'required|unique:products,barcode',
            'cost' => 'required|numeric|min:0',
            'profit_percentage' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($validated);

        return response()->json(['success' => true, 'product' => $product]);
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:255',
            'barcode' => 'required|unique:products,barcode,'.$id,
            'cost' => 'required|numeric|min:0',
            'profit_percentage' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($validated);

        return response()->json(['success' => true, 'product' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['success' => true]);
    }

    public function findByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();
        return response()->json($product);
    }

    public function addInventory(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|exists:products,barcode',
            'quantity' => 'required|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'profit_percentage' => 'required|numeric|min:0',
            'new_price' => 'required|numeric|min:0',
        ]);

        $product = Product::where('barcode', $validated['barcode'])->firstOrFail();
        $product->stock += $validated['quantity'];
        $product->cost = $validated['cost'];
        $product->profit_percentage = $validated['profit_percentage'];
        $product->price = $validated['new_price'];
        $product->save();

        return response()->json(['success' => true, 'product' => $product]);
    }
}