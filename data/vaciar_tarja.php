<?php
include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();

$date = new DateTime();

$cliente = $_POST['cliente'];
$proceso = $_POST['proceso'];
$tarja = $_POST['tarjaId'];
// Para testear se puede seleccionar variables directamente
//$cliente = 49;
//$proceso = 75;
//$tarja = 8145;

if ($cliente == 15) {
    $connnect = $conn->connectToRK();
} else {
    $connnect = $conn->connectToServ();
}
$detalleTarja = json_decode($functions->getTarjaDetalle($connnect, $tarja, $cliente));
$detalleProceso = json_decode($functions->getProcesoDetalle($connnect, $cliente, $proceso));
$estadoProceso = json_decode($functions->getEstadoProcesoMovimento($connnect, $cliente, $proceso));
$pesosEnvases = json_decode($functions->getPesoEnvasesTarja($connnect, $cliente, $tarja), true);
if ($estadoProceso->estado == 0) {
    echo json_encode(['error' => 'si', 'error_type' => 4, 'message' => 'Orden de proceso no encontrada.']);
    exit;
} else if ($estadoProceso->estado == 'termino') {
    echo json_encode(['error' => 'si', 'error_type' => 5, 'message' => 'Termino de proceso realizado, solicite a tarjado comercial anular el término.']);
    exit;
} else if ($estadoProceso->estado == 'cierre') {
    echo json_encode(['error' => 'si', 'error_type' => 6, 'message' => 'Proceso cerrado, informe a sistemas.']);
    exit;
} else {
    if ($detalleTarja->error) {
        echo json_encode(['error' => 'si', 'error_type' => 1, 'message' => 'Tarja vaciada anteriormente']);
        exit;
    } else {
        $queryExist = "SELECT COUNT(1) FROM dba.spro_ordenprocvacenca WHERE plde_codigo = ? AND orpr_tipord = ? AND orpr_numero = ? AND clie_codigo = ? AND opve_fecvac = ? AND opve_turno = ?";
        $resultExist = odbc_prepare($connnect, $queryExist);
        $paramsExist = [
            $detalleProceso->planta,
            $detalleProceso->tipoOrd,
            $detalleProceso->proceso,
            $detalleProceso->cliente,
            $detalleProceso->fecPro,
            $detalleProceso->turno
        ];
        odbc_execute($resultExist, $paramsExist);
        if (odbc_result($resultExist, 1) == 0) {
            $queryEnca = "INSERT INTO dba.spro_ordenprocvacenca(plde_codigo, orpr_tipord, orpr_numero, clie_codigo, opve_fecvac, opve_turno, opve_estado, line_codigo) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $resultEnca = odbc_prepare($connnect, $queryEnca);
            $paramsEnca = [
                $detalleProceso->planta,
                $detalleProceso->tipoOrd,
                $detalleProceso->proceso,
                $detalleProceso->cliente,
                $detalleProceso->fecPro,
                $detalleProceso->turno,
                'V',
                $detalleProceso->linea
            ];
            if (!odbc_execute($resultEnca, $paramsEnca)) {
                echo json_encode(['error' => 'si', 'error_type' => 2, 'message' => 'Error al insertar la orden de vaciado: ' . odbc_errormsg($connnect)]);
                exit;
            }
            $query = "INSERT INTO dba.spro_ordenprocvacdeta(plde_codigo, orpr_tipord, orpr_numero, clie_codigo, opve_fecvac, opve_turno, opvd_horava,
opvd_horate, lote_pltcod, lote_espcod, lote_codigo, enva_tipoen, enva_codigo, cale_calida, opvd_canbul, opve_nrtar1, opvd_pesone, opvd_pesobr, opvd_kilpro, opvd_kilori, 
opvd_fereva) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $result = odbc_prepare($connnect, $query);
            $pesoBin = count($pesosEnvases) > 1 ? array_pop($pesosEnvases) : 0;
            $pesoNeto = $detalleTarja->pesoNeto;
            $pesoProm = $pesoNeto / $detalleTarja->canBul;
            $bultosTarja = $detalleTarja->canBul;
            $params = [
                $detalleProceso->planta,
                $detalleProceso->tipoOrd,
                $detalleProceso->proceso,
                $detalleProceso->cliente,
                $detalleProceso->fecPro,
                $detalleProceso->turno,
                $date->format('H:i:s'),
                $date->format('H:i:s'),
                $detalleTarja->pltCod,
                $detalleTarja->especie,
                $detalleTarja->lote,
                $detalleTarja->tipoEnvase,
                $detalleTarja->envaseCodigo,
                $detalleTarja->calidad,
                $detalleTarja->canBul,
                $detalleTarja->nroTarja,
                $pesoNeto,
                $detalleTarja->pesoBruto,
                $pesoProm,
                $detalleTarja->pesoNeto,
                $detalleProceso->fecPro
            ];

            if (!odbc_execute($result, $params)) {
                echo json_encode(['error' => 'si', 'error_type' => 3, 'message' => 'Error al vaciar tarja: ' . odbc_errormsg($connnect)]);
                exit;
            }
        } else {
            $query = "INSERT INTO dba.spro_ordenprocvacdeta(plde_codigo, orpr_tipord, orpr_numero, clie_codigo, opve_fecvac, opve_turno, opvd_horava,
opvd_horate, lote_pltcod, lote_espcod, lote_codigo, enva_tipoen, enva_codigo, cale_calida, opvd_canbul, opve_nrtar1, opvd_pesone, opvd_pesobr, opvd_kilpro, opvd_kilori, 
opvd_fereva) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $result = odbc_prepare($connnect, $query);
            $pesoBin = count($pesosEnvases) > 1 ? array_pop($pesosEnvases) : 0;
            $pesoNeto = $detalleTarja->pesoNeto;
            $pesoProm = $pesoNeto / $detalleTarja->canBul;
            $bultosTarja = $detalleTarja->canBul;
            $params = [
                $detalleProceso->planta,
                $detalleProceso->tipoOrd,
                $detalleProceso->proceso,
                $detalleProceso->cliente,
                $detalleProceso->fecPro,
                $detalleProceso->turno,
                $date->format('H:i:s'),
                $date->format('H:i:s'),
                $detalleTarja->pltCod,
                $detalleTarja->especie,
                $detalleTarja->lote,
                $detalleTarja->tipoEnvase,
                $detalleTarja->envaseCodigo,
                $detalleTarja->calidad,
                $detalleTarja->canBul,
                $detalleTarja->nroTarja,
                $pesoNeto,
                $detalleTarja->pesoBruto,
                $pesoProm,
                $detalleTarja->pesoNeto,
                $detalleProceso->fecPro
            ];

            if (!odbc_execute($result, $params)) {
                echo json_encode(['error' => 'si', 'error_type' => 3, 'message' => 'Error al vaciar tarja: ' . odbc_errormsg($connnect)]);
                exit;
            }
        }
        echo json_encode(['error' => 'no', 'message' => 'Tarja vaciada correctamente.', 'pesoVac' => $pesoNeto, 'bulVac' => $bultosTarja]);
    }
}
