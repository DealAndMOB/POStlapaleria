@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Reporte de Ventas y Cortes de Caja</h1>

    <!-- Sección de Inicio/Cierre de Turno -->
    <div class="card mb-4">
        <div class="card-header">
            <h2>Turno Actual</h2>
        </div>
        <div class="card-body">
            @if($activeClosure)
                <p>Turno iniciado el: {{ $activeClosure->start_time }}</p>
                <p>Efectivo inicial: ${{ number_format($activeClosure->initial_cash, 2) }}</p>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#closeTurnModal">
                    Cerrar Turno
                </button>
            @else
                <p>No hay un turno activo.</p>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#openTurnModal">
                    Iniciar Turno
                </button>
            @endif
        </div>
    </div>

    <!-- Sección de Ventas Recientes -->
    <div class="card mb-4">
        <div class="card-header">
            <h2>Ventas Recientes</h2>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->created_at }}</td>
                        <td>${{ number_format($sale->total, 2) }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#saleDetailsModal" data-sale-id="{{ $sale->id }}">
                                Ver Detalles
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sección de Cortes de Caja Anteriores -->
    <div class="card">
        <div class="card-header">
            <h2>Cortes de Caja Anteriores</h2>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha de Inicio</th>
                        <th>Fecha de Cierre</th>
                        <th>Efectivo Inicial</th>
                        <th>Efectivo Final</th>
                        <th>Total Ventas</th>
                        <th>Diferencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previousClosures as $closure)
                    <tr>
                        <td>{{ $closure->start_time }}</td>
                        <td>{{ $closure->end_time }}</td>
                        <td>${{ number_format($closure->initial_cash, 2) }}</td>
                        <td>${{ number_format($closure->final_cash, 2) }}</td>
                        <td>${{ number_format($closure->total_sales, 2) }}</td>
                        <td>${{ number_format($closure->difference, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Iniciar Turno -->
<div class="modal fade" id="openTurnModal" tabindex="-1" role="dialog" aria-labelledby="openTurnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('cash_register_closures.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="openTurnModalLabel">Iniciar Turno</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="initial_cash">Efectivo Inicial:</label>
                        <input type="number" class="form-control" id="initial_cash" name="initial_cash" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notas:</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Iniciar Turno</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Cerrar Turno -->
<div class="modal fade" id="closeTurnModal" tabindex="-1" role="dialog" aria-labelledby="closeTurnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('cash_register_closures.close') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="closeTurnModalLabel">Cerrar Turno</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="final_cash">Efectivo Final:</label>
                        <input type="number" class="form-control" id="final_cash" name="final_cash" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notas:</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cerrar Turno</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Detalles de Venta -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1" role="dialog" aria-labelledby="saleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleDetailsModalLabel">Detalles de la Venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Los detalles de la venta se cargarán aquí mediante AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#saleDetailsModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var saleId = button.data('sale-id');
        var modal = $(this);
        
        // Cargar detalles de la venta mediante AJAX
        $.ajax({
            url: '/sales/' + saleId,
            type: 'GET',
            success: function(response) {
                modal.find('.modal-body').html(response);
            },
            error: function(xhr) {
                modal.find('.modal-body').html('Error al cargar los detalles de la venta.');
            }
        });
    });
});
</script>
@endpush