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

    public function printTicket($saleId)
    {
        try {
            $sale = Sale::with(['items.product'])->findOrFail($saleId);
            
            // Configurar el conector de la impresora
            // Cambia "POS-58" por el nombre de tu impresora
            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);

            // Iniciar la impresión
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            
            // Encabezado del ticket
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("TU EMPRESA\n");
            $printer->selectPrintMode();
            $printer->text("Dirección de tu empresa\n");
            $printer->text("Tel: (123) 456-7890\n");
            $printer->feed();

            // Información de la venta
            $printer->text("Ticket #: " . str_pad($sale->id, 8, "0", STR_PAD_LEFT) . "\n");
            $printer->text("Fecha: " . $sale->created_at->format('d/m/Y H:i:s') . "\n");
            $printer->feed();

            // Encabezado de productos
            $printer->text("--------------------------------\n");
            $printer->text("CANT  PRODUCTO    PRECIO   TOTAL\n");
            $printer->text("--------------------------------\n");

            // Productos
            foreach ($sale->items as $item) {
                $name = $item->product ? $item->product->name : $item->external_product_name;
                // Limitar el nombre del producto a 10 caracteres
                $name = substr($name, 0, 10);
                
                $qty = str_pad($item->quantity, 3, " ", STR_PAD_LEFT);
                $price = str_pad('$' . number_format($item->price, 2), 8, " ", STR_PAD_LEFT);
                $total = str_pad('$' . number_format($item->quantity * $item->price, 2), 8, " ", STR_PAD_LEFT);
                
                $printer->text("$qty $name$price$total\n");
            }

            $printer->text("--------------------------------\n");

            // Totales
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("SUBTOTAL: $" . number_format($sale->total, 2) . "\n");
            $printer->text("TOTAL:    $" . number_format($sale->total, 2) . "\n");
            $printer->text("PAGADO:   $" . number_format($sale->amount_paid, 2) . "\n");
            $printer->text("CAMBIO:   $" . number_format($sale->change, 2) . "\n");

            // Pie de página
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->feed(2);
            $printer->text("¡Gracias por su compra!\n");
            $printer->text("Vuelva pronto\n");
            
            // Código QR o Barcode si lo necesitas
            // $printer->qrCode("https://tutienda.com/ticket/" . $sale->id);
            
            $printer->feed(3);
            $printer->cut();
            $printer->pulse();

            $printer->close();

            return response()->json(['success' => true, 'message' => 'Ticket impreso correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al imprimir: ' . $e->getMessage()], 500);
        }
    }
}