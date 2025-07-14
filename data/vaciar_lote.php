<?php
include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();

$date = new DateTime();

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
    $queryExist = "SELECT COUNT(*) as count FROM dba.spro_ordenprocvacenca WHERE plde_codigo = ? AND orpr_tipord = ? AND orpr_numero = ? AND clie_codigo = ? AND opve_fecvac = ? AND opve_turno = ?";
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
    if (odbc_fetch_array($resultExist)['count'] == 0) {
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
            echo json_encode(['error' => 'si', 'message' => 'Error al insertar la orden de vaciado: ' . odbc_errormsg($connnect)]);
            exit;
        }
        $query = "INSERT INTO dba.spro_ordenprocvacdeta(plde_codigo, orpr_tipord, orpr_numero, clie_codigo, opve_fecvac, opve_turno, opvd_horava,
opvd_horate, lote_pltcod, lote_espcod, lote_codigo, enva_tipoen, enva_codigo, cale_calida, opvd_canbul, opve_nrtar1, opvd_pesone, opvd_pesobr, opvd_kilpro, opvd_kilori, 
opvd_fereva) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $result = odbc_prepare($connnect, $query);

        foreach ($tarjasXVaciar as $tarja) {
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
                date('Y-m-d')
            ];

            if (!odbc_execute($result, $params)) {
                echo json_encode(['error' => 'si', 'message' => 'Error al vaciar el lote: ' . odbc_errormsg($connnect)]);
                exit;
            }
        }
    } else {
        $query = "INSERT INTO dba.spro_ordenprocvacdeta(plde_codigo, orpr_tipord, orpr_numero, clie_codigo, opve_fecvac, opve_turno, opvd_horava,
opvd_horate, lote_pltcod, lote_espcod, lote_codigo, enva_tipoen, enva_codigo, cale_calida, opvd_canbul, opve_nrtar1, opvd_pesone, opvd_pesobr, opvd_kilpro, opvd_kilori, 
opvd_fereva) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $result = odbc_prepare($connnect, $query);

        foreach ($tarjasXVaciar as $tarja) {
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
                date('Y-m-d')
            ];

            if (!odbc_execute($result, $params)) {
                echo json_encode(['error' => 'si', 'message' => 'Error al vaciar el lote: ' . odbc_errormsg($connnect)]);
                exit;
            }
        }
    }
    echo json_encode(['error' => 'no', 'message' => 'Lote vaciado correctamente.']);
}
