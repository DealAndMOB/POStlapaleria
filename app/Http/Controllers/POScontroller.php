<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

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
                        'external_product_name' => $item['name'], // Asegúrate de que este campo se está guardando
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

    public function printTicket($saleId)
    {
        try {
            $sale = Sale::with(['items.product'])->findOrFail($saleId);
            
            $pdf = PDF::loadView('pos.ticket', compact('sale'));
            
            // Configurar el PDF para impresora térmica de 58mm (164.409pt)
            $customPaper = array(0, 0, 164.409, 400);
            
            $pdf->setPaper($customPaper);
            
            $pdf->setOptions([
                'margin-top'    => 0,
                'margin-right'  => 0,
                'margin-bottom' => 0,
                'margin-left'   => 0,
                'dpi'          => 130,
                'default-font-size' => 11,
                'enable-smart-shrinking' => true,
                'no-outline'   => true,
                'page-size'    => 'custom',
                'zoom'         => 1,
                'title' => false,
                'enable-local-file-access' => true,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'isHtml5ParserEnabled' => true
            ]);
            
            return $pdf->stream("ticket-{$saleId}.pdf");
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error al generar el ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    // Método adicional para imprimir directamente en la impresora térmica (opcional)
    public function printToThermal($saleId)
    {
        try {
            $sale = Sale::with(['items.product'])->findOrFail($saleId);
            
            // Primero generamos el PDF
            $pdf = PDF::loadView('pos.ticket', compact('sale'));
            $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait');
            
            // Guardamos el PDF temporalmente
            $tempPath = storage_path('app/public/temp/');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            $pdfPath = $tempPath . "ticket-{$saleId}.pdf";
            $pdf->save($pdfPath);
            
            // Imprimir usando el comando del sistema
            if (PHP_OS === 'WINNT') {
                // Para Windows
                $printerName = 'POS-58'; // Nombre de tu impresora térmica
                exec('SumatraPDF.exe -print-to "' . $printerName . '" "' . $pdfPath . '"');
            } else {
                // Para Linux/Unix
                exec('lpr -P POS-58 ' . $pdfPath);
            }
            
            // Eliminar el archivo temporal
            unlink($pdfPath);
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket impreso correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al imprimir el ticket: ' . $e->getMessage()
            ], 500);
        }
    }

     // Método para obtener el historial de ventas
     public function getSalesHistory(Request $request)
     {
         $sales = Sale::with(['items.product'])
             ->orderBy('created_at', 'desc')
             ->paginate(10);
 
         return view('pos.sales-history', compact('sales'));
     }
 
     // Método para reimprimir un ticket
     public function reprintTicket($saleId)
     {
         return $this->printTicket($saleId);
     }
}