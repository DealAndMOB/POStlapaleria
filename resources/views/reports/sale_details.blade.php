<!-- resources/views/sale_details.blade.php -->
<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>${{ number_format($item->price, 2) }}</td>
            <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total</th>
            <th>${{ number_format($sale->total, 2) }}</th>
        </tr>
    </tfoot>
</table>

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
            error: function(xhr, status, error) {
                console.error("Error al cargar los detalles de la venta:", error);
                modal.find('.modal-body').html('Error al cargar los detalles de la venta: ' + error);
            }
        });
    });
});
</script>
@endpush