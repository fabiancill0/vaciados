<?php
include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();

$cliente = $_POST['cliente'];
$proceso = $_POST['proceso'];

if ($cliente == 15) {
    $connnect = $conn->connectToRK();
} else {
    $connnect = $conn->connectToServ();
}

$queryDelete = "DELETE FROM dba.spro_ordenprocvacdeta WHERE orpr_numero = ? AND clie_codigo = ?";
$resultDelete = odbc_prepare($connnect, $queryDelete);
$estadoProceso = json_decode($functions->getEstadoProcesoMovimento($connnect, $cliente, $proceso));
if ($estadoProceso->estado == 0) {
    echo json_encode(['error' => 'si', 'error_type' => 4, 'message' => 'Orden de proceso no encontrada.']);
    exit;
} else if ($estadoProceso->estado == 'termino') {
    echo json_encode(['error' => 'si', 'error_type' => 5, 'message' => 'Termino de proceso realizado, solicite a tarjado comercial anular el término.']);
    exit;
} else if ($estadoProceso->estado == 'cierre') {
    echo json_encode(['error' => 'si', 'error_type' => 6, 'message' => 'Proceso cerrado, informe a sistemas.']);
    exit;
} else {
    if (!odbc_execute($resultDelete, [$proceso, $cliente])) {
        echo json_encode(['error' => 'si', 'error_type' => 1, 'message' => 'Error en el proceso de eliminación de vaciado: ' . odbc_errormsg($connnect)]);
        exit;
    } else {
        echo json_encode(['error' => 'no', 'message' => 'Vaciado eliminado correctamente']);
    }
}
