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
$dataTraspaso = json_decode($functions->getNumeroTraspaso($connnect, $cliente, $proceso));
$numeroTraspaso = $dataTraspaso->nroTraspaso;
if ($numeroTraspaso == 0) {
    echo "<script>alert('No se encontró el número de traspaso para el cliente y proceso especificados.');</script>";
    exit;
} else {

    $lotesDetalle = json_decode($functions->getLotesXVaciarDeta($connnect, $dataTraspaso->codEspecie, $numeroTraspaso), true);
    $lotesVaciados = json_decode($functions->getLotesVaciados($connnect, $cliente, $proceso), true);
    $conteoBulVac = 0;
    $conteoKilVac = 0;
    foreach ($lotesDetalle as $lote => $deta) {
        $tarjasVaciadas =  json_decode($functions->getTotalTarjasVaciadas($connnect, $cliente, $proceso, $lote));
        if (isset($lotesVaciados[$lote])) {
            if ($lotesVaciados[$lote] == $deta['canBul']) {
                $conteoBulVac += $deta['canBul'];
                $conteoKilVac += $deta['kiloNeto'];
            } else {
                $conteoBulVac += $tarjasVaciadas->canBulVac;
                $conteoKilVac += $tarjasVaciadas->canKilVac;
            }
        }
    }

?>
    <div class="card mt-1 mb-1">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-center">
                <div class="flex-fill">Kilos Vaciados</div>
                <div class="flex-fill" id="totKilVac"><?= number_format($conteoKilVac, 2, ',', '.') ?></div>
                <div style="display:none" id="totKilVacReal"><?= $conteoKilVac ?></div>
                <div class="flex-fill">Bultos Vaciados</div>
                <div class="flex-fill" id="totBulVac"><?= number_format($conteoBulVac, 0, ',', '.') ?></div>
                <div class="flex-fill"><button class="btn btn-primary col-12" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="mostrarDetalle('<?= $cliente ?>', '<?= $proceso ?>')"><span><i class="fa-solid fa-plus"></i></span> <span role="status">Mostrar detalle</span></button></div>
            </div>
        </div>
    </div>
<?php
}
