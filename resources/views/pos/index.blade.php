@extends('layouts.app')

@section('content')
<div id="pos-app" class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('sales.report') }}" class="btn btn-primary mr-2">Ver Reporte de Ventas</a>
        </div>
    </div>
    <div class="row">
        <!-- Búsqueda de productos y carrito -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Punto de Venta</h3>
                </div>
                <div class="card-body">
                    <!-- Búsqueda de productos -->
                    <div class="form-group">
                        <input type="text" class="form-control" id="search" placeholder="Buscar producto por nombre o código de barras">
                    </div>
                    <!-- Lista de productos encontrados -->
                    <div id="searchResults" class="list-group mt-3"></div>
                    <!-- Carrito -->
                    <table class="table mt-4">
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
        <!-- Resumen de venta y pago -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Resumen de Venta</h3>
                </div>
                <div class="card-body">
                    <h4>Total: $<span id="total">0.00</span></h4>
                    <div class="form-group">
                        <label for="amount_paid">Monto Pagado:</label>
                        <input type="number" id="amount_paid" class="form-control" min="0" step="0.01">
                    </div>
                    <h4>Cambio: $<span id="change">0.00</span></h4>
                    <button id="processSale" class="btn btn-primary btn-block mt-3">Procesar Venta</button>
                    <button id="showExternalProductModal" class="btn btn-secondary btn-block mt-3">Agregar Producto Externo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar producto externo -->
    <div class="modal fade" id="externalProductModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Producto Externo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="external_product_name">Nombre del Producto:</label>
                        <input type="text" id="external_product_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="external_product_price">Precio:</label>
                        <input type="number" id="external_product_price" class="form-control" min="0" step="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="addExternalProduct" class="btn btn-primary">Agregar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    console.log('SweetAlert2 available:', typeof Swal !== 'undefined');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token available:', !!csrfToken);

    const search = document.getElementById('search');
    const searchResults = document.getElementById('searchResults');
    const cartItems = document.getElementById('cartItems');
    const totalElement = document.getElementById('total');
    const amountPaidInput = document.getElementById('amount_paid');
    const changeElement = document.getElementById('change');
    const processSaleButton = document.getElementById('processSale');
    const showExternalProductModalButton = document.getElementById('showExternalProductModal');
    const addExternalProductButton = document.getElementById('addExternalProduct');

    console.log('All elements retrieved:', {
        search, searchResults, cartItems, totalElement, amountPaidInput,
        changeElement, processSaleButton, showExternalProductModalButton, addExternalProductButton
    });

    let cart = [];

    search.addEventListener('input', debounce(searchProducts, 300));

    function searchProducts() {
        console.log('Searching for:', search.value);
        if (search.value.length > 2) {
            fetch(`/pos/search?term=${search.value}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Search results:', data);
                    searchResults.innerHTML = data.map(product => `
                        <a href="#" class="list-group-item list-group-item-action" data-id="${product.id}">
                            ${product.name} - $${product.price} - Stock: ${product.stock}
                        </a>
                    `).join('');

                    searchResults.querySelectorAll('a').forEach(item => {
                        item.addEventListener('click', (e) => {
                            e.preventDefault();
                            addToCart(data.find(p => p.id == item.dataset.id));
                        });
                    });
                })
                .catch(error => {
                    console.error('Error searching products:', error);
                    searchResults.innerHTML = `<p class="text-danger">Error al buscar productos: ${error.message}</p>`;
                });
        } else {
            searchResults.innerHTML = '';
        }
    }

    function addToCart(product) {
        console.log('Adding to cart:', product);
        const existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            if (existingItem.quantity < product.stock) {
                existingItem.quantity++;
            } else {
                showAlert('No hay suficiente stock', 'error');
            }
        } else {
            cart.push({...product, quantity: 1});
        }
        search.value = '';
        searchResults.innerHTML = '';
        updateCartDisplay();
    }

    function updateCartDisplay() {
        console.log('Updating cart display');
        cartItems.innerHTML = cart.map((item, index) => `
            <tr>
                <td>${item.name}</td>
                <td>
                    <input type="number" value="${item.quantity}" min="1" max="${item.is_external ? null : item.stock}" 
                           onchange="updateCartItemQuantity(${index}, this.value)">
                </td>
                <td>$${item.price}</td>
                <td>$${(item.price * item.quantity).toFixed(2)}</td>
                <td>
                    <button onclick="removeFromCart(${index})" class="btn btn-sm btn-danger">Eliminar</button>
                </td>
            </tr>
        `).join('');
        updateTotal();
    }

    function updateCartItemQuantity(index, quantity) {
        console.log('Updating cart item quantity:', index, quantity);
        cart[index].quantity = parseInt(quantity);
        if (!cart[index].is_external && cart[index].quantity > cart[index].stock) {
            cart[index].quantity = cart[index].stock;
            showAlert(`La cantidad de ${cart[index].name} ha sido ajustada al stock disponible`, 'warning');
        }
        updateCartDisplay();
    }

    function removeFromCart(index) {
        console.log('Removing from cart:', index);
        cart.splice(index, 1);
        updateCartDisplay();
    }

    function updateTotal() {
        console.log('Updating total');
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        totalElement.textContent = total.toFixed(2);
        updateChange();
    }

    amountPaidInput.addEventListener('input', updateChange);

    function updateChange() {
        console.log('Updating change');
        const total = parseFloat(totalElement.textContent);
        const amountPaid = parseFloat(amountPaidInput.value) || 0;
        const change = amountPaid - total;
        changeElement.textContent = change > 0 ? change.toFixed(2) : '0.00';
        processSaleButton.disabled = cart.length === 0 || amountPaid < total;
    }

    processSaleButton.addEventListener('click', processSale);

    function processSale() {
        console.log('Processing sale');
        const total = parseFloat(totalElement.textContent);
        const amountPaid = parseFloat(amountPaidInput.value);

        if (amountPaid < total) {
            showAlert('El monto pagado es insuficiente', 'error');
            return;
        }

        const items = cart.map(item => ({
            id: item.id,
            quantity: item.quantity,
            price: item.price,
            name: item.name
        }));

        fetch('/pos/process-sale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                items: items,
                total: total,
                amount_paid: amountPaid
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            console.log('Sale processed:', data);
            showAlert('Venta procesada con éxito', 'success');
            cart = [];
            updateCartDisplay();
            amountPaidInput.value = '';
        })
        .catch(error => {
            console.error('Error processing sale:', error);
            showAlert('Error al procesar la venta: ' + error.message, 'error');
        });
    }

    showExternalProductModalButton.addEventListener('click', () => {
        console.log('Attempting to show modal');
        const modal = new bootstrap.Modal(document.getElementById('externalProductModal'));
        modal.show();
    });

    addExternalProductButton.addEventListener('click', addExternalProduct);

    function addExternalProduct() {
        console.log('Adding external product');
        const name = document.getElementById('external_product_name').value;
        const price = parseFloat(document.getElementById('external_product_price').value);

        if (name && price > 0) {
            fetch('/pos/add-external-product', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ name, price })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('External product added:', data);
                cart.push({...data, quantity: 1});
                const modal = bootstrap.Modal.getInstance(document.getElementById('externalProductModal'));
                modal.hide();
                document.getElementById('external_product_name').value = '';
                document.getElementById('external_product_price').value = '';
                updateCartDisplay();
            })
            .catch(error => {
                console.error('Error adding external product:', error);
                showAlert('Error al agregar producto externo: ' + error.message, 'error');
            });
        } else {
            showAlert('Por favor, ingrese un nombre y un precio válido', 'error');
        }
    }

    function showAlert(message, type) {
        console.log('Showing alert:', message, type);
        Swal.fire({
            title: message,
            icon: type,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Expose functions to global scope for inline event handlers
    window.updateCartItemQuantity = updateCartItemQuantity;
    window.removeFromCart = removeFromCart;
});
</script>
@endpush