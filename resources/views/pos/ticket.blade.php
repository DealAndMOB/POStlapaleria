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
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.2;
            width: 100%;
            max-width: 100%;
            display: flex;
            justify-content: center;
        }
        
        .ticket {
            width: 100%;
            max-width: 250px; /* Ancho máximo para mantener proporción */
            padding: 5px;
            margin: 0 auto;
        }
        
        .header {
            width: 100%;
            text-align: center;
            margin-bottom: 8px;
        }
        
        .logo-container {
            width: 100%;
            text-align: center;
            margin-bottom: 5px;
        }
        
        .logo-container img {
            max-width: 160px; /* Ajusta este valor según el tamaño que necesites */
            height: auto;
            margin: 0 auto;
            display: block;
        }
        
        .company-info {
            font-size: 11px;
            line-height: 1.2;
            margin-top: 5px;
        }
        
        .info {
            margin-bottom: 8px;
            width: 100%;
        }
        
        .info div {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .divider {
            border-top: 1px dotted #000;
            margin: 5px 0;
            width: 100%;
        }
        
        .items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Importante para control de ancho de columnas */
        }
        
        .items thead th {
            border-bottom: 1px solid #000;
            padding: 3px 2px;
            font-size: 11px;
            text-align: center;
        }
        
        .items tbody td {
            padding: 3px 2px;
            vertical-align: top;
        }
        
        /* Ajuste de anchos de columna */
        .quantity {
            width: 15%;
            text-align: center;
        }
        
        .product-name {
            width: 45%;
            word-wrap: break-word;
            text-align: left;
        }
        
        .price {
            width: 20%;
            text-align: right;
        }
        
        .total {
            width: 20%;
            text-align: right;
        }
        
        .total-section {
            width: 100%;
            margin-top: 8px;
        }
        
        .total-section div {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .total-line {
            font-weight: bold;
            font-size: 13px;
            margin-top: 3px;
        }
        
        .footer {
            width: 100%;
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
        }
        
        /* Manejo de desbordamiento para productos largos */
        .product-cell {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Asegura que los números no se desborden */
        .number {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <div class="logo-container">
                <img src="{{ asset('images/logo-tienda.jpeg') }}" alt="Logo de la empresa">
            </div>
            <div class="company-info">
                <div>Dirección de tu empresa</div>
                <div>Tel: (123) 456-7890</div>
            </div>
        </div>

        <div class="info">
            <div>
                <span><strong>Ticket:</strong></span>
                <span>{{ str_pad($sale->id, 8, "0", STR_PAD_LEFT) }}</span>
            </div>
            <div>
                <span><strong>Fecha:</strong></span>
                <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <table class="items">
            <thead>
                <tr>
                    <th class="quantity">CANTIDAD</th>
                    <th class="product-name">DESCRIPCIÓN</th>
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
            <div>¡Gracias por su compra!</div>
            <div>Vuelva pronto</div>
        </div>
    </div>
</body>
</html>