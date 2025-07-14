$(document).ready(function () {
    $('button[name="search"]').on('click', function (event) {
        var proceso = $('#proceso').val();
        var cliente = $('#cliente').val();
        $.ajax({
            type: "POST",
            url: "./data/detalle_proceso.php",
            data: {
                proceso: proceso,
                cliente: cliente
            },
            dataType: "html",
            beforeSend: function () {
                $('#orden').html('<div class="d-flex justify-content-center mt-3"><div class="spinner-border" role="status"></div></div>');
            },
            success: function (response) {
                $('#orden').html(response);
            },
        });



    });
});
function vaciarLote(loteId, cliente, proceso) {
    $.ajax({
        type: "POST",
        url: "./data/vaciar_lote.php",
        data: {
            loteId: loteId,
            cliente: cliente,
            proceso: proceso
        },
        dataType: "json",
        success: function (response) {
            if (response.error == 'si') {
                alert(response.message);
            } else {
                alert(response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            console.error("Detalles de la respuesta:", xhr.responseText);
            alert('Error al procesar la solicitud' + xhr.responseText);
        }
    });
}