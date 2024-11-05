<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket de Venta #{{ str_pad($sale->id, 8, "0", STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 8px;
            line-height: 1.2;
            width: 100%;
            display: flex;
            justify-content: center;
            background-color: white;
        }
        
        .ticket {
            width: 50mm;
            max-width: 50mm;
            background: white;
            padding: 2mm;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        
        .logo-container {
            margin-bottom: 4px;
        }
        
        .logo-container img {
            max-width: 40mm;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        .company-info {
            font-size: 8px;
            line-height: 1.3;
            margin-top: 4px;
        }
        
        .company-info div {
            margin: 1px 0;
        }
        
        .info {
            margin: 5px 0;
        }
        
        .info div {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 8px;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
        
        /* Tabla de productos */
        .items {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
            table-layout: fixed;
        }
        
        .items thead th {
            padding: 2px 1px;
            font-size: 8px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .items tbody td {
            padding: 2px 1px;
            font-size: 8px;
            vertical-align: top;
        }
        
        /* Columnas de la tabla */
        .quantity {
            width: 15%;
            text-align: center;
        }
        
        .product-name {
            width: 40%;
            text-align: left;
        }
        
        .price {
            width: 22%;
            text-align: right;
        }
        
        .total {
            width: 23%;
            text-align: right;
        }
        
        /* Contenedor de productos */
        .product-cell {
            word-break: break-word;
            overflow-wrap: break-word;
            line-height: 1.2;
        }
        
        /* Sección de totales */
        .total-section {
            margin-top: 5px;
        }
        
        .total-section div {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 8px;
        }
        
        .total-line {
            font-weight: bold;
            font-size: 9px;
            margin: 3px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 8px;
            font-size: 8px;
            line-height: 1.3;
        }
        
        /* Mejoras para impresión */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            
            .ticket {
                padding: 2mm;
            }
            
            .product-cell {
                max-width: 20mm;
            }
        }
        
        /* Utilidades */
        .number {
            font-family: 'Courier New', monospace;
            white-space: nowrap;
        }
        
        strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <div class="logo-container">
                <img src="{{ asset('images/logo-tienda.jpeg') }}" alt="Logo">
            </div>
            <div class="company-info">
                <div><strong>RFC:</strong> CUMR770117N18</div>
                <div>Av 16 de septiembre Mz. 39. Lt. 24.</div>
                <div>San Pedro Atzompa</div>
                <div><strong>Tel:</strong> 55-14-36-34-81</div>
                <div><strong>Tel:</strong> 55-59-38-75-20</div>
            </div>
        </div>

        <div class="divider"></div>

        <div class="info">
            <div>
                <span><strong>Ticket:</strong></span>
                <span class="number">{{ str_pad($sale->id, 9, "0", STR_PAD_LEFT) }}</span>
            </div>
            <div>
                <span><strong>Fecha:</strong></span>
                <span class="number">{{ $sale->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <table class="items">
            <thead>
                <tr>
                    <th class="quantity">CANT</th>
                    <th class="product-name">DESC</th>
                    <th class="price">PRECIO</th>
                    <th class="total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td class="quantity number">{{ $item->quantity }}</td>
                    <td class="product-name">
                        <div class="product-cell">
                            {{ $item->product_name }}
                        </div>
                    </td>
                    <td class="price number">${{ number_format($item->price, 2) }}</td>
                    <td class="total number">${{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="total-section">
            <div>
                <span><strong>SUBTOTAL:</strong></span>
                <span class="number">${{ number_format($sale->total, 2) }}</span>
            </div>
            <div class="total-line">
                <span><strong>TOTAL:</strong></span>
                <span class="number">${{ number_format($sale->total, 2) }}</span>
            </div>
            <div>
                <span><strong>PAGADO:</strong></span>
                <span class="number">${{ number_format($sale->amount_paid, 2) }}</span>
            </div>
            <div>
                <span><strong>CAMBIO:</strong></span>
                <span class="number">${{ number_format($sale->change, 2) }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <div><strong>TODA ENTREGA</strong></div>
            <div><strong>SE REALIZA A PIE DE CAMIÓN</strong></div>
            <div style="margin-top: 4px;">¡Gracias por su compra!</div>
        </div>
    </div>
</body>
</html>