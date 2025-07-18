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

    <table class="table table-responsive-sm table-sm table-borderless text-center">
        <thead>
            <tr class="table-active">
                <th>Lote</th>
                <th>Productor</th>
                <th>Kilos</th>
                <th>Bultos</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $lotesDetalle = json_decode($functions->getLotesXVaciarDeta($connnect, $dataTraspaso->codEspecie, $numeroTraspaso));
            foreach ($lotesDetalle as $lote) {
            ?>

                <tr>
                    <td><?= $lote->lote ?></td>
                    <td><?= $lote->prodNombre ?></td>
                    <td><?= number_format($lote->kiloNeto, 2, ',', '.') ?></td>
                    <td><?= $lote->canBul ?></td>
                    <td><button id="<?= $lote->lote ?>" class="btn btn-success" onclick="vaciarLote('<?= $lote->lote ?>', '<?= $cliente ?>', '<?= $proceso ?>')"><i class="fa-solid fa-caret-up fa-flip-vertical"></i> Vaciar</button></td>
                </tr>

            <?php
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
