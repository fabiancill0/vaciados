<?php
include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();
$cliente = $_POST['cliente'];
$functions->getProcesosDiarios($conn->connectToServ(), $cliente);
