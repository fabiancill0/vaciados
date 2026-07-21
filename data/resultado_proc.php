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
    $productor =  json_decode($functions->getProductorProceso($connnect, $cliente, $proceso));
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
    <div class="input-group">
        <div class="form-floating">
            <input class="form-control fw-bold" type="text" value="<?= $productor->productor ?>" id="productor" disabled readonly>
            <label for="productor">Productor</label>
        </div>
    </div>
    <div class="input-group">
        <div class="form-floating">
            <input class="form-control fw-bold" type="number" value="<?= $conteoKilVac ?>" id="totKilVac" disabled readonly step="0.01">
            <label for="totKilVac">Kilos Vaciados</label>
        </div>
        <div class="form-floating">
            <input class="form-control fw-bold" type="number" value="<?= $conteoBulVac ?>" id="totBulVac" disabled readonly>
            <label for="totBulVac">Bultos Vaciados</label>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="mostrarDetalle('<?= $cliente ?>', '<?= $proceso ?>')">
            <span>
                <i class="bi bi-clipboard-plus"></i>
            </span>
            <span role="status">Mostrar detalle</span>
        </button>
    </div>
<?php
}
