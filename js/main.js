$(document).ready(function () {
    $('#cliente').on('change', function () {
        $('#list_procesos').load("./data/procesos_diarios.php", { cliente: $('#cliente').val() });
        $('#proceso').removeClass('border border-danger bg-danger-subtle')
        $('#proceso_label').removeClass('text-danger')
    });
    $('#ordenes').on('click', function () {
        $('#list_procesos').load("./data/procesos_diarios.php", { cliente: $('#cliente').val() });
        $('#proceso').removeClass('border border-danger bg-danger-subtle')
        $('#proceso_label').removeClass('text-danger')
    });
    $('button[name="search"]').on('click', function (event) {
        $('#proceso').removeClass('border border-danger bg-danger-subtle')
        $('#proceso_label').removeClass('text-danger')
        var proceso = $('#proceso').val();
        var cliente = $('#cliente').val();
        if (!proceso) {
            alert('Favor digitar o seleccionar proceso a vaciar!')
            $('#proceso').addClass('border border-danger bg-danger-subtle')
            $('#proceso_label').addClass('text-danger')
        } else {
            $.ajax({
                type: "POST",
                url: "./data/resultado_proc.php",
                data: {
                    proceso: proceso,
                    cliente: cliente
                },
                dataType: "html",
                beforeSend: function () {
                    $('#orden').html('<div class="d-flex justify-content-center mt-3"><div class="spinner-border" role="status"></div></div>');
                },
                success: function (response) {
                    $('#total_proc').html(response);
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
        }

    });
});
function swapPlace(proceso) {
    $('#proceso').val(proceso);
    $('button[name="search"]').trigger('click');
    $('#proceso').removeClass('border border-danger bg-danger-subtle')
    $('#proceso_label').removeClass('text-danger')
}
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
                $('#prod').html('');
                $('#total_proc').html('');
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
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: "./data/resultado_proc.php",
                        data: {
                            proceso: proceso,
                            cliente: cliente
                        },
                        dataType: "html",
                        beforeSend: function () {
                            $('#orden').html('<div class="d-flex justify-content-center mt-3"><div class="spinner-border" role="status"></div></div>');
                        },
                        success: function (response) {
                            $('#total_proc').html(response);
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
                    $('#' + loteId + '_deta').html('<i class="bi bi-plus-square"></i> Vaciar');
                    $('#' + loteId + '_deta').prop('disabled', false);
                    $('#' + loteId).html('<i class="bi bi-plus-square"></i> Vaciar');
                    $('#' + loteId).prop('disabled', false);
                }
                else if (response.error_type == 4) {
                    alert(response.message);
                    $('#' + loteId).html('<i class="bi bi-plus-square"></i> Vaciar');
                    $('#' + loteId).prop('disabled', false);
                }
                else if (response.error_type == 5) {
                    alert(response.message);
                    $('#' + loteId).html('<i class="bi bi-plus-square"></i> Vaciar');
                    $('#' + loteId).prop('disabled', false);
                }
                else if (response.error_type == 6) {
                    alert(response.message);
                    $('#' + loteId).html('<i class="bi bi-plus-square"></i> Vaciar');
                    $('#' + loteId).prop('disabled', false);
                }
            } else {
                alert(response.message);
                document.getElementById(loteId + '_row_canBul').valueAsNumber += response.bultosLote;
                var kilosVaciados = parseFloat(document.getElementById('totKilVac').value) || 0;
                var pesoVaciados = parseFloat(response.pesoLote) || 0;
                var nuevoValor = kilosVaciados + pesoVaciados
                document.getElementById('totKilVac').value = nuevoValor.toFixed(2);
                document.getElementById("totBulVac").valueAsNumber += response.bultosLote;
                $('#' + loteId + '_deta').html('<i class="bi bi-check2-square"></i> Vaciado');
                $('#' + loteId + '_deta').prop('disabled', true);
                $('#' + loteId).html('<i class="bi bi-check2-square"></i> Vaciado');
                $('#' + loteId).prop('disabled', true);

            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            console.error("Detalles de la respuesta:", xhr.responseText);
            alert('Error al procesar la solicitud' + xhr.responseText);
            $('#' + loteId).html('<i class="bi bi-plus-square"></i> Vaciar');
            $('#' + loteId).prop('disabled', false);
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
                $('#' + loteId + '_deta').html('<i class="bi bi-check2-square"></i> Vaciado');
                $('#' + loteId + '_deta').prop('disabled', true);
                $('#' + loteId).html('<i class="bi bi-check2-square"></i> Vaciado');
                $('#' + loteId).prop('disabled', true);
            } else {
                $('#' + loteId + '_row_deta').append('<div class="row g-0 text-bg-secondary sticky-top tarjas-' + loteId + '" style="top: 62px; z-index: 1030"><div class="col-12">' +
                    '<h5 class="m-0 text-center">Detalle Lote: ' + loteId + '</h5></div></div>');
                $.each(response, function (index, valueOfElement) {
                    if (valueOfElement.estado == 'vaciada') {
                        $('#' + loteId + '_row_deta').append('<div class="col-xl-3 col-md-4 col-6 tarjas-' + loteId + '"><div id="' + valueOfElement.nroTarja + '_card" class="card card-sm text-center border-success fw-bold">' +
                            '<div id="' + valueOfElement.nroTarja + '_header" class="card-header bg-success-subtle border-success">Tarja: ' + valueOfElement.nroTarja + '</div>' +
                            '<div id="' + valueOfElement.nroTarja + '_body" class="card-body bg-success-subtle border-success"><div class="row g-0">' +
                            '<div class="col-6">Kilos: ' + valueOfElement.pesoNeto + '</div>' +
                            '<div class="col-6">Bultos: ' + valueOfElement.canBul + '</div></div></div>' +
                            '<div id="' + valueOfElement.nroTarja + '_footer" class="p-0 card-footer bg-success-subtle border-success"><button id="' + valueOfElement.nroTarja + '" class="rounded-bottom rounded-top-0 btn btn-success btn-sm col-12 h-100 fw-bold" onclick="vaciarTarja(' + loteId + ', ' + valueOfElement.nroTarja + ', ' + cliente + ', ' + proceso + ')" disabled><i class="bi bi-check2-square"></i> Vaciada</button></div></div></div>');
                    } else {
                        $('#' + loteId + '_row_deta').append('<div class="col-xl-3 col-md-4 col-6 tarjas-' + loteId + '"><div id="' + valueOfElement.nroTarja + '_card" class="card card-sm text-center border-warning fw-bold">' +
                            '<div id="' + valueOfElement.nroTarja + '_header" class="card-header bg-warning-subtle border-warning">Tarja: ' + valueOfElement.nroTarja + '</div>' +
                            '<div id="' + valueOfElement.nroTarja + '_body" class="card-body bg-warning-subtle border-warning"><div class="row g-0">' +
                            '<div class="col-6">Kilos: ' + valueOfElement.pesoNeto + '</div>' +
                            '<div class="col-6">Bultos: ' + valueOfElement.canBul + '</div></div></div>' +
                            '<div id="' + valueOfElement.nroTarja + '_footer" class="p-0 card-footer bg-warning-subtle border-warning"><button id="' + valueOfElement.nroTarja + '" class="rounded-bottom rounded-top-0 btn btn-warning btn-sm col-12 h-100 fw-bold" onclick="vaciarTarja(' + loteId + ', ' + valueOfElement.nroTarja + ', ' + cliente + ', ' + proceso + ')"><i class="bi bi-plus-square"></i> Vaciar</button></div></div></div>');
                    }
                });
                $('#' + loteId + '_deta').html('<i class="bi bi-dash-square"></i> Cerrar');
                $('#' + loteId + '_row').addClass('sticky-top');
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
    $('#' + loteId + '_deta').html('<i class="bi bi-plus-square"></i> Tarjas');

}
function mostrarDetalle(cliente, proceso) {
    $.ajax({
        beforeSend: function () {
            $('#deta_proc').html('<div class="d-flex justify-content-center mt-3"><div class="spinner-border" role="status"></div></div>');
        },
        success: function (response) {
            $('#deta_proc').load("./data/detalle_vaciado.php", { proceso: proceso, cliente: cliente });
        }
    });

}
function vaciarTarja(loteId, tarjaId, cliente, proceso) {
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
                    $('#' + tarjaId).html('<i class="bi bi-check2-square"></i> Vaciado');
                    $('#' + tarjaId).prop('disabled', true);
                }
                else if (response.error_type == 4) {
                    alert(response.message);
                    $('#' + tarjaId).html('<i class="bi bi-plus-square"></i> Vaciar');
                    $('#' + tarjaId).prop('disabled', false);
                }
                else if (response.error_type == 5) {
                    alert(response.message);
                    $('#' + tarjaId).html('<i class="bi bi-plus-square"></i> Vaciar');
                    $('#' + tarjaId).prop('disabled', false);
                }
                else if (response.error_type == 6) {
                    alert(response.message);
                    $('#' + tarjaId).html('<i class="bi bi-plus-square"></i> Vaciar');
                    $('#' + tarjaId).prop('disabled', false);
                }
            } else {
                alert(response.message);
                document.getElementById(loteId + '_row_canBul').valueAsNumber += response.bulVac;
                var kilosVaciados = parseFloat(document.getElementById('totKilVac').value) || 0;
                var pesoVaciados = parseFloat(response.pesoVac) || 0;
                var nuevoValor = kilosVaciados + pesoVaciados
                document.getElementById('totKilVac').value = nuevoValor.toFixed(2);
                document.getElementById("totBulVac").valueAsNumber += response.bulVac;
                $('#' + tarjaId).removeClass('btn-warning')
                $('#' + tarjaId + '_header').removeClass('bg-warning-subtle border-warning');
                $('#' + tarjaId + '_footer').removeClass('bg-warning-subtle border-warning');
                $('#' + tarjaId + '_body').removeClass('bg-warning-subtle border-warning');
                $('#' + tarjaId + '_card').removeClass('border-warning');
                $('#' + tarjaId).addClass('btn-success')
                $('#' + tarjaId + '_header').addClass('bg-success-subtle border-success');
                $('#' + tarjaId + '_footer').addClass('bg-success-subtle border-success');
                $('#' + tarjaId + '_body').addClass('bg-success-subtle border-success');
                $('#' + tarjaId + '_card').addClass('border-success');
                $('#' + tarjaId).html('<i class="bi bi-check2-square"></i> Vaciado');
                $('#' + tarjaId).prop('disabled', true);

            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            console.error("Detalles de la respuesta:", xhr.responseText);
            alert('Error al procesar la solicitud' + xhr.responseText);
            $('#' + tarjaId).html('<i class="bi bi-plus-square"></i> Vaciar');
            $('#' + tarjaId).prop('disabled', false);
        }
    });
}