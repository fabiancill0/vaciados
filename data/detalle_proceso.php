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

    <table class="table table-responsive table-borderless text-center">
        <thead>
            <tr class="table-active">
                <th>Lote</th>
                <th>Kilos</th>
                <th>Bultos</th>
                <th>Bultos Vaciados</th>
                <th colspan="2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $lotesDetalle = json_decode($functions->getLotesXVaciarDeta($connnect, $dataTraspaso->codEspecie, $numeroTraspaso));
            $lotesVaciados = json_decode($functions->getLotesVaciados($connnect, $cliente, $proceso), true);
            $conteoBulVac = 0;
            $conteoKilVac = 0;
            foreach ($lotesDetalle as $lote) {
                $tarjasVaciadas =  json_decode($functions->getTotalTarjasVaciadas($connnect, $cliente, $proceso, $lote->lote));
                if (isset($lotesVaciados[$lote->lote])) {
                    if ($lotesVaciados[$lote->lote] == $lote->canBul) {
            ?>

                        <tr id="<?= $lote->lote ?>_row">
                            <td><?= $lote->lote ?></td>
                            <td><?= number_format($lote->kiloNeto, 2, ',', '.') ?></td>
                            <td><?= $lote->canBul ?></td>
                            <td id="<?= $lote->lote ?>_row_canBul"><?= $lote->canBul ?></td>
                            <td><button id="<?= $lote->lote ?>_deta" class="btn btn-warning" onclick="desplegarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')" disabled><i class="fa-solid fa-check"></i> Vaciado</button></td>
                            <td><button id="<?= $lote->lote ?>" class="btn btn-success" onclick="vaciarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')" disabled><i class="fa-solid fa-check"></i> Vaciado</button></td>
                        </tr>

                    <?php
                    } else {
                    ?>

                        <tr id="<?= $lote->lote ?>_row">
                            <td><?= $lote->lote ?></td>
                            <td><?= number_format($lote->kiloNeto, 2, ',', '.') ?></td>
                            <td><?= $lote->canBul ?></td>
                            <td id="<?= $lote->lote ?>_row_canBul"><?= $tarjasVaciadas->canBulVac ?></td>
                            <td><button id="<?= $lote->lote ?>_deta" class="btn btn-warning" onclick="desplegarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')"><i class="fa-solid fa-caret-up fa-flip-vertical"></i> Tarjas</button></td>
                            <td><button id="<?= $lote->lote ?>" class="btn btn-success" onclick="vaciarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')"><i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar</button></td>
                        </tr>

                    <?php
                    }
                } else {
                    ?>

                    <tr id="<?= $lote->lote ?>_row">
                        <td><?= $lote->lote ?></td>
                        <td><?= number_format($lote->kiloNeto, 2, ',', '.') ?></td>
                        <td><?= $lote->canBul ?></td>
                        <td id="<?= $lote->lote ?>_row_canBul"><?= $tarjasVaciadas->canBulVac ?></td>
                        <td><button id="<?= $lote->lote ?>_deta" class="btn btn-warning" onclick="desplegarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')"><i class="fa-solid fa-caret-up fa-flip-vertical"></i> Tarjas</button></td>
                        <td><button id="<?= $lote->lote ?>" class="btn btn-success" onclick="vaciarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')"><i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar</button></td>
                    </tr>

            <?php
                }
            }

            ?>
        </tbody>
    </table>
    <br>
    <div class="container-fluid">
        <div class="col-12 d-flex align-items-middle justify-content-center fixed-bottom">
            <button onclick="eliminarVaciado()" class="btn btn-danger col-12"><i class="fa-solid fa-trash-can"></i> Eliminar Vaciado</button>
        </div>
    </div>
<?php
}
