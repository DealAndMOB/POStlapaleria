@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Categorías</h1>
    <button id="createCategoryBtn" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Crear Nueva Categoría
    </button>
    
    <table id="categoriesTable" class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal para Crear/Editar Categoría -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Crear Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="categoryName" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('categories.index') }}",
        columns: [
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        }
    });

    $('#createCategoryBtn').click(function() {
        $('#categoryModalLabel').text('Crear Categoría');
        $('#categoryId').val('');
        $('#categoryName').val('');
        var modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        modal.show();
    });

    $('#saveCategoryBtn').click(function() {
        var id = $('#categoryId').val();
        var name = $('#categoryName').val();
        var url = id ? "{{ url('categories') }}/" + id : "{{ route('categories.store') }}";
        var method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: {name: name},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                var modal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
                modal.hide();
                Swal.fire('¡Éxito!', id ? 'Categoría actualizada.' : 'Categoría creada.', 'success');
                table.ajax.reload();
            },
            error: function(xhr) {
                Swal.fire('Error', 'Hubo un problema al guardar la categoría.', 'error');
            }
        });
    });

    window.editCategory = function(id) {
        $.get("{{ url('categories') }}/" + id + "/edit", function(data) {
            $('#categoryModalLabel').text('Editar Categoría');
            $('#categoryId').val(data.id);
            $('#categoryName').val(data.name);
            var modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        });
    }

    window.deleteCategory = function(id) {
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
                    url: "{{ url('categories') }}/" + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('¡Eliminado!', 'La categoría ha sido eliminada.', 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Hubo un problema al eliminar la categoría.', 'error');
                    }
                });
            }
        });
    }
});
</script>
@endpush