<?php

include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();

$cliente = $_POST['cliente'];
$proceso = $_POST['proceso'];

$connnect = $conn->connectToServ();
$productor =  json_decode($functions->getProductorProceso($connnect, $cliente, $proceso));
if ($productor->productor == 0) {
    echo "<script>alert('No existe orden para este productor.');</script>";
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
