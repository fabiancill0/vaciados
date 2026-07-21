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
?>
    <div class="row g-0 sticky-top text-bg-secondary" style="top: 0; z-index: 1040">
        <div class="col-2 text-center">
            <span class="m-0">Lote</span>
        </div>
        <div class="col-2 text-center">
            <span class="m-0">Kilos</span>
        </div>
        <div class="col-1 text-center">
            <span class="m-0">Bultos</span>
        </div>
        <div class="col-1 text-center">
            <span class="m-0">Vaciados</span>
        </div>
        <div class="col-6 text-center">
            <span class="m-0">Acciones</span>
        </div>
    </div>
    <?php
    $lotesDetalle = json_decode($functions->getLotesXVaciarDeta($connnect, $dataTraspaso->codEspecie, $numeroTraspaso), true);
    $lotesVaciados = json_decode($functions->getLotesVaciados($connnect, $cliente, $proceso), true);
    $lotesOrden = json_decode($functions->getOrdenLotesProceso($connnect, $cliente, $proceso));
    $conteoBulVac = 0;
    $conteoKilVac = 0;
    foreach ($lotesOrden as $lote) {
        $tarjasVaciadas =  json_decode($functions->getTotalTarjasVaciadas($connnect, $cliente, $proceso, $lote->lote));
        if (isset($lotesVaciados[$lote->lote])) {
            if ($lotesVaciados[$lote->lote] == $lotesDetalle[$lote->lote]['canBul']) {
    ?>
                <div id="<?= $lote->lote ?>_row" class="row g-0" style="top: 24px; z-index: 1020">
                    <div class="col-2"><span class="input-group-text rounded-0 justify-content-center"><?= $lote->lote ?></span></div>
                    <div class="col-2"><span class="input-group-text rounded-0 justify-content-center"><?= number_format($lotesDetalle[$lote->lote]['kiloNeto'], 2, ',', '.') ?></span></div>
                    <div class="col-1"><span class="input-group-text rounded-0 justify-content-center"><?= number_format($lotesDetalle[$lote->lote]['canBul'], 0, '', '.') ?></span></div>
                    <div class="col-1"><input type="number" value="<?= intval($lotesDetalle[$lote->lote]['canBul']) ?>" class="form-control rounded-0 text-center" width="50px" id="<?= $lote->lote ?>_row_canBul" disabled readonly></div>
                    <div class="col-3"><button id="<?= $lote->lote ?>_deta" class="btn rounded-0 border-black col-12 btn-sm h-100 btn-warning" onclick="desplegarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')" disabled><i class="bi bi-check2-square"></i> Vaciado</button></div>
                    <div class="col-3"><button id="<?= $lote->lote ?>" class="btn rounded-0 border-black col-12 btn-sm h-100 btn-success" onclick="vaciarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')" disabled><i class="bi bi-check2-square"></i> Vaciado</button></div>
                </div>
                <div id="<?= $lote->lote ?>_row_deta" class="row g-0"></div>
            <?php
            } else {
            ?>
                <div id="<?= $lote->lote ?>_row" class="row g-0" style="top: 24px; z-index: 1020">
                    <div class="col-2"><span class="input-group-text rounded-0 justify-content-center"><?= $lote->lote ?></span></div>
                    <div class="col-2"><span class="input-group-text rounded-0 justify-content-center"><?= number_format($lotesDetalle[$lote->lote]['kiloNeto'], 2, ',', '.') ?></span></div>
                    <div class="col-1"><span class="input-group-text rounded-0 justify-content-center"><?= number_format($lotesDetalle[$lote->lote]['canBul'], 0, '', '.') ?></span></div>
                    <div class="col-1"><input type="number" value="<?= intval($tarjasVaciadas->canBulVac) ?>" class="form-control rounded-0 text-center" width="50px" id="<?= $lote->lote ?>_row_canBul" disabled readonly></div>
                    <div class="col-3"><button id="<?= $lote->lote ?>_deta" class="btn rounded-0 border-black col-12 btn-sm h-100 btn-warning" onclick="desplegarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')"><i class="bi bi-plus-square"></i> Tarjas</button></div>
                    <div class="col-3"><button id="<?= $lote->lote ?>" class="btn rounded-0 border-black col-12 btn-sm h-100 btn-success" onclick="vaciarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')"><i class="bi bi-plus-square"></i> Vaciar</button></div>
                </div>
                <div id="<?= $lote->lote ?>_row_deta" class="row g-0"></div>
            <?php
            }
        } else if (!isset($lotesDetalle[$lote->lote])) {
            ?>
            <div class="row g-0">
                <div class="col-12">
                    <div class="alert alert-danger text-center" role="alert">
                        <i class="bi bi-exclamation-diamond"></i>
                        <strong>Importante!</strong> Lote <strong><?= $lote->lote ?></strong> no se encuentra dentro del traspaso.
                        <i class="bi bi-exclamation-diamond"></i>
                    </div>
                </div>
            </div>

        <?php
        } else {
        ?>
            <div id="<?= $lote->lote ?>_row" class="row g-0" style="top: 24px; z-index: 1020">
                <div class="col-2"><span class="input-group-text rounded-0 justify-content-center"><?= $lote->lote ?></span></div>
                <div class="col-2"><span class="input-group-text rounded-0 justify-content-center"><?= number_format($lotesDetalle[$lote->lote]['kiloNeto'], 2, ',', '.') ?></span></div>
                <div class="col-1"><span class="input-group-text rounded-0 justify-content-center"><?= number_format($lotesDetalle[$lote->lote]['canBul'], 0, '', '.') ?></span></div>
                <div class="col-1"><input type="number" value="<?= intval($tarjasVaciadas->canBulVac) ?>" class="form-control rounded-0 text-center" width="50px" id="<?= $lote->lote ?>_row_canBul" disabled readonly></div>
                <div class="col-3"><button id="<?= $lote->lote ?>_deta" class="btn rounded-0 border-black col-12 btn-sm h-100 btn-warning" onclick="desplegarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')"><i class="bi bi-plus-square"></i> Tarjas</button></div>
                <div class="col-3"><button id="<?= $lote->lote ?>" class="btn rounded-0 border-black col-12 btn-sm h-100 btn-success" onclick="vaciarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')"><i class="bi bi-plus-square"></i> Vaciar</button></div>
            </div>
            <div id="<?= $lote->lote ?>_row_deta" class="row g-0"></div>

    <?php
        }
    }
    ?>
    <div class="container-fluid">
        <div class="col-12 d-flex align-items-middle justify-content-center fixed-bottom">
            <button onclick="eliminarVaciado()" class="btn btn-danger col-12"><i class="bi bi-trash"></i> Eliminar Vaciado</button>
        </div>
    </div>
<?php
}
