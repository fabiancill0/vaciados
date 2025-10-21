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
$productor =  json_decode($functions->getProductorProceso($connnect, $cliente, $proceso));
if ($productor->error) {
    echo "<script>alert('No se encontró el número de traspaso para el cliente y proceso especificados.');</script>";
    exit;
} else {
?>
    <div class="card mt-1 mb-1">
        <div class="card-header">
            <h6 class="text-center"><?= $productor->productor ?></h6>
        </div>
    </div>
<?php
}
