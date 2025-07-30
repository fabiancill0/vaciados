<?php
include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();

$date = new DateTime();

$cliente = $_POST['cliente'];
$proceso = $_POST['proceso'];
$lote = $_POST['loteId'];
// Para testear se puede seleccionar variables directamente
//$cliente = 49;
//$proceso = 75;
//$lote = 8145;

$connnect = $conn->connectToServ();
$tarjasXVaciar =  json_decode($functions->getTarjasXVaciar($connnect, $lote, $cliente));
$detalleProceso = json_decode($functions->getProcesoDetalle($connnect, $cliente, $proceso));
$estadoProceso = json_decode($functions->getEstadoProcesoMovimento($connnect, $cliente, $proceso));
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
    if (empty($tarjasXVaciar)) {
        echo json_encode(['error' => 'si', 'error_type' => 1, 'message' => 'Lote vaciado.']);
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
            $bultosLote = 0;
            foreach ($tarjasXVaciar as $tarja) {
                $pesoLote += $tarja->pesoNeto;
                $bultosLote += $tarja->canBul;
                $date->modify('+4 seconds');
                $params = [
                    $detalleProceso->planta,
                    $detalleProceso->tipoOrd,
                    $detalleProceso->proceso,
                    $detalleProceso->cliente,
                    $detalleProceso->fecPro,
                    $detalleProceso->turno,
                    $date->format('H:i:s'),
                    $date->format('H:i:s'),
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
                    $detalleProceso->fecPro
                ];

                if (!odbc_execute($result, $params)) {
                    echo json_encode(['error' => 'si', 'error_type' => 3, 'message' => 'Error al vaciar el lote: ' . odbc_errormsg($connnect)]);
                    exit;
                }
            }
        } else {
            $query = "INSERT INTO dba.spro_ordenprocvacdeta(plde_codigo, orpr_tipord, orpr_numero, clie_codigo, opve_fecvac, opve_turno, opvd_horava,
opvd_horate, lote_pltcod, lote_espcod, lote_codigo, enva_tipoen, enva_codigo, cale_calida, opvd_canbul, opve_nrtar1, opvd_pesone, opvd_pesobr, opvd_kilpro, opvd_kilori, 
opvd_fereva) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $result = odbc_prepare($connnect, $query);
            $pesoLote = 0;
            $bultosLote = 0;
            foreach ($tarjasXVaciar as $tarja) {
                $pesoLote += $tarja->pesoNeto;
                $bultosLote += $tarja->canBul;
                $date->modify('+4 seconds');
                $params = [
                    $detalleProceso->planta,
                    $detalleProceso->tipoOrd,
                    $detalleProceso->proceso,
                    $detalleProceso->cliente,
                    $detalleProceso->fecPro,
                    $detalleProceso->turno,
                    $date->format('H:i:s'),
                    $date->format('H:i:s'),
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
                    $detalleProceso->fecPro
                ];

                if (!odbc_execute($result, $params)) {
                    echo json_encode(['error' => 'si', 'error_type' => 3, 'message' => 'Error al vaciar el lote: ' . odbc_errormsg($connnect)]);
                    exit;
                }
            }
        }
        echo json_encode(['error' => 'no', 'message' => 'Lote vaciado correctamente.', 'pesoLote' => $pesoLote, 'bultosLote' => $bultosLote]);
    }
}
