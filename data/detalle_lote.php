<?php

include '../model/connections.php';
include '../model/functions.php';

$conn = new Connections();
$functions = new Functions();


$lote = $_POST['loteId'];
$cliente = $_POST['cliente'];
$proceso = $_POST['proceso'];

if ($cliente == 15) {
    $connnect = $conn->connectToRK();
} else {
    $connnect = $conn->connectToServ();
}
$dataTraspaso = json_decode($functions->getNumeroTraspaso($connnect, $cliente, $proceso));
$tarjasXVaciar =  json_decode($functions->getTarjasHistoricas($connnect, $lote, $cliente));
$tarjasVaciadas =  json_decode($functions->getTarjasVaciadas($connnect, $cliente, $lote), true);
$pesosEnvases = json_decode($functions->getPesoEnvasesLote($connnect, $cliente, $lote), true);
if ($tarjasXVaciar == 0) {
    echo json_encode(['error' => 'si', 'message' => 'No hay tarjas para vaciar en este lote.']);
    exit;
} else {
    $tarjas = [];
    $pesoBin = count($pesosEnvases) > 1 ? array_pop($pesosEnvases) : 0;
    if ($dataTraspaso->codEspecie != 21) {
        foreach ($tarjasXVaciar as $tarja) {
            if (isset($tarjasVaciadas[$tarja->nroTarja])) {
                $tarjas[] = [
                    'nroTarja' => $tarja->nroTarja,
                    'pesoNeto' => number_format($tarja->pesoNeto, 2, ',', '.'),
                    'canBul' => number_format($tarja->canBul, 0),
                    'estado' => $tarjasVaciadas[$tarja->nroTarja]
                ];
            } else {
                $tarjas[] = [
                    'nroTarja' => $tarja->nroTarja,
                    'pesoNeto' => number_format($tarja->pesoNeto, 2, ',', '.'),
                    'canBul' => number_format($tarja->canBul, 0),
                    'estado' => 'disponible'
                ];
            }
        }
    } else {
        foreach ($tarjasXVaciar as $tarja) {
            if (isset($tarjasVaciadas[$tarja->nroTarja])) {
                $tarjas[] = [
                    'nroTarja' => $tarja->nroTarja,
                    'pesoNeto' => number_format($tarja->pesoNeto - $pesoBin, 2, ',', '.'),
                    'canBul' => number_format($tarja->canBul, 0),
                    'estado' => $tarjasVaciadas[$tarja->nroTarja]
                ];
            } else {
                $tarjas[] = [
                    'nroTarja' => $tarja->nroTarja,
                    'pesoNeto' => number_format($tarja->pesoNeto - $pesoBin, 2, ',', '.'),
                    'canBul' => number_format($tarja->canBul, 0),
                    'estado' => 'disponible'
                ];
            }
        }
    }

    echo json_encode($tarjas);
}
