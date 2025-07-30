<?php

include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();


$cliente = $_POST['cliente'];
$proceso = $_POST['proceso'];

$connnect = $conn->connectToServ();
$dataTraspaso = json_decode($functions->getNumeroTraspaso($connnect, $cliente, $proceso));
$numeroTraspaso = $dataTraspaso->nroTraspaso;
if ($numeroTraspaso == 0) {
    echo "<script>alert('No se encontró el número de traspaso para el cliente y proceso especificados.');</script>";
    exit;
} else {

    $lotesDetalle = json_decode($functions->getLotesXVaciarDeta($connnect, $dataTraspaso->codEspecie, $numeroTraspaso));
    $lotesVaciados = json_decode($functions->getLotesVaciados($connnect, $cliente, $proceso), true);
    $conteoBulVac = 0;
    $conteoKilVac = 0;
    foreach ($lotesDetalle as $lote) {
        $tarjasVaciadas =  json_decode($functions->getTotalTarjasVaciadas($connnect, $cliente, $proceso, $lote->lote));
        if (isset($lotesVaciados[$lote->lote])) {
            if ($lotesVaciados[$lote->lote] == $lote->canBul) {
                $conteoBulVac += $lote->canBul;
                $conteoKilVac += $lote->kiloNeto;
            } else {
                $conteoBulVac += $tarjasVaciadas->canBulVac;
                $conteoKilVac += $tarjasVaciadas->canKilVac;
            }
        }
    }

?>
    <div class="card mt-1 mb-1">
        <div class="card-header">
            <div class="row jutify-content-center">
                <h6 class="col text-center">Kilos Vaciados</h6>
                <h6 class="col text-center" id="totKilVac"><?= number_format($conteoKilVac, 2, ',', '.') ?></h6>
                <h6 style="display:none" id="totKilVacReal"><?= $conteoKilVac ?></h6>
                <h6 class="col text-center">Bultos Vaciados</h6>
                <h6 class="col text-center" id="totBulVac"><?= $conteoBulVac ?></h6>
            </div>

        </div>
    </div>
<?php
}
