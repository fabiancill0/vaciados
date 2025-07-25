$(document).ready(function () {
    $('button[name="search"]').on('click', function (event) {
        var proceso = $('#proceso').val();
        var cliente = $('#cliente').val();
        $.ajax({
            type: "POST",
            url: "./data/productor.php",
            data: {
                proceso: proceso,
                cliente: cliente
            },
            dataType: "html",
            beforeSend: function () {
                $('#orden').html('<div class="d-flex justify-content-center mt-3"><div class="spinner-border" role="status"></div></div>');
            },
            success: function (response) {
                $('#prod').html(response);
            },
        });
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
    if (confirm('Seguro deseas eliminar este movimiento?')) {
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
                    if (responseDel.error_type == 1) {
                        alert(responseDel.message);
                    }
                    else if (responseDel.error_type == 4) {
                        alert(responseDel.message);
                        $('#orden').load("./data/detalle_proceso.php", { proceso: proceso, cliente: cliente });
                    }
                    else if (responseDel.error_type == 5) {
                        alert(responseDel.message);
                        $('#orden').load("./data/detalle_proceso.php", { proceso: proceso, cliente: cliente });
                    }
                    else if (responseDel.error_type == 6) {
                        alert(responseDel.message);
                        $('#orden').load("./data/detalle_proceso.php", { proceso: proceso, cliente: cliente });
                    }
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
    } else {
        alert('Eliminación cancelada.');
    }

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
                    $('#' + loteId + '_deta').html('<i class="fa-solid fa-check"></i> Vaciado');
                    $('#' + loteId + '_deta').prop('disabled', true);
                    $('#' + loteId).html('<i class="fa-solid fa-check"></i> Vaciado');
                    $('#' + loteId).prop('disabled', true);
                }
                else if (response.error_type == 4) {
                    alert(response.message);
                    $('#' + loteId).html('<i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar');
                    $('#' + loteId).prop('disabled', false);
                }
                else if (response.error_type == 5) {
                    alert(response.message);
                    $('#' + loteId).html('<i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar');
                    $('#' + loteId).prop('disabled', false);
                }
                else if (response.error_type == 6) {
                    alert(response.message);
                    $('#' + loteId).html('<i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar');
                    $('#' + loteId).prop('disabled', false);
                }
            } else {
                alert(response.message);
                $('#' + loteId + '_deta').html('<i class="fa-solid fa-check"></i> Vaciado');
                $('#' + loteId + '_deta').prop('disabled', true);
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
function desplegarLote(loteId, cliente, proceso) {
    $.ajax({
        type: "POST",
        url: "./data/detalle_lote.php",
        data: {
            loteId: loteId,
            cliente: cliente,
            proceso: proceso
        },
        dataType: "json",
        beforeSend: function () {
            $('#' + loteId + '_deta').html('<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>');
            $('#' + loteId + '_deta').prop('disabled', true);
        },
        success: function (response) {
            if (response.error == 'si') {
                alert(response.message);
                $('#' + loteId + '_deta').html('<i class="fa-solid fa-check"></i> Vaciado');
                $('#' + loteId + '_deta').prop('disabled', true);
                $('#' + loteId).html('<i class="fa-solid fa-check"></i> Vaciado');
                $('#' + loteId).prop('disabled', true);
            } else {

                $.each(response, function (index, valueOfElement) {
                    if (valueOfElement.estado == 'vaciada') {
                        $('#' + loteId + '_row').after('<tr class="border border-warning-subtle tarjas-' + loteId + '">' +
                            '</td><td>' + valueOfElement.nroTarja +
                            '</td><td>' + valueOfElement.pesoNeto +
                            '</td><td>' + valueOfElement.canBul +
                            '</td><td colspan="2"><button id="' + valueOfElement.nroTarja + '" class="btn btn-success" onclick="vaciarTarja(' + valueOfElement.nroTarja + ', ' + cliente + ', ' + proceso + ')" disabled><i class="fa-solid fa-check"></i> Vaciada</button>' +
                            '</td></tr>');
                    } else {
                        $('#' + loteId + '_row').after('<tr class="border border-warning-subtle tarjas-' + loteId + '">' +
                            '</td><td>' + valueOfElement.nroTarja +
                            '</td><td>' + valueOfElement.pesoNeto +
                            '</td><td>' + valueOfElement.canBul +
                            '</td><td colspan="2"><button id="' + valueOfElement.nroTarja + '" class="btn btn-success" onclick="vaciarTarja(' + valueOfElement.nroTarja + ', ' + cliente + ', ' + proceso + ')"><i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar</button>' +
                            '</td></tr>');
                    }
                });
                $('#' + loteId + '_row').after('<tr class="tarjas-' + loteId + ' table-active"><th>Tarja</th><th>Kilos</th><th>Bultos</th><th colspan="2">Acción</th></tr>');
                $('#' + loteId + '_deta').html('<i class="fa-solid fa-caret-up"></i> Cerrar');
                $('#' + loteId + '_deta').prop('disabled', false);
                $('#' + loteId + '_deta').attr('onclick', 'cerrarDetalle(' + loteId + ', ' + cliente + ', ' + proceso + ')');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            console.error("Detalles de la respuesta:", xhr.responseText);
            alert('Error al procesar la solicitud' + xhr.responseText);
        }
    });
}
function cerrarDetalle(loteId, cliente, proceso) {
    document.querySelectorAll('.tarjas-' + loteId).forEach(function (element) {
        element.remove();
    });
    $('#' + loteId + '_deta').attr('onclick', 'desplegarLote(' + loteId + ', ' + cliente + ', ' + proceso + ')');
    $('#' + loteId + '_deta').html('<i class="fa-solid fa-caret-up fa-flip-vertical"></i> Tarjas');

}
function vaciarTarja(tarjaId, cliente, proceso) {
    $.ajax({
        type: "POST",
        url: "./data/vaciar_tarja.php",
        data: {
            tarjaId: tarjaId,
            cliente: cliente,
            proceso: proceso
        },
        dataType: "json",
        beforeSend: function () {
            $('#' + tarjaId).html('<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> <span role="status">Vaciando...</span>');
            $('#' + tarjaId).prop('disabled', true);
        },
        success: function (response) {
            if (response.error == 'si') {
                if (response.error_type == 1) {
                    alert(response.message);
                    $('#' + tarjaId).html('<i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar');
                    $('#' + tarjaId).prop('disabled', false);
                }
                else if (response.error_type == 4) {
                    alert(response.message);
                    $('#' + tarjaId).html('<i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar');
                    $('#' + tarjaId).prop('disabled', false);
                }
                else if (response.error_type == 5) {
                    alert(response.message);
                    $('#' + tarjaId).html('<i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar');
                    $('#' + tarjaId).prop('disabled', false);
                }
                else if (response.error_type == 6) {
                    alert(response.message);
                    $('#' + tarjaId).html('<i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar');
                    $('#' + tarjaId).prop('disabled', false);
                }
            } else {
                alert(response.message);
                $('#' + tarjaId).html('<i class="fa-solid fa-check"></i> Vaciado');
                $('#' + tarjaId).prop('disabled', true);

            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            console.error("Detalles de la respuesta:", xhr.responseText);
            alert('Error al procesar la solicitud' + xhr.responseText);
        }
    });
}