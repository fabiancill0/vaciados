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
function eliminarVaciado() {
    var proceso = $('#proceso').val();
    var cliente = $('#cliente').val();
    $.ajax({
        type: "POST",
        url: "./data/eliminar_vaciado.php",
        data: {
            proceso: proceso,
            cliente: cliente
        },
        dataType: "json",
        beforeSend: function () {
            $('#orden').html('<div class="d-flex justify-content-center mt-3"><div class="spinner-border" role="status"></div></div>');
        },
        success: function (responseDel) {
            if (responseDel.error == 'si') {
                alert(responseDel.message);
            } else {
                alert(responseDel.message);
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
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            console.error("Detalles de la respuesta:", xhr.responseText);
            alert('Error al procesar la solicitud' + xhr.responseText);
        }
    });
}
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
        beforeSend: function () {
            $('#' + loteId).html('<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> <span role="status">Vaciando...</span>');
            $('#' + loteId).prop('disabled', true);
        },
        success: function (response) {
            if (response.error == 'si') {
                if (response.error_type == 1) {
                    alert(response.message);
                    $('#' + loteId).html('<i class="fa-solid fa-check"></i> Vaciado');
                    $('#' + loteId).prop('disabled', true);
                }
            } else {
                alert(response.message);
                $('#' + loteId).html('<i class="fa-solid fa-check"></i> Vaciado');
                $('#' + loteId).prop('disabled', true);

            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            console.error("Detalles de la respuesta:", xhr.responseText);
            alert('Error al procesar la solicitud' + xhr.responseText);
        }
    });
}