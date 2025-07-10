<?php
include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();

$date = new DateTime();

$minutos = range(0, 59);
$segundos = range(0, 59);


$test = [
    318915,
    318916,
    318917,
    318918,
    318919,
    318920,
    318921,
    318922,
    318923,
    318924,
    318925,
    318926,
    318927,
    318928,
    318929,
    318930,
    318931,
    318932,
    318933,
    318934,
    318935,
    318936,
    318937,
    318938,
    318939,
    318940,
    318941,
    318942,
    318943,
    318944,
    318945,
    318946,
    318947,
    318948,
    318949,
    318950,
    318951,
    318952,
    318953,
    318954,
    318955,
    318956,
    318957,
    318958,
    318959,
    318960,
    318961,
    318962,
    318963,
    318964,
    318965,
    318966,
    318967,
    318968,
    318969,
    318970,
    318971,
    318972,
    318973,
    318974,
    318975,
    318976,
    318977,
    318978,
    318913,
    318914

];

$i = 0;

foreach ($test as $tarja) {

    $min = intval($date->format('i'));
    $seg = intval($date->format('s')) + $i;

    if ($seg > 59) {
        $min++;
        $seg -= 60;
        $hora = $date->format('H') . ':' . str_pad($min, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seg, 2, '0', STR_PAD_LEFT);
    } else if ($min > 59) {
        $min = 0;
        $seg -= 60;
        $date->modify('+1 hour');
        $hora = $date->format('H') . ':' . str_pad($min, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seg, 2, '0', STR_PAD_LEFT);
    } else {
        $hora = $date->format('H') . ':' . str_pad($min, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seg, 2, '0', STR_PAD_LEFT);
    }
    $i += 1;
    echo "Tarja: $tarja - Hora: $hora" . "\n";
}


/*
$cliente = $_POST['cliente'];
$lote = $_POST['loteId'];
$proceso = $_POST['proceso'];

$connnect = $conn->connectToServ();
$tarjasXVaciar =  json_decode($functions->getTarjasXVaciar($connnect, $lote, $cliente));
$detalleProceso = json_decode($functions->getProcesoDetalle($connnect, $cliente, $proceso));

if (empty($tarjasXVaciar)) {
    echo json_encode(['error' => 'si', 'message' => 'No hay tarjas para vaciar en este lote.']);
    exit;
} else {
    $query = "INSERT INTO dba.spro_ordenprocvacdeta(plde_codigo, orpr_tipord, orpr_numero, clie_codigo, opve_fecvac, opve_turno, opvd_horava,
opvd_horate, lote_pltcod, lote_espcod, lote_codigo, enva_tipoen, enva_codigo, cale_calida, opvd_canbul, opve_nrtar1, opvd_pesone, opvd_pesobr, opvd_kilpro, opvd_kilori, 
opvd_fereva) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $result = odbc_prepare($connnect, $query);

    $i = 4;
    foreach ($tarjasXVaciar as $tarja) {
    if($i > 59){
        $i = 4;
        $min = $minutos[intval((new DateTime)->format('i')) + 1];
    }
    $hora = (new DateTime)->format('H').':'.$min.':'.$segundos[intval((new DateTime)->format('s')) + $i];
        $i++;
        $params = [
            $detalleProceso->planta,
            $detalleProceso->tipoOrd,
            $detalleProceso->proceso,
            $detalleProceso->cliente,
            date('Y-m-d'),
            $detalleProceso->turno,
            $hora,
            $hora,
            $tarja->pltCod,
            $tarja->especie,
            $tarja->lote,
            $tarja->tipoEnvase,
            $tarja->envaseCodigo,
            $tarja->calidad,
            $tarja->canBul,
            $tarja->nroTarja,
            $tarja->pesoNeto,
            $tarja->pesoBruto,
            $tarja->pesoNeto,
            $tarja->pesoNeto,
            date('Y-m-d')
        ];

        if (!odbc_execute($result, $params)) {
            echo json_encode(['error' => 'si', 'message' => 'Error al vaciar el lote: ' . odbc_errormsg($connnect)]);
            exit;
        }
            $i+=4;
    }

    echo json_encode(['error' => 'no', 'message' => 'Lote vaciado correctamente.']);
}
*/