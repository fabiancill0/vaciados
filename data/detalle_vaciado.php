<?php

include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();

$cliente = $_POST['cliente'];
$proceso = $_POST['proceso'];

$connnect = $conn->connectToServ();
$detalleProceso = json_decode($functions->getProcesoDetalle($connnect, $cliente, $proceso));
$detalleVaciadoT1 =  json_decode($functions->getDetalleVaciado($connnect, $detalleProceso->fecPro, 1));
$detalleVaciadoT2 =  json_decode($functions->getDetalleVaciado($connnect, $detalleProceso->fecPro, 2));
$totalVaciado =  json_decode($functions->getTotalVaciado($connnect, $detalleProceso->fecPro));
$fecha = new DateTime($detalleProceso->fecPro);
if ($detalleVaciadoT1[0]->error and $detalleVaciadoT2[0]->error) {
    echo "<script>alert('Error al recuperar detalle del proceso seleccionado.');</script>";
    exit;
} else if ($detalleVaciadoT2[0]->error) {
?>
    <div class="modal-header">
        <h6 class="text-center">Detalle Proceso <?= $fecha->format('d-m-Y') ?></h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="card mt-1 mb-1">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col">
                        <table class="table table-responsive table-borderless text-center">
                            <thead>
                                <tr>
                                    <th>Kilos Vaciados</th>
                                    <th>Bultos Vaciados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= number_format($totalVaciado[0]->kilos, 2, ',', '.') ?></td>
                                    <td><?= $totalVaciado[0]->bultos ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-1 mb-1">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col">
                        <table class="table table-responsive table-borderless text-center">
                            <thead>
                                <tr class="table-active">
                                    <td colspan="3">Detalle vaciado turno 1</td>
                                </tr>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Kilos</th>
                                    <th>Bultos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($detalleVaciadoT1 as $detalle) {

                                ?>
                                    <tr>
                                        <td><?= $detalle->tramoHora ?></td>
                                        <td><?= number_format($detalle->kilos, 2, ',', '.') ?></td>
                                        <td><?= $detalle->bultos ?></td>
                                    </tr>
                                <?php

                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
} else if ($detalleVaciadoT1[0]->error) {
?>
    <div class="modal-header">
        <h6 class="text-center">Detalle Proceso <?= $fecha->format('d-m-Y') ?></h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="card mt-1 mb-1">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col">
                        <table class="table table-responsive table-borderless text-center">
                            <thead>
                                <tr>
                                    <th>Kilos Vaciados</th>
                                    <th>Bultos Vaciados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= number_format($totalVaciado[0]->kilos, 2, ',', '.') ?></td>
                                    <td><?= $totalVaciado[0]->bultos ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-1 mb-1">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col">
                        <table class="table table-responsive table-borderless text-center">
                            <thead>
                                <tr class="table-active">
                                    <td colspan="3">Detalle vaciado turno 2</td>
                                </tr>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Kilos</th>
                                    <th>Bultos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($detalleVaciadoT2 as $detalle) {

                                ?>
                                    <tr>
                                        <td><?= $detalle->tramoHora ?></td>
                                        <td><?= number_format($detalle->kilos, 2, ',', '.') ?></td>
                                        <td><?= $detalle->bultos ?></td>
                                    </tr>
                                <?php

                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
} else {
?>
    <div class="modal-header">
        <h6 class="text-center">Detalle Proceso <?= $fecha->format('d-m-Y') ?></h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="card mt-1 mb-1">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col">
                        <table class="table table-responsive table-borderless text-center">
                            <thead>
                                <tr>
                                    <th>Kilos Vaciados</th>
                                    <th>Bultos Vaciados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= number_format($totalVaciado[0]->kilos, 2, ',', '.') ?></td>
                                    <td><?= $totalVaciado[0]->bultos ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-1 mb-1">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-6">
                        <table class="table table-responsive table-borderless text-center">
                            <thead>
                                <tr class="table-active">
                                    <td colspan="3">Detalle vaciado turno 1</td>
                                </tr>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Kilos</th>
                                    <th>Bultos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($detalleVaciadoT1 as $detalle) {

                                ?>
                                    <tr>
                                        <td><?= $detalle->tramoHora ?></td>
                                        <td><?= number_format($detalle->kilos, 2, ',', '.') ?></td>
                                        <td><?= $detalle->bultos ?></td>
                                    </tr>
                                <?php

                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6">
                        <table class="table table-responsive table-borderless text-center">
                            <thead>
                                <tr class="table-active">
                                    <td colspan="3">Detalle vaciado turno 2</td>
                                </tr>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Kilos</th>
                                    <th>Bultos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($detalleVaciadoT2 as $detalle) {

                                ?>
                                    <tr>
                                        <td><?= $detalle->tramoHora ?></td>
                                        <td><?= number_format($detalle->kilos, 2, ',', '.') ?></td>
                                        <td><?= $detalle->bultos ?></td>
                                    </tr>
                                <?php

                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
