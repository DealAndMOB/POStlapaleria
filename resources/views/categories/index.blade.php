@extends('layouts.app')

@push('styles')
<style>
    /* Contenedor principal */
    .categories-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        animation: fadeIn 0.3s ease;
    }

    /* Título y botón superior */
    .categories-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .categories-title {
        font-size: 1.875rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }

    .categories-title i {
        color: var(--primary-color);
        font-size: 2rem;
    }

    /* Botón principal mejorado */
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

    .btn-main i {
        font-size: 1.25rem;
        transition: transform 0.3s ease;
    }

    .btn-main:hover i {
        transform: translateY(-1px);
    }

    /* Contenedor de la tabla */
    .categories-table-container {
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
    .category-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .category-icon {
        width: 40px;
        height: 40px;
        background: rgba(99, 102, 241, 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
    }

    .category-name {
        font-weight: 500;
        color: var(--text-primary);
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

    /* Modal */
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

    /* Formulario */
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

    .form-control {
        border: 2px solid rgba(0,0,0,0.05);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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
        .categories-header {
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
<div class="categories-container">
    <div class="categories-header">
        <h1 class="categories-title">
            <i class="material-icons-round">category</i>
            Categorías
        </h1>
        <button id="createCategoryBtn" class="btn-main create">
            <i class="material-icons-round">add</i>
            Crear Nueva Categoría
        </button>
    </div>
    
    <div class="categories-table-container">
        <table id="categoriesTable" class="table">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Crear/Editar Categoría -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="material-icons-round">category</i>
                    <span id="categoryModalLabel">Crear Categoría</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId">
                    <div class="form-group">
                        <label for="categoryName" class="form-label">
                            <i class="material-icons-round">label</i>
                            Nombre de la Categoría
                        </label>
                        <input type="text" class="form-control" id="categoryName" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="material-icons-round">close</i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
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
const CategoryManager = {
    table: null,

    init: function() {
        this.initializeDataTable();
        this.setupEventListeners();
    },

    initializeDataTable: function() {
        if (this.table !== null) {
            this.table.destroy();
            this.table = null;
        }

        this.table = $('#categoriesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('categories.index') }}",
            columns: [
                {
                    data: 'name',
                    name: 'name',
                    render: function(data, type, row) {
                        return `
                            <div class="category-info">
                                <div class="category-icon">
                                    <i class="material-icons-round">category</i>
                                </div>
                                <div class="category-name">${data}</div>
                            </div>
                        `;
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
                                <button onclick="CategoryManager.editCategory(${row.id})" 
                                        class="btn-action edit" 
                                        title="Editar categoría">
                                    <i class="material-icons-round">edit</i>
                                </button>
                                <button onclick="CategoryManager.deleteCategory(${row.id})" 
                                        class="btn-action delete" 
                                        title="Eliminar categoría">
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
        $('#createCategoryBtn').click(() => this.showCreateModal());
        $('#saveCategoryBtn').click(() => this.saveCategory());

        $('#categoryModal').on('hidden.bs.modal', function() {
            $('#categoryForm')[0].reset();
            $('#categoryId').val('');
        });
    },

    showCreateModal: function() {
        $('#categoryModalLabel').text('Crear Categoría');
        $('#categoryId').val('');
        $('#categoryForm')[0].reset();
        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    },

    editCategory: function(id) {
        $.get("{{ url('categories') }}/" + id + "/edit")
            .done((data) => {
                $('#categoryModalLabel').text('Editar Categoría');
                $('#categoryId').val(data.id);
                $('#categoryName').val(data.name);
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
            })
            .fail(() => {
                this.showNotification('error', 'Error al cargar la categoría');
            });
    },

    saveCategory: function() {
        const id = $('#categoryId').val();
        const saveBtn = $('#saveCategoryBtn');
        const originalContent = saveBtn.html();
        
        saveBtn.html('<i class="material-icons-round rotating">sync</i> Guardando...').prop('disabled', true);

        const data = {
            name: $('#categoryName').val()
        };

        const url = id ? "{{ url('categories') }}/" + id : "{{ route('categories.store') }}";
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                this.table.ajax.reload();
                this.showNotification('success', id ? 'Categoría actualizada con éxito' : 'Categoría creada con éxito');
            },
            error: (xhr) => {
                this.showNotification('error', 'Hubo un problema al guardar la categoría');
            },
            complete: () => {
                saveBtn.html(originalContent).prop('disabled', false);
            }
        });
    },

    deleteCategory: function(id) {
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
                    url: "{{ url('categories') }}/" + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: (response) => {
                        this.table.ajax.reload();
                        this.showNotification('success', 'Categoría eliminada con éxito');
                    },
                    error: (xhr) => {
                        this.showNotification('error', 'No se pudo eliminar la categoría');
                    }
                });
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
    CategoryManager.init();
});

// Exponer métodos necesarios globalmente
window.CategoryManager = CategoryManager;
</script>
@endpush
