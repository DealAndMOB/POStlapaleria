@extends('layouts.app')

@push('styles')
<style>
    /* Contenedor principal */
    .products-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        animation: fadeIn 0.3s ease;
    }

    /* Título y botones superiores */
    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .products-title {
        font-size: 1.875rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }

    .products-title i {
        color: var(--primary-color);
        font-size: 2rem;
    }

    .header-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
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

    .btn-main.create {
        background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 100%);
        color: white;
    }

    .btn-main.create:hover {
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .btn-main.inventory {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
    }

    .btn-main.inventory:hover {
        background: linear-gradient(135deg, #047857 0%, #059669 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-main i {
        font-size: 1.25rem;
        transition: transform 0.3s ease;
    }

    .btn-main:hover i {
        transform: translateY(-1px);
    }

    /* Contenedor de la tabla */
    .products-table-container {
        background: var(--surface-color);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        overflow-x: auto;
    }

    /* Tabla */
    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
        margin-top: -0.5rem;
    }

    .table th {
        background: transparent;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 0.75rem 1rem;
        border: none;
        white-space: nowrap;
    }

    .table tbody tr {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .table td {
        padding: 1rem;
        border: none;
        background: transparent;
        vertical-align: middle;
    }

    /* Estilos específicos para celdas */
    .product-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .product-icon {
        width: 40px;
        height: 40px;
        background: rgba(99, 102, 241, 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
    }

    .product-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .product-name {
        font-weight: 500;
        color: var(--text-primary);
    }

    .product-barcode {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .price-cell {
        font-weight: 600;
        color: var(--text-primary);
    }

    .stock-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .stock-badge i {
        font-size: 1rem;
    }

    .stock-high {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .stock-medium {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .stock-low {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .category-badge {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary-color);
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* DataTables personalización */
    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid rgba(0,0,0,0.05);
        border-radius: 0.75rem;
        padding: 0.625rem 1rem;
        padding-left: 2.5rem;
        transition: all 0.3s ease;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23666666'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.75rem center;
        background-size: 1rem;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    /* Botones de acción */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

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

    .btn-action::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at center, rgba(255,255,255,0.2) 0%, transparent 100%);
        transform: scale(0);
        transition: transform 0.5s ease;
    }

    .btn-action:hover::before {
        transform: scale(2);
    }

    .btn-action i {
        font-size: 1rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .btn-action.edit {
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary-color);
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .btn-action.edit:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }

    .btn-action.edit:hover i {
        transform: rotate(15deg);
    }

    .btn-action.delete {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .btn-action.delete:hover {
        background: var(--danger-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }

    .btn-action.delete:hover i {
        transform: scale(1.1);
    }

    /* Estilos para los modales */
    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(145deg, var(--primary-color), #6366f1);
        padding: 1.5rem;
        border: none;
        color: white;
    }

    .modal-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: white;
        font-weight: 600;
    }

    .modal-title i {
        font-size: 1.5rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .btn-close {
        color: white;
        filter: brightness(0) invert(1);
        opacity: 0.8;
        transition: all 0.2s ease;
    }

    .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 2rem 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(0,0,0,0.05);
        background: rgba(0,0,0,0.02);
    }

    /* Formulario dentro del modal */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label i {
        font-size: 1.25rem;
        color: var(--primary-color);
        opacity: 0.7;
    }

    .form-control, .form-select {
        border: 2px solid rgba(0,0,0,0.05);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
        background-color: white;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* Panel de información del producto */
    .product-info-panel {
        background: linear-gradient(145deg, #f8fafc, #f1f5f9);
        border-radius: 1rem;
        padding: 1.5rem;
        margin: 1rem 0;
        border: 2px solid rgba(99, 102, 241, 0.1);
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .info-label {
        color: var(--text-secondary);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Animaciones */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes rotating {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .rotating {
        animation: rotating 1s linear infinite;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .products-header {
            flex-direction: column;
            align-items: stretch;
        }

        .header-buttons {
            flex-direction: column;
        }

        .btn-main {
            width: 100%;
            justify-content: center;
        }

        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }
    }
</style>
@endpush

@section('content')
<div class="products-container">
    <div class="products-header">
        <h1 class="products-title">
            <i class="material-icons-round">inventory_2</i>
            Productos
        </h1>
        <div class="header-buttons">
            <button id="createProductBtn" class="btn-main create">
                <i class="material-icons-round">add</i>
                Crear Nuevo Producto
            </button>
            <button id="addInventoryBtn" class="btn-main inventory">
                <i class="material-icons-round">add_shopping_cart</i>
                Agregar Inventario
            </button>
        </div>
    </div>
    
    <div class="products-table-container">
        <table id="productsTable" class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Código de Barras</th>
                    <th>Costo</th>
                    <th>% Ganancia</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Crear/Editar Producto -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="material-icons-round">inventory_2</i>
                    <span id="productModalLabel">Crear Producto</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <input type="hidden" id="productId">
                    <div class="form-group">
                        <label for="productName" class="form-label">
                            <i class="material-icons-round">label</i>
                            Nombre del Producto
                        </label>
                        <input type="text" class="form-control" id="productName" required>
                    </div>
                    <div class="form-group">
                        <label for="productBarcode" class="form-label">
                            <i class="material-icons-round">qr_code_2</i>
                            Código de Barras
                        </label>
                        <input type="text" class="form-control" id="productBarcode" required>
                    </div>
                    <div class="form-group">
                        <label for="productCost" class="form-label">
                            <i class="material-icons-round">payments</i>
                            Costo
                        </label>
                        <input type="number" class="form-control" id="productCost" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="productProfit" class="form-label">
                            <i class="material-icons-round">trending_up</i>
                            Porcentaje de Ganancia
                        </label>
                        <input type="number" class="form-control" id="productProfit" required>
                    </div>
                    <div class="form-group">
                        <label for="productPrice" class="form-label">
                            <i class="material-icons-round">sell</i>
                            Precio de Venta
                        </label>
                        <input type="number" class="form-control" id="productPrice" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="productStock" class="form-label">
                            <i class="material-icons-round">inventory</i>
                            Stock
                        </label>
                        <input type="number" class="form-control" id="productStock" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory" class="form-label">
                            <i class="material-icons-round">category</i>
                            Categoría
                        </label>
                        <select class="form-select" id="productCategory" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="material-icons-round">close</i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="saveProductBtn">
                    <i class="material-icons-round">save</i>
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Inventario -->
<div class="modal fade" id="inventoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="material-icons-round">add_shopping_cart</i>
                    <span id="inventoryModalLabel">Agregar Inventario</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="inventoryForm">
                    <div class="form-group">
                        <label for="inventoryBarcode" class="form-label">
                            <i class="material-icons-round">qr_code_scanner</i>
                            Código de Barras
                        </label>
                        <input type="text" class="form-control" id="inventoryBarcode" required>
                    </div>

                    <div id="productInfo" class="product-info-panel" style="display: none;">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="material-icons-round">inventory_2</i>
                                Producto:
                            </div>
                            <div class="info-value" id="inventoryProductName"></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="material-icons-round">inventory</i>
                                Stock Actual:
                            </div>
                            <div class="info-value">
                                <span id="inventoryCurrentStock"></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="material-icons-round">payments</i>
                                Precio Actual:
                            </div>
                            <div class="info-value">$<span id="inventoryCurrentPrice"></span></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inventoryQuantity" class="form-label">
                            <i class="material-icons-round">add_shopping_cart</i>
                            Cantidad a Agregar
                        </label>
                        <input type="number" class="form-control" id="inventoryQuantity" required>
                    </div>
                    <div class="form-group">
                        <label for="inventoryCost" class="form-label">
                            <i class="material-icons-round">payments</i>
                            Nuevo Costo por Unidad
                        </label>
                        <input type="number" class="form-control" id="inventoryCost" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="inventoryProfit" class="form-label">
                            <i class="material-icons-round">trending_up</i>
                            Nuevo Porcentaje de Ganancia
                        </label>
                        <input type="number" class="form-control" id="inventoryProfit" required>
                    </div>
                    <div class="form-group">
                        <label for="inventoryNewPrice" class="form-label">
                            <i class="material-icons-round">sell</i>
                            Nuevo Precio de Venta
                        </label>
                        <input type="number" class="form-control" id="inventoryNewPrice" step="0.01" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="material-icons-round">close</i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="saveInventoryBtn">
                    <i class="material-icons-round">save</i>
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ProductManager = {
    table: null,

    init: function() {
        this.initializeDataTable();
        this.setupEventListeners();
        this.setupCalculations();
    },

    initializeDataTable: function() {
        if (this.table !== null) {
            this.table.destroy();
            this.table = null;
        }

        this.table = $('#productsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('products.index') }}",
            columns: [
                {
                    data: 'name',
                    name: 'name',
                    render: function(data, type, row) {
                        return `
                            <div class="product-info">
                                <div class="product-icon">
                                    <i class="material-icons-round">inventory_2</i>
                                </div>
                                <div class="product-details">
                                    <div class="product-name">${data}</div>
                                    <div class="product-barcode">${row.barcode}</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {data: 'barcode', name: 'barcode', visible: false},
                {
                    data: 'cost',
                    name: 'cost',
                    render: function(data) {
                        return `<div class="price-cell">$${parseFloat(data).toFixed(2)}</div>`;
                    }
                },
                {
                    data: 'profit_percentage',
                    name: 'profit_percentage',
                    render: function(data) {
                        return `<div class="price-cell">${data}%</div>`;
                    }
                },
                {
                    data: 'price',
                    name: 'price',
                    render: function(data) {
                        return `<div class="price-cell">$${parseFloat(data).toFixed(2)}</div>`;
                    }
                },
                {
                    data: 'stock',
                    name: 'stock',
                    render: function(data) {
                        let stockClass = 'stock-high';
                        let icon = 'inventory';
                        if (data < 10) {
                            stockClass = 'stock-low';
                            icon = 'error_outline';
                        } else if (data < 20) {
                            stockClass = 'stock-medium';
                            icon = 'warning';
                        }
                        
                        return `
                            <span class="stock-badge ${stockClass}">
                                <i class="material-icons-round">${icon}</i>
                                ${data}
                            </span>
                        `;
                    }
                },
                {
                    data: 'category.name',
                    name: 'category.name',
                    render: function(data) {
                        return `<span class="category-badge">${data}</span>`;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="action-buttons">
                                <button onclick="ProductManager.editProduct(${row.id})" 
                                        class="btn-action edit" 
                                        title="Editar producto">
                                    <i class="material-icons-round">edit</i>
                                </button>
                                <button onclick="ProductManager.deleteProduct(${row.id})" 
                                        class="btn-action delete" 
                                        title="Eliminar producto">
                                    <i class="material-icons-round">delete</i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true,
            autoWidth: false,
            dom: '<"top"lf>rt<"bottom"ip><"clear">',
            pageLength: 10,
            order: [[0, 'asc']],
            drawCallback: function() {
                $('[title]').tooltip();
            }
        });
    },

    setupEventListeners: function() {
        $('#createProductBtn').click(() => this.showCreateModal());
        $('#addInventoryBtn').click(() => this.showInventoryModal());
        $('#saveProductBtn').click(() => this.saveProduct());
        $('#saveInventoryBtn').click(() => this.saveInventory());

        $('#productModal').on('hidden.bs.modal', function() {
            $('#productForm')[0].reset();
            $('#productId').val('');
        });

        $('#inventoryModal').on('hidden.bs.modal', function() {
            $('#inventoryForm')[0].reset();
            $('#productInfo').hide();
        });
    },

    setupCalculations: function() {
        $('#productCost, #productProfit').on('input', () => this.calculateSalePrice('product'));
        $('#inventoryCost, #inventoryProfit').on('input', () => this.calculateSalePrice('inventory'));
        $('#inventoryBarcode').on('blur', () => this.findProductByBarcode());
    },

    calculateSalePrice: function(prefix) {
        const cost = parseFloat($('#' + prefix + 'Cost').val()) || 0;
        const profit = parseFloat($('#' + prefix + 'Profit').val()) || 0;
        const salePrice = cost * (1 + profit / 100);
        $('#' + prefix + (prefix === 'product' ? 'Price' : 'NewPrice')).val(salePrice.toFixed(2));
    },

    showCreateModal: function() {
        $('#productModalLabel').text('Crear Producto');
        $('#productId').val('');
        $('#productForm')[0].reset();
        new bootstrap.Modal(document.getElementById('productModal')).show();
    },

    showInventoryModal: function() {
        $('#inventoryForm')[0].reset();
        $('#productInfo').hide();
        new bootstrap.Modal(document.getElementById('inventoryModal')).show();
    },

    findProductByBarcode: function() {
        const barcode = $('#inventoryBarcode').val();
        if (!barcode) return;

        $.get("{{ url('products/find-by-barcode') }}/" + barcode)
            .done((data) => {
                if (data) {
                    $('#inventoryProductName').text(data.name);
                    $('#inventoryCurrentStock').text(`${data.stock} unidades`);
                    $('#inventoryCurrentPrice').text(parseFloat(data.price).toFixed(2));
                    $('#inventoryCost').val(data.cost);
                    $('#inventoryProfit').val(data.profit_percentage);
                    $('#inventoryNewPrice').val(data.price);
                    $('#productInfo').fadeIn(300);
                } else {
                    this.showNotification('error', 'Producto no encontrado');
                    $('#productInfo').hide();
                }
            })
            .fail(() => {
                this.showNotification('error', 'Error al buscar el producto');
                $('#productInfo').hide();
            });
    },

    editProduct: function(id) {
        $.get("{{ url('products') }}/" + id + "/edit")
            .done((data) => {
                $('#productModalLabel').text('Editar Producto');
                $('#productId').val(data.id);
                $('#productName').val(data.name);
                $('#productBarcode').val(data.barcode);
                $('#productCost').val(data.cost);
                $('#productProfit').val(data.profit_percentage);
                $('#productPrice').val(data.price);
                $('#productStock').val(data.stock);
                $('#productCategory').val(data.category_id);
                
                new bootstrap.Modal(document.getElementById('productModal')).show();
            })
            .fail(() => {
                this.showNotification('error', 'Error al cargar el producto');
            });
    },

    deleteProduct: function(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="material-icons-round">delete</i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            buttonsStyling: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('products') }}/" + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: (response) => {
                        this.table.ajax.reload();
                        this.showNotification('success', 'Producto eliminado con éxito');
                    },
                    error: (xhr) => {
                        this.showNotification('error', 'No se pudo eliminar el producto');
                    }
                });
            }
        });
    },

    saveProduct: function() {
        const id = $('#productId').val();
        const saveBtn = $('#saveProductBtn');
        const originalContent = saveBtn.html();
        
        saveBtn.html('<i class="material-icons-round rotating">sync</i> Guardando...').prop('disabled', true);

        const data = {
            name: $('#productName').val(),
            barcode: $('#productBarcode').val(),
            cost: $('#productCost').val(),
            profit_percentage: $('#productProfit').val(),
            price: $('#productPrice').val(),
            stock: $('#productStock').val(),
            category_id: $('#productCategory').val()
        };

        const url = id ? "{{ url('products') }}/" + id : "{{ route('products.store') }}";
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                this.table.ajax.reload();
                this.showNotification('success', id ? 'Producto actualizado con éxito' : 'Producto creado con éxito');
            },
            error: (xhr) => {
                this.showNotification('error', 'Hubo un problema al guardar el producto');
            },
            complete: () => {
                saveBtn.html(originalContent).prop('disabled', false);
            }
        });
    },

    saveInventory: function() {
        const saveBtn = $('#saveInventoryBtn');
        const originalContent = saveBtn.html();
        
        saveBtn.html('<i class="material-icons-round rotating">sync</i> Guardando...').prop('disabled', true);

        const data = {
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
            success: (response) => {
                bootstrap.Modal.getInstance(document.getElementById('inventoryModal')).hide();
                this.table.ajax.reload();
                this.showNotification('success', 'Inventario actualizado con éxito');
            },
            error: (xhr) => {
                this.showNotification('error', 'Hubo un problema al actualizar el inventario');
            },
            complete: () => {
                saveBtn.html(originalContent).prop('disabled', false);
            }
        });
    },

    showNotification: function(type, message) {
        const config = {
            icon: type,
            title: type === 'success' ? '¡Éxito!' : 'Error',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            iconColor: type === 'success' ? '#10B981' : '#EF4444',
            customClass: {
                popup: 'colored-toast'
            }
        };
        Swal.fire(config);
    }
};

// Animación para el icono de carga
document.head.insertAdjacentHTML('beforeend', `
    <style>
        @keyframes rotating {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .rotating {
            animation: rotating 1s linear infinite;
        }
        .colored-toast.swal2-icon-success {
            background: #10B981 !important;
            color: white !important;
        }
        .colored-toast.swal2-icon-error {
            background: #EF4444 !important;
            color: white !important;
        }
    </style>
`);

// Inicializar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    ProductManager.init();
});

// Exponer métodos necesarios globalmente
window.ProductManager = ProductManager;
</script>
@endpush