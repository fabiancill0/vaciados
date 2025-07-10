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
?>

    <div class="card" style="position: sticky; top: 0; z-index: 1; background-color: #343a40; color: white;">
        <div class="card-header">
            <div class="row">
                <div class="col-2">
                    <h3 class="text-center">Lote</h3>
                </div>
                <div class="col-2">
                    <h3 class="text-center">Cod Prod</h3>
                </div>
                <div class="col-2">
                    <h3 class="text-center">Productor</h3>
                </div>
                <div class="col-2">
                    <h3 class="text-center">Kilos Netos</h3>
                </div>
                <div class="col-2">
                    <h3 class="text-center">Bultos</h3>
                </div>
            </div>
        </div>
    </div>

    <?php
    $lotesTraspaso = json_decode($functions->getLotesXVaciar($connnect, $cliente, $numeroTraspaso));
    $lotesDetalle = json_decode($functions->getLotesXVaciarDeta($connnect, $lotesTraspaso, $dataTraspaso->codEspecie));
    foreach ($lotesDetalle as $lote) {
    ?>
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-2">
                        <h3 class="text-center"><?= $lote->lote ?></h3>
                    </div>
                    <div class="col-2">
                        <h3 class="text-center"><?= $lote->codProd ?></h3>
                    </div>
                    <div class="col-2">
                        <h5 class="text-center"><?= $lote->prodNombre ?></h5>
                    </div>
                    <div class="col-2">
                        <h3 class="text-center"><?= number_format($lote->kiloNeto, 2, ',', '.') ?></h3>
                    </div>
                    <div class="col-2">
                        <h3 class="text-center"><?= $lote->canBul ?></h3>
                    </div>
                    <div class="col-2 d-flex justify-content-center">
                        <button class="btn btn-success col-8" onclick="vaciarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')">Vaciar</button>
                    </div>
                </div>
            </div>
        </div>

    <?php
    }

    ?>
    <div class="container-fluid bg-dark">
        <div class="col-12 d-flex align-items-middle justify-content-center fixed-bottom mb-3">
            <button type="submit" name="search" class="btn btn-primary col-8">Guardar</button>
        </div>
    </div>
<?php
}
