<?php
include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();

$cliente = $_POST['cliente'];
$proceso = $_POST['proceso'];

$connnect = $conn->connectToServ();

$queryDelete = "DELETE FROM dba.spro_ordenprocvacdeta WHERE orpr_numero = ? AND clie_codigo = ?";
$resultDelete = odbc_prepare($connnect, $queryDelete);
if (!odbc_execute($resultDelete, [$proceso, $cliente])) {
    echo json_encode(['error' => 'si', 'error_type' => 4, 'message' => 'Error en el proceso de eliminación de vaciado: ' . odbc_errormsg($connnect)]);
    exit;
} else {
    echo json_encode(['error' => 'no', 'message' => 'Vaciado eliminado correctamente']);
}
