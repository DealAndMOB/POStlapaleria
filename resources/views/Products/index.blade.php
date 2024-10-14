@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Productos</h1>
    <button id="createProductBtn" class="btn btn-primary mb-3 me-2">
        <i class="fas fa-plus"></i> Crear Nuevo Producto
    </button>
    <button id="addInventoryBtn" class="btn btn-success mb-3">
        <i class="fas fa-boxes"></i> Agregar Inventario
    </button>
    
    <table id="productsTable" class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Código de Barras</th>
                <th>Costo</th>
                <th>% Ganancia</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal para Crear/Editar Producto -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Crear Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <input type="hidden" id="productId">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" id="productName" required>
                    </div>
                    <div class="mb-3">
                        <label for="productBarcode" class="form-label">Código de Barras</label>
                        <input type="text" class="form-control" id="productBarcode" required>
                    </div>
                    <div class="mb-3">
                        <label for="productCost" class="form-label">Costo</label>
                        <input type="number" class="form-control" id="productCost" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="productProfit" class="form-label">Porcentaje de Ganancia</label>
                        <input type="number" class="form-control" id="productProfit" required>
                    </div>
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Precio de Venta</label>
                        <input type="number" class="form-control" id="productPrice" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="productStock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="productStock" required>
                    </div>
                    <div class="mb-3">
                        <label for="productCategory" class="form-label">Categoría</label>
                        <select class="form-control" id="productCategory" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveProductBtn">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Inventario -->
<div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="inventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventoryModalLabel">Agregar Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="inventoryForm">
                    <div class="mb-3">
                        <label for="inventoryBarcode" class="form-label">Código de Barras</label>
                        <input type="text" class="form-control" id="inventoryBarcode" required>
                    </div>
                    <div id="productInfo" style="display: none;">
                        <p><strong>Nombre:</strong> <span id="inventoryProductName"></span></p>
                        <p><strong>Stock Actual:</strong> <span id="inventoryCurrentStock"></span></p>
                    </div>
                    <div class="mb-3">
                        <label for="inventoryQuantity" class="form-label">Cantidad a Agregar</label>
                        <input type="number" class="form-control" id="inventoryQuantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="inventoryCost" class="form-label">Nuevo Costo por Unidad</label>
                        <input type="number" class="form-control" id="inventoryCost" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="inventoryProfit" class="form-label">Nuevo Porcentaje de Ganancia</label>
                        <input type="number" class="form-control" id="inventoryProfit" required>
                    </div>
                    <div class="mb-3">
                        <label for="inventoryNewPrice" class="form-label">Nuevo Precio de Venta</label>
                        <input type="number" class="form-control" id="inventoryNewPrice" step="0.01" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveInventoryBtn">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#productsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.index') }}",
        columns: [
            {data: 'name', name: 'name'},
            {data: 'barcode', name: 'barcode'},
            {data: 'cost', name: 'cost'},
            {data: 'profit_percentage', name: 'profit_percentage'},
            {data: 'price', name: 'price'},
            {data: 'stock', name: 'stock'},
            {data: 'category.name', name: 'category.name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        }
    });

    $('#createProductBtn').click(function() {
        $('#productModalLabel').text('Crear Producto');
        $('#productId').val('');
        $('#productForm')[0].reset();
        var modal = new bootstrap.Modal(document.getElementById('productModal'));
        modal.show();
    });

    $('#addInventoryBtn').click(function() {
        $('#inventoryForm')[0].reset();
        $('#productInfo').hide();
        var modal = new bootstrap.Modal(document.getElementById('inventoryModal'));
        modal.show();
    });

    $('#productCost, #productProfit').on('input', function() {
        calculateSalePrice('product');
    });

    $('#inventoryCost, #inventoryProfit').on('input', function() {
        calculateSalePrice('inventory');
    });

    function calculateSalePrice(prefix) {
        var cost = parseFloat($('#' + prefix + 'Cost').val()) || 0;
        var profit = parseFloat($('#' + prefix + 'Profit').val()) || 0;
        var salePrice = cost * (1 + profit / 100);
        $('#' + prefix + (prefix === 'product' ? 'Price' : 'NewPrice')).val(salePrice.toFixed(2));
    }

    $('#inventoryBarcode').on('blur', function() {
        var barcode = $(this).val();
        if (barcode) {
            $.get("{{ url('products/find-by-barcode') }}/" + barcode, function(data) {
                if (data) {
                    $('#inventoryProductName').text(data.name);
                    $('#inventoryCurrentStock').text(data.stock);
                    $('#inventoryCost').val(data.cost);
                    $('#inventoryProfit').val(data.profit_percentage);
                    $('#inventoryNewPrice').val(data.price);
                    $('#productInfo').show();
                } else {
                    Swal.fire('Error', 'Producto no encontrado', 'error');
                    $('#productInfo').hide();
                }
            });
        }
    });

    $('#saveProductBtn').click(function() {
        var id = $('#productId').val();
        var data = {
            name: $('#productName').val(),
            barcode: $('#productBarcode').val(),
            cost: $('#productCost').val(),
            profit_percentage: $('#productProfit').val(),
            price: $('#productPrice').val(),
            stock: $('#productStock').val(),
            category_id: $('#productCategory').val()
        };
        var url = id ? "{{ url('products') }}/" + id : "{{ route('products.store') }}";
        var method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                var modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
                modal.hide();
                Swal.fire('¡Éxito!', id ? 'Producto actualizado.' : 'Producto creado.', 'success');
                table.ajax.reload();
            },
            error: function(xhr) {
                Swal.fire('Error', 'Hubo un problema al guardar el producto.', 'error');
            }
        });
    });

    $('#saveInventoryBtn').click(function() {
        var data = {
            barcode: $('#inventoryBarcode').val(),
            quantity: $('#inventoryQuantity').val(),
            cost: $('#inventoryCost').val(),
            profit_percentage: $('#inventoryProfit').val(),
            new_price: $('#inventoryNewPrice').val()
        };

        $.ajax({
            url: "{{ route('products.add-inventory') }}",
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                var modal = bootstrap.Modal.getInstance(document.getElementById('inventoryModal'));
                modal.hide();
                Swal.fire('¡Éxito!', 'Inventario actualizado.', 'success');
                table.ajax.reload();
            },
            error: function(xhr) {
                Swal.fire('Error', 'Hubo un problema al actualizar el inventario.', 'error');
            }
        });
    });

    window.editProduct = function(id) {
        $.get("{{ url('products') }}/" + id + "/edit", function(data) {
            $('#productModalLabel').text('Editar Producto');
            $('#productId').val(data.id);
            $('#productName').val(data.name);
            $('#productBarcode').val(data.barcode);
            $('#productCost').val(data.cost);
            $('#productProfit').val(data.profit_percentage);
            $('#productPrice').val(data.price);
            $('#productStock').val(data.stock);
            $('#productCategory').val(data.category_id);
            var modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();
        });
    }

    window.deleteProduct = function(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, bórralo!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('products') }}/" + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('¡Eliminado!', 'El producto ha sido eliminado.', 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Hubo un problema al eliminar el producto.', 'error');
                    }
                });
            }
        });
    }
});
</script>
@endpush