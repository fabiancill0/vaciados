<?php
include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();
$cliente = $_POST['cliente'];
if ($cliente == 15) {
    $connnect = $conn->connectToRK();
} else {
    $connnect = $conn->connectToServ();
}
$functions->getProcesosDiarios($connnect, $cliente);
