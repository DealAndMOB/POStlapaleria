@extends('layouts.app')

@push('styles')
<style>
    /* Contenedor principal */
    .pos-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem;
        animation: fadeIn 0.3s ease;
    }

    /* Encabezado */
    .pos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pos-title {
        font-size: 1.875rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }

    .pos-title i {
        color: var(--primary-color);
        font-size: 2rem;
    }

    /* Botones principales */
    .btn-main {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-main.report {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .btn-main.report:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-main.process {
        background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 100%);
        color: white;
        width: 100%;
        justify-content: center;
        margin-top: 1rem;
    }

    .btn-main.process:hover {
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .btn-main.process:disabled {
        background: #e5e7eb;
        transform: none;
        box-shadow: none;
        cursor: not-allowed;
    }

    .btn-main i {
        font-size: 1.25rem;
        transition: transform 0.3s ease;
    }

    .btn-main:hover i {
        transform: translateY(-1px);
    }

    /* Tarjetas principales */
    .card {
        background: var(--surface-color);
        border-radius: var(--border-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: none;
        margin-bottom: 1.5rem;
    }

    .card-header {
        background: linear-gradient(145deg, var(--primary-color), #6366f1);
        padding: 1.25rem 1.5rem;
        border: none;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
    }

    .card-header h3 {
        color: white;
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-header h3 i {
        font-size: 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Búsqueda de productos */
    .search-container {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 2px solid rgba(0,0,0,0.05);
        border-radius: 0.75rem;
        transition: all 0.2s ease;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23666666'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.75rem center;
        background-size: 1rem;
    }

    .search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    /* Resultados de búsqueda */
    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    }

    .search-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .search-item:hover {
        background: rgba(99, 102, 241, 0.05);
    }

    .search-item-icon {
        width: 40px;
        height: 40px;
        background: rgba(99, 102, 241, 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
    }

    .search-item-details {
        flex: 1;
    }

    .search-item-name {
        font-weight: 500;
        color: var(--text-primary);
    }

    .search-item-price {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    /* Tabla del carrito */
    .cart-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }

    .cart-table th {
        background: transparent;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 0.75rem 1rem;
        border: none;
    }

    .cart-table tbody tr {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .cart-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .cart-table td {
        padding: 1rem;
        border: none;
        background: transparent;
        vertical-align: middle;
    }

    /* Controles de cantidad */
    .quantity-input {
        width: 80px;
        padding: 0.5rem;
        border: 2px solid rgba(0,0,0,0.05);
        border-radius: 0.5rem;
        text-align: center;
    }

    .quantity-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    /* Botones de acción */
    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-action.remove {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .btn-action.remove:hover {
        background: var(--danger-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }

    /* Resumen de venta */
    .sale-summary {
        background: linear-gradient(145deg, #f8fafc, #f1f5f9);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .summary-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .summary-label {
        font-weight: 500;
        color: var(--text-secondary);
    }

    .summary-value {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 1.25rem;
    }

    /* Modal */
    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .modal-header {
        background: linear-gradient(145deg, var(--primary-color), #6366f1);
        border: none;
        color: white;
        border-radius: 1rem 1rem 0 0;
    }

    .modal-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: white;
        font-weight: 600;
    }

    .btn-close {
        color: white;
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        background: rgba(0,0,0,0.02);
        border-top: 1px solid rgba(0,0,0,0.05);
    }

    /* Formularios */
    .form-label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-control {
        border: 2px solid rgba(0,0,0,0.05);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    /* Animaciones */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .pos-header {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-main {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="pos-container">
    <div class="pos-header">
        <h1 class="pos-title">
            <i class="material-icons-round">point_of_sale</i>
            Punto de Venta
        </h1>
        <a href="{{ route('sales.report') }}" class="btn-main report">
            <i class="material-icons-round">assessment</i>
            Ver Reporte de Ventas
        </a>
    </div>

    <div class="row">
        <!-- Búsqueda de productos y carrito -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="material-icons-round">shopping_cart</i>
                        Carrito de Compras
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Búsqueda de productos -->
                    <div class="search-container">
                        <input type="text" 
                               class="search-input" 
                               id="search" 
                               placeholder="Buscar producto por nombre o código de barras">
                        <div id="searchResults" class="search-results"></div>
                    </div>

                    <!-- Carrito -->
                    <div class="table-responsive">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="cartItems"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de venta y pago -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="material-icons-round">receipt</i>
                        Resumen de Venta
                    </h3>
                </div>
                <div class="card-body">
                    <div class="sale-summary">
                        <div class="summary-item">
                            <span class="summary-label">Total</span>
                            <span class="summary-value">$<span id="total">0.00</span></span>
                        </div>
                        <div class="summary-item">
                            <label for="amount_paid" class="summary-label">Monto Pagado</label>
                            <input type="number" 
                                   id="amount_paid" 
                                   class="form-control" 
                                   min="0" 
                                   step="0.01" 
                                   style="max-width: 150px;">
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Cambio</span>
                            <span class="summary-value">$<span id="change">0.00</span></span>
                        </div>
                    </div>

                    <button id="processSale" class="btn-main process" disabled>
                        <i class="material-icons-round">payment</i>
                        Procesar Venta
                    </button>

                    <button id="showExternalProductModal" class="btn-main process" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                        <i class="material-icons-round">add_shopping_cart</i>
                        Agregar Producto Externo
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- Modal para agregar producto externo -->
<div class="modal fade" id="externalProductModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="externalProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="externalProductModalLabel">
                    <i class="material-icons-round">add_shopping_cart</i>
                    Agregar Producto Externo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="external_product_name" class="form-label">
                        <i class="material-icons-round">label</i>
                        Nombre del Producto
                    </label>
                    <input type="text" id="external_product_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="external_product_price" class="form-label">
                        <i class="material-icons-round">payments</i>
                        Precio
                    </label>
                    <input type="number" id="external_product_price" class="form-control" min="0" step="0.01" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="material-icons-round">close</i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="addExternalProduct">
                    <i class="material-icons-round">add_shopping_cart</i>
                    Agregar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM y configuración inicial
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const search = document.getElementById('search');
    const searchResults = document.getElementById('searchResults');
    const cartItems = document.getElementById('cartItems');
    const totalElement = document.getElementById('total');
    const amountPaidInput = document.getElementById('amount_paid');
    const changeElement = document.getElementById('change');
    const processSaleButton = document.getElementById('processSale');
    
    // Inicialización del modal
    const externalProductModal = new bootstrap.Modal(document.getElementById('externalProductModal'), {
        keyboard: true,
        backdrop: true,
        focus: true
    });

    let cart = [];

    // Función de búsqueda de productos
    function searchProducts() {
        if (search.value.length > 2) {
            fetch(`/pos/search?term=${encodeURIComponent(search.value)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (Array.isArray(data) && data.length > 0) {
                        searchResults.innerHTML = data.map(product => `
                            <div class="search-item" role="button" tabindex="0" data-product='${JSON.stringify(product)}'>
                                <div class="search-item-icon">
                                    <i class="material-icons-round">inventory_2</i>
                                </div>
                                <div class="search-item-details">
                                    <div class="search-item-name">${product.name}</div>
                                    <div class="search-item-price">
                                        $${parseFloat(product.price).toFixed(2)} - Stock: ${product.stock}
                                        ${product.stock < 10 ? '<span class="stock-badge stock-low">Stock Bajo</span>' : ''}
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        searchResults.querySelectorAll('.search-item').forEach(item => {
                            item.addEventListener('click', () => {
                                const product = JSON.parse(item.dataset.product);
                                addToCart(product);
                            });
                            item.addEventListener('keypress', (e) => {
                                if (e.key === 'Enter' || e.key === ' ') {
                                    const product = JSON.parse(item.dataset.product);
                                    addToCart(product);
                                }
                            });
                        });
                    } else {
                        searchResults.innerHTML = '<div class="p-3">No se encontraron productos</div>';
                    }
                })
                .catch(error => {
                    console.error('Error searching products:', error);
                    searchResults.innerHTML = '<div class="p-3 text-danger">Error al buscar productos</div>';
                    showAlert('Error al buscar productos', 'error');
                });
        } else {
            searchResults.innerHTML = '';
        }
    }

    // Función para agregar al carrito
    function addToCart(product) {
        if (!product || !product.id) {
            console.error('Producto inválido:', product);
            showAlert('Error al agregar el producto', 'error');
            return;
        }

        const existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            if (existingItem.quantity < product.stock) {
                existingItem.quantity++;
                showAlert(`Se agregó una unidad de ${product.name}`, 'success');
            } else {
                showAlert('No hay suficiente stock', 'error');
                return;
            }
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                stock: parseInt(product.stock),
                quantity: 1,
                is_external: product.is_external || false
            });
            showAlert(`${product.name} agregado al carrito`, 'success');
        }

        search.value = '';
        searchResults.innerHTML = '';
        updateCartDisplay();
    }

    // Función para actualizar la visualización del carrito
    function updateCartDisplay() {
        if (!Array.isArray(cart)) {
            console.error('El carrito no es un array:', cart);
            cart = [];
        }

        cartItems.innerHTML = cart.map((item, index) => `
            <tr>
                <td>
                    <div class="search-item">
                        <div class="search-item-icon">
                            <i class="material-icons-round">inventory_2</i>
                        </div>
                        <div class="search-item-details">
                            <div class="search-item-name">${item.name}</div>
                            ${!item.is_external ? 
                                `<div class="search-item-price">Stock disponible: ${item.stock}</div>` : 
                                '<div class="search-item-price"><em>Producto externo</em></div>'
                            }
                        </div>
                    </div>
                </td>
                <td>
                    <input type="number" 
                           class="quantity-input"
                           value="${item.quantity}" 
                           min="1" 
                           max="${item.is_external ? 999 : item.stock}"
                           onchange="updateCartItemQuantity(${index}, this.value)">
                </td>
                <td>$${parseFloat(item.price).toFixed(2)}</td>
                <td>$${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</td>
                <td>
                    <button onclick="removeFromCart(${index})" 
                            class="btn-action remove" 
                            title="Eliminar producto">
                        <i class="material-icons-round">delete</i>
                    </button>
                </td>
            </tr>
        `).join('');

        updateTotal();
        saveCurrentSale();
    }

    // Función para actualizar la cantidad de un item en el carrito
    function updateCartItemQuantity(index, quantity) {
        const item = cart[index];
        const newQuantity = parseInt(quantity);
        
        if (isNaN(newQuantity) || newQuantity < 1) {
            showAlert('La cantidad debe ser mayor a 0', 'error');
            updateCartDisplay();
            return;
        }

        if (!item.is_external && newQuantity > item.stock) {
            showAlert(`Solo hay ${item.stock} unidades disponibles de ${item.name}`, 'error');
            item.quantity = item.stock;
        } else {
            item.quantity = newQuantity;
            showAlert(`Cantidad actualizada`, 'success');
        }
        
        updateCartDisplay();
    }

    // Función para actualizar el total
    function updateTotal() {
        try {
            const total = cart.reduce((sum, item) => {
                return sum + (parseFloat(item.price) * parseInt(item.quantity));
            }, 0);
            
            totalElement.textContent = total.toFixed(2);
            updateChange();
            
            processSaleButton.disabled = cart.length === 0 || 
                                       parseFloat(amountPaidInput.value || 0) < total;
        } catch (error) {
            console.error('Error al calcular el total:', error);
            totalElement.textContent = '0.00';
            processSaleButton.disabled = true;
        }
    }

    // Función para eliminar del carrito
    function removeFromCart(index) {
        const item = cart[index];
        Swal.fire({
            title: '¿Eliminar producto?',
            text: `¿Deseas eliminar ${item.name} del carrito?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                cart.splice(index, 1);
                updateCartDisplay();
                showAlert('Producto eliminado del carrito', 'success');
            }
        });
    }

    // Función para actualizar el cambio
    function updateChange() {
        const total = parseFloat(totalElement.textContent);
        const amountPaid = parseFloat(amountPaidInput.value) || 0;
        const change = amountPaid - total;
        changeElement.textContent = change > 0 ? change.toFixed(2) : '0.00';
        
        processSaleButton.disabled = cart.length === 0 || amountPaid < total;
    }

    // Función para mostrar el modal de producto externo
    function showExternalProductModal() {
        // Limpiar campos del modal
        document.getElementById('external_product_name').value = '';
        document.getElementById('external_product_price').value = '';
        
        // Mostrar el modal
        externalProductModal.show();
    }

    // Función para agregar producto externo
    function addExternalProduct() {
        const nameInput = document.getElementById('external_product_name');
        const priceInput = document.getElementById('external_product_price');
        const name = nameInput.value.trim();
        const price = parseFloat(priceInput.value);

        if (!name || !price || price <= 0) {
            showAlert('Por favor, ingrese un nombre y un precio válido', 'error');
            return;
        }

        const externalProduct = {
            id: 'ext_' + Date.now(),
            name: name,
            price: price,
            is_external: true,
            stock: 999,
            quantity: 1
        };

        cart.push(externalProduct);
        
        // Ocultar el modal
        externalProductModal.hide();
        
        // Limpiar campos
        nameInput.value = '';
        priceInput.value = '';
        
        updateCartDisplay();
        showAlert('Producto externo agregado al carrito', 'success');
    }

    // Función para procesar la venta
    function processSale() {
        const total = parseFloat(totalElement.textContent);
        const amountPaid = parseFloat(amountPaidInput.value);

        if (amountPaid < total) {
            showAlert('El monto pagado es insuficiente', 'error');
            return;
        }

        Swal.fire({
            title: '¿Procesar venta?',
            text: `Total a cobrar: $${total.toFixed(2)}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Sí, procesar venta',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                processingSale();
            }
        });
    }

    // Función para procesar la venta (backend)
    function processingSale() {
        const loadingSwal = Swal.fire({
            title: 'Procesando venta',
            text: 'Por favor espere...',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        const items = cart.map(item => ({
            id: item.id,
            quantity: item.quantity,
            price: parseFloat(item.price),
            name: item.name,
            is_external: item.is_external || false
        }));

        fetch('/pos/process-sale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                items: items,
                total: parseFloat(totalElement.textContent),
                amount_paid: parseFloat(amountPaidInput.value)
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            loadingSwal.close();
            Swal.fire({
                title: '¡Venta exitosa!',
                text: `Venta procesada correctamente. Cambio: $${parseFloat(changeElement.textContent).toFixed(2)}`,
                icon: 'success',
                confirmButtonColor: '#10B981'
            }).then(() => {
                printTicket(data.sale.id);
                cart = [];
                updateCartDisplay();
                amountPaidInput.value = '';
                search.value = '';
                searchResults.innerHTML = '';
                sessionStorage.removeItem('currentSale');
            });
        })
        .catch(error => {
            loadingSwal.close();
            console.error('Error processing sale:', error);
            showAlert(error.message || 'Error al procesar la venta', 'error');
        });
    }

    // Función para imprimir ticket
    function printTicket(saleId) {
        fetch(`/pos/print-ticket/${saleId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Ticket impreso correctamente', 'success');
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error printing ticket:', error);
            showAlert('Error al imprimir el ticket', 'error');
        });
    }

    // Función para mostrar alertas
    function showAlert(message, type) {
        Swal.fire({
            title: message,
            icon: type,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            iconColor: type === 'success' ? '#10B981' : '#EF4444',
            customClass: {
                popup: 'colored-toast'
            }
        });
    }

    // Función para guardar venta actual
    function saveCurrentSale() {
        if (cart.length > 0) {
            sessionStorage.setItem('currentSale', JSON.stringify(cart));
        } else {
            sessionStorage.removeItem('currentSale');
        }
    }

    // Función debounce para la búsqueda
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Event Listeners
    search.addEventListener('input', debounce(searchProducts, 300));
    amountPaidInput.addEventListener('input', updateChange);
 // Event Listeners (continuación)
 processSaleButton.addEventListener('click', processSale);
    document.getElementById('showExternalProductModal').addEventListener('click', showExternalProductModal);
    document.getElementById('addExternalProduct').addEventListener('click', addExternalProduct);

    // Cerrar resultados de búsqueda al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!search.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.innerHTML = '';
        }
    });

    // Manejar tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchResults.innerHTML = '';
            externalProductModal.hide();
        }
    });

    // Recuperar venta guardada
    if (sessionStorage.getItem('currentSale')) {
        try {
            const savedSale = JSON.parse(sessionStorage.getItem('currentSale'));
            if (savedSale && Array.isArray(savedSale)) {
                cart = savedSale;
                updateCartDisplay();
                showAlert('Se ha restaurado una venta en progreso', 'info');
            }
        } catch (e) {
            console.error('Error al restaurar venta:', e);
            sessionStorage.removeItem('currentSale');
        }
    }

    // Exponer funciones necesarias globalmente
    window.addExternalProduct = addExternalProduct;
    window.showExternalProductModal = showExternalProductModal;
    window.updateCartItemQuantity = updateCartItemQuantity;
    window.removeFromCart = removeFromCart;

    // Estilos adicionales para las alertas y elementos UI
    document.head.insertAdjacentHTML('beforeend', `
        <style>
            .colored-toast.swal2-icon-success {
                background: #10B981 !important;
                color: white !important;
            }
            .colored-toast.swal2-icon-error {
                background: #EF4444 !important;
                color: white !important;
            }
            .colored-toast.swal2-icon-info {
                background: #3B82F6 !important;
                color: white !important;
            }
            .stock-badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                border-radius: 1rem;
                margin-left: 0.5rem;
            }
            .stock-low {
                background: rgba(239, 68, 68, 0.1);
                color: #EF4444;
            }
            .modal-backdrop {
                opacity: 0.5;
            }
            .search-results {
                z-index: 1050;
            }
            .quantity-input::-webkit-inner-spin-button,
            .quantity-input::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            .quantity-input {
                -moz-appearance: textfield;
            }
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .search-item {
                animation: fadeIn 0.2s ease-out;
            }
            .cart-table tr {
                transition: all 0.2s ease;
            }
            .cart-table tr:hover {
                background-color: rgba(99, 102, 241, 0.05);
            }
            .btn-action {
                transition: all 0.2s ease;
            }
            .btn-action:hover {
                transform: translateY(-2px);
            }
            .search-input:focus {
                box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
            }
            #externalProductModal .modal-content {
                border: none;
                border-radius: 1rem;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            #externalProductModal .modal-header {
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                padding: 1.5rem;
            }
            #externalProductModal .modal-body {
                padding: 1.5rem;
            }
            #externalProductModal .modal-footer {
                border-top: 1px solid rgba(0, 0, 0, 0.1);
                padding: 1.5rem;
            }
            .form-control:focus {
                border-color: #6366F1;
                box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
            }
        </style>
    `);
});
</script>
@endpush