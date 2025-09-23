<?php
class Functions
{
  function getClientesCod($conn)
  {
    $query = "SELECT clie_codigo, clie_nombre FROM dba.clientesprod ORDER BY clie_codigo";
    $result = odbc_exec($conn, $query);
    while ($row = odbc_fetch_array($result)) {
      $row = array_map("utf8_encode", $row);
      if ($row['clie_codigo'] == 15) {
?>
        <option value="<?= $row['clie_codigo'] ?>"><?= $row['clie_codigo'] . ' - Exportadora BB Trading' ?></option>
      <?php
      } else {
      ?>
        <option value="<?= $row['clie_codigo'] ?>"><?= $row['clie_codigo'] . ' - ' . $row['clie_nombre'] ?></option>
      <?php
      }
    }
  }
  function getClientesCodOrden($conn)
  {
    $query = "SELECT pro.clie_codigo, clie.clie_nombre FROM DBA.spro_ordenproceso AS pro JOIN dba.clientesprod AS clie ON pro.clie_codigo = clie.clie_codigo
WHERE pro.clie_codigo <> 15 GROUP BY pro.clie_codigo, clie.clie_nombre ORDER BY pro.clie_codigo";
    $result = odbc_exec($conn, $query);
    while ($row = odbc_fetch_array($result)) {
      $row = array_map("utf8_encode", $row);
      ?>
      <option value="<?= $row['clie_codigo'] ?>"><?= $row['clie_codigo'] . ' - ' . $row['clie_nombre'] ?></option>
<?php
    }
  }

  function getNombreProductor($conex, $codigo)
  {
    $query = "SELECT prod_nombre FROM DBA.productores WHERE prod_codigo = $codigo";
    $resultQuery = odbc_exec($conex, $query);
    $nombre = odbc_fetch_array($resultQuery);
    return strtoupper($nombre['prod_nombre']);
  }

  function getNombreVariedad($conex, $codigoVari, $codigoEspe)
  {
    $query = "SELECT vari_nombre FROM DBA.variedades WHERE espe_codigo = $codigoEspe AND vari_codigo = $codigoVari";
    $resultQuery = odbc_exec($conex, $query);
    $nombre = odbc_fetch_array($resultQuery);
    return strtoupper($nombre['vari_nombre']);
  }

  function getNombreCliente($conex, $codigo)
  {
    $query = "SELECT clie_nombre FROM DBA.clientesprod WHERE clie_codigo = $codigo";
    $resultQuery = odbc_exec($conex, $query);
    $nombre = odbc_fetch_array($resultQuery);
    return strtoupper($nombre['clie_nombre']);
  }
  function getNombreClienteAbreviado($conex, $codigo)
  {
    $query = "SELECT clie_abrevi FROM DBA.clientesprod WHERE clie_codigo = $codigo";
    $resultQuery = odbc_exec($conex, $query);
    $nombre = odbc_fetch_array($resultQuery);
    return strtoupper($nombre['clie_abrevi']);
  }
  function getNombreEspecie($conex, $codigo)
  {
    $query = "SELECT espe_nombre FROM DBA.especies WHERE espe_codigo = $codigo";
    $resultQuery = odbc_exec($conex, $query);
    $nombre = odbc_fetch_array($resultQuery);
    return strtoupper($nombre['espe_nombre']);
  }
  function getNumeroTraspaso($conex, $cliente, $proceso)
  {
    $query = "SELECT mfge_numero, espe_codigo FROM DBA.spro_movtofrutagranenca where defg_docrel = ? and clie_codigo = ?";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, array($proceso, $cliente));
    if (odbc_num_rows($resultQuery) == 0) {
      return json_encode(['nroTraspaso' => 0]);
    } else {
      return json_encode(['nroTraspaso' => odbc_result($resultQuery, 'mfge_numero'), 'codEspecie' => odbc_result($resultQuery, 'espe_codigo')]);
    }
  }
  function getEstadoProcesoMovimento($conex, $cliente, $proceso)
  {
    $query = "SELECT op.orpr_estado, mov.mfge_estmov FROM DBA.spro_ordenproceso as op join DBA.spro_movtofrutagranenca as mov on op.clie_codigo = mov.clie_codigo
and op.orpr_numero = mov.defg_docrel where op.clie_codigo = ? and op.orpr_numero = ?";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$cliente, $proceso]);
    if (odbc_num_rows($resultQuery) == 0) {
      return json_encode(['estado' => 0]);
    } else {
      if (odbc_result($resultQuery, 'orpr_estado') == 2 and odbc_result($resultQuery, 'mfge_estmov') == 2) {
        return json_encode(['estado' => 'termino']);
      } else if (odbc_result($resultQuery, 'orpr_estado') == 3 and odbc_result($resultQuery, 'mfge_estmov') == 3) {
        return json_encode(['estado' => 'cierre']);
      } else {
        return json_encode(['estado' => 'activo']);
      }
    }
  }
  function getProcesoDetalle($conex, $cliente, $proceso)
  {
    $query = "SELECT plde_codigo, orpr_tipord, orpr_fecpro, orpr_nrotur, line_codigo FROM DBA.spro_ordenproceso where clie_codigo = ? and orpr_numero = ?";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, array($cliente, $proceso));
    if (odbc_num_rows($resultQuery) == 0) {
      return 0;
    } else {
      $row = odbc_fetch_array($resultQuery);
      $info = [
        'planta' => $row['plde_codigo'],
        'tipoOrd' => $row['orpr_tipord'],
        'fecPro' => $row['orpr_fecpro'],
        'linea' => $row['line_codigo'],
        'proceso' => $proceso,
        'cliente' => $cliente,
        'turno' => $row['orpr_nrotur']
      ];
      return json_encode($info);
    }
  }
  function getProductorProceso($conex, $cliente, $proceso)
  {
    $query = "SELECT prod_codigo FROM DBA.spro_movtofrutagranenca where defg_docrel = ? and clie_codigo = ?";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$proceso, $cliente]);
    if (odbc_num_rows($resultQuery) == 0) {
      $detalle = ['error' => true];
      return json_encode($detalle);
    } else {
      $row = odbc_fetch_array($resultQuery);
      $detalle = ['error' => false, 'productor' => mb_convert_encoding($this->getNombreProductor($conex, $row['prod_codigo']), 'UTF-8', 'ISO-8859-1')];
      return json_encode($detalle);
    }
  }
  function getLotesXVaciar($conex, $cliente, $movimiento)
  {
    $query = "SELECT lote_codigo FROM DBA.spro_movtofrutagrandeta where mfge_numero = ? and clie_codigo = ?";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, array($movimiento, $cliente));
    if (odbc_num_rows($resultQuery) == 0) {
      return 0;
    } else {
      while ($row = odbc_fetch_array($resultQuery)) {
        $lotes[] = $row['lote_codigo'];
      }
      return json_encode($lotes);
    }
  }
  function getLotesXVaciarDeta($conex, $especie, $numeroMov)
  {
    $query = "SELECT (fg_enca.lote_kilpro * fg_deta.mfgd_bulent) as mfgd_kgnent, fg_enca.prod_codigo, fg_deta.lote_codigo, fg_deta.mfgd_bulent FROM 
DBA.spro_movtofrutagrandeta as fg_deta join DBA.spro_lotesfrutagranel as fg_enca on fg_deta.lote_codigo = fg_enca.lote_codigo and fg_deta.lote_espcod = fg_enca.lote_espcod where 
fg_deta.lote_espcod = ? and fg_deta.mfge_numero = ? and fg_deta.tpmv_codigo = 21";
    $resultQuery = odbc_prepare($conex, $query);
    $params = [$especie, $numeroMov];
    odbc_execute($resultQuery, $params);
    if (odbc_num_rows($resultQuery) == 0) {
      return 0;
    } else {
      while ($row = odbc_fetch_array($resultQuery)) {
        $info[$row['lote_codigo']] = [
          'codProd' => $row['prod_codigo'],
          'prodNombre' => mb_convert_encoding($this->getNombreProductor($conex, $row['prod_codigo']), 'UTF-8', 'ISO-8859-1'),
          'kiloNeto' => $row['mfgd_kgnent'],
          'canBul' => $row['mfgd_bulent']
        ];
      }
      return json_encode($info);
    }
  }
  function getTarjasXVaciar($conex, $lotes, $cliente)
  {
    $query = "SELECT pesa.lote_pltcod, pesa.lote_espcod, pesa.lote_codigo, bins.enva_tipoen, bins.enva_codigo, bins.cale_calida ,pesa.mfgp_canbul, pesa.fgmb_nrotar,(pesa.mfgp_pesore - bins.enva_pesone) as mfgp_pesone, pesa.mfgp_pesore
FROM DBA.spro_movtofrutagranpesa as pesa join (SELECT enva.enva_pesone, bin.enva_tipoen, bin.enva_codigo, bin.cale_calida, bin.bins_numero from dba.spro_bins as bin join dba.envases as enva on bin.enva_tipoen = enva.enva_tipoen 
and bin.enva_codigo = enva.enva_codigo where bin.clie_codigo = ?) as bins on  pesa.bins_numero = bins.bins_numero left join dba.spro_ordenprocvacdeta as vaci on pesa.fgmb_nrotar = vaci.opve_nrtar1
where pesa.lote_codigo = ? and pesa.clie_codigo = ? and vaci.opve_nrtar1 is null order by pesa.fgmb_nrotar";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$cliente, $lotes, $cliente]);
    if (odbc_num_rows($resultQuery) == 0) {
      return 0;
    } else {
      $tarjas = [];
      while ($row = odbc_fetch_array($resultQuery)) {
        $tarjas[] = [
          'lote' => $row['lote_codigo'],
          'especie' => $row['lote_espcod'],
          'pltCod' => $row['lote_pltcod'],
          'tipoEnvase' => $row['enva_tipoen'],
          'envaseCodigo' => $row['enva_codigo'],
          'calidad' => $row['cale_calida'],
          'canBul' => $row['mfgp_canbul'],
          'nroTarja' => $row['fgmb_nrotar'],
          'pesoNeto' => $row['mfgp_pesone'],
          'pesoBruto' => $row['mfgp_pesore']
        ];
      }
      return json_encode($tarjas);
    }
  }
  function getTarjasHistoricas($conex, $lotes, $cliente)
  {
    $query = "SELECT pesa.lote_pltcod, pesa.lote_espcod, pesa.lote_codigo, bins.enva_tipoen, bins.enva_codigo, bins.cale_calida ,pesa.mfgp_canbul, pesa.fgmb_nrotar,(pesa.mfgp_pesore - bins.enva_pesone) as mfgp_pesone, pesa.mfgp_pesore
FROM DBA.spro_movtofrutagranpesa as pesa join (SELECT enva.enva_pesone, bin.enva_tipoen, bin.enva_codigo, bin.cale_calida, bin.bins_numero from dba.spro_bins as bin join dba.envases as enva on bin.enva_tipoen = enva.enva_tipoen 
and bin.enva_codigo = enva.enva_codigo where bin.clie_codigo = ?) as bins on  pesa.bins_numero = bins.bins_numero left join dba.spro_ordenprocvacdeta as vaci on pesa.fgmb_nrotar = vaci.opve_nrtar1
where pesa.lote_codigo = ? and pesa.clie_codigo = ? order by pesa.fgmb_nrotar";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$cliente, $lotes, $cliente]);
    if (odbc_num_rows($resultQuery) == 0) {
      return 0;
    } else {
      $tarjas = [];
      while ($row = odbc_fetch_array($resultQuery)) {
        $tarjas[] = [
          'lote' => $row['lote_codigo'],
          'especie' => $row['lote_espcod'],
          'pltCod' => $row['lote_pltcod'],
          'tipoEnvase' => $row['enva_tipoen'],
          'envaseCodigo' => $row['enva_codigo'],
          'calidad' => $row['cale_calida'],
          'canBul' => $row['mfgp_canbul'],
          'nroTarja' => $row['fgmb_nrotar'],
          'pesoNeto' => $row['mfgp_pesone'],
          'pesoBruto' => $row['mfgp_pesore']
        ];
      }
      return json_encode($tarjas);
    }
  }
  function getTarjaDetalle($conex, $tarja, $cliente)
  {
    $query = "SELECT pesa.lote_pltcod, pesa.lote_espcod, pesa.lote_codigo, bins.enva_tipoen, bins.enva_codigo, bins.cale_calida ,pesa.mfgp_canbul, pesa.fgmb_nrotar,(pesa.mfgp_pesore - bins.enva_pesone) as mfgp_pesone, pesa.mfgp_pesore
FROM DBA.spro_movtofrutagranpesa as pesa join (SELECT enva.enva_pesone, bin.enva_tipoen, bin.enva_codigo, bin.cale_calida, bin.bins_numero from dba.spro_bins as bin join dba.envases as enva on bin.enva_tipoen = enva.enva_tipoen 
and bin.enva_codigo = enva.enva_codigo where bin.clie_codigo = ?) as bins on  pesa.bins_numero = bins.bins_numero left join dba.spro_ordenprocvacdeta as vaci on pesa.fgmb_nrotar = vaci.opve_nrtar1
where pesa.fgmb_nrotar = ? and pesa.clie_codigo = ? and vaci.opve_nrtar1 is null order by pesa.fgmb_nrotar";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$cliente, $tarja, $cliente]);
    if (odbc_num_rows($resultQuery) == 0) {
      return json_encode(['error' => true]);
    } else {

      $row = odbc_fetch_array($resultQuery);
      $tarjas = [
        'error' => false,
        'lote' => $row['lote_codigo'],
        'especie' => $row['lote_espcod'],
        'pltCod' => $row['lote_pltcod'],
        'tipoEnvase' => $row['enva_tipoen'],
        'envaseCodigo' => $row['enva_codigo'],
        'calidad' => $row['cale_calida'],
        'canBul' => $row['mfgp_canbul'],
        'nroTarja' => $row['fgmb_nrotar'],
        'pesoNeto' => $row['mfgp_pesone'],
        'pesoBruto' => $row['mfgp_pesore']
      ];
      return json_encode($tarjas);
    }
  }
  function getTarjasVaciadas($conex, $cliente, $lote)
  {
    $query = "SELECT opve_nrtar1 from dba.spro_ordenprocvacdeta where clie_codigo = ? and lote_codigo = ?";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$cliente, $lote]);
    if (odbc_num_rows($resultQuery) == 0) {
      return json_encode(['error' => true]);
    } else {
      $tarjas = [];
      while ($row = odbc_fetch_array($resultQuery)) {
        $tarjas[$row['opve_nrtar1']] = 'vaciada';
      }
      return json_encode($tarjas);
    }
  }
  function getTotalTarjasVaciadas($conex, $cliente, $proceso, $lote)
  {
    $query = "SELECT count(opve_nrtar1) as canBulVac, sum(opvd_pesone) as canKilVac from dba.spro_ordenprocvacdeta where clie_codigo = ? and orpr_numero = ? and lote_codigo = ?";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$cliente, $proceso, $lote]);
    if (odbc_num_rows($resultQuery) == 0) {
      return json_encode(['error' => true]);
    } else {
      $tarjas = [];
      $row = odbc_fetch_array($resultQuery);
      $tarjas = ['canBulVac' => $row['canBulVac'], 'canKilVac' => $row['canKilVac']];

      return json_encode($tarjas);
    }
  }
  function getOrdenLotesProceso($conex, $cliente, $orden)
  {
    $query = "SELECT orden.lote_codigo, orden.orpd_secuen FROM DBA.spro_ordenprocdeta as orden 
where orden.clie_codigo = ? and orden.orpr_numero = ? order by orden.orpd_secuen";
    $resultQuery = odbc_prepare($conex, $query);
    $params = [$cliente, $orden];
    odbc_execute($resultQuery, $params);
    if (odbc_num_rows($resultQuery) == 0) {
      return json_encode(['error' => true]);
    } else {
      while ($row = odbc_fetch_array($resultQuery)) {
        $info[] = [
          'error' => false,
          'orden' => $row['orpd_secuen'],
          'lote' => $row['lote_codigo']
        ];
      }
      return json_encode($info);
    }
  }
  function getLotesVaciados($conex, $cliente, $proceso)
  {
    $query = "SELECT lote_codigo, count(opve_nrtar1) as canBul from dba.spro_ordenprocvacdeta where clie_codigo = ? and orpr_numero = ? group by lote_codigo";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$cliente, $proceso]);
    if (odbc_num_rows($resultQuery) == 0) {
      return json_encode(['error' => true]);
    } else {
      $lotes = [];
      while ($row = odbc_fetch_array($resultQuery)) {
        $lotes[$row['lote_codigo']] = $row['canBul'];
      }
      return json_encode($lotes);
    }
  }
  function getDetalleVaciado($conex, $fecha, $turno)
  {
    $query = "SELECT sum(opvd_pesone) as kilos, count(opve_nrtar1) as bultos, hour(opvd_horava) as horas, case 
when horas = 0 then '12 a.m. - 1 a.m.'
when horas = 1 then '1 a.m. - 2 a.m.'
when horas = 2 then '2 a.m. - 3 a.m.'
when horas = 3 then '3 a.m. - 4 a.m.'
when horas = 4 then '4 a.m. - 5 a.m.'
when horas = 5 then '5 a.m. - 6 a.m.'
when horas = 6 then '6 a.m. - 7 a.m.'
when horas = 7 then '7 a.m. - 8 a.m.'
when horas = 8 then '8 a.m. - 9 a.m.'
when horas = 9 then '9 a.m. - 10 a.m.'
when horas = 10 then '10 a.m. - 11 a.m.'
when horas = 11 then '11 a.m. - 12 p.m.'
when horas = 12 then '12 p.m. - 1 p.m. '
when horas = 13 then '1 p.m. - 2 p.m.'
when horas = 14 then '2 p.m. - 3 p.m.'
when horas = 15 then '3 p.m. - 4 p.m.'
when horas = 16 then '4 p.m. - 5 p.m.'
when horas = 17 then '5 p.m. - 6 p.m.'
when horas = 18 then '6 p.m. - 7 p.m.'
when horas = 19 then '7 p.m. - 8 p.m.'
when horas = 20 then '8 p.m. - 9 p.m.'
when horas = 21 then '9 p.m. - 10 p.m.'
when horas = 22 then '10 p.m. - 11 p.m.'
else '11 p.m. - 12 a.m.' end as tramoHora, case 
when horas = 0 then 2
when horas = 1 then 2
when horas = 2 then 2
when horas = 3 then 2
when horas = 4 then 2
when horas = 5 then 2
when horas = 6 then 2
when horas = 7 then 1
when horas = 8 then 1
when horas = 9 then 1
when horas = 10 then 1
when horas = 11 then 1
when horas = 12 then 1
when horas = 13 then 1
when horas = 14 then 1
when horas = 15 then 1
when horas = 16 then 1
when horas = 17 then 1
when horas = 18 then 2
when horas = 19 then 2
when horas = 20 then 2
when horas = 21 then 2
when horas = 22 then 2
else 2 end as turno, case
when horas = 0 then 7
when horas = 1 then 8
when horas = 2 then 9
when horas = 3 then 10
when horas = 4 then 11
when horas = 5 then 12
when horas = 6 then 1
when horas = 7 then 2
when horas = 8 then 3
when horas = 9 then 4
when horas = 10 then 5
when horas = 11 then 6
when horas = 12 then 7
when horas = 13 then 8
when horas = 14 then 9
when horas = 15 then 10
when horas = 16 then 11
when horas = 17 then 12
when horas = 18 then 1
when horas = 19 then 2
when horas = 20 then 3
when horas = 21 then 4
when horas = 22 then 5
else 6 end as secuencia FROM DBA.spro_ordenprocvacdeta where opve_fecvac = ? AND turno = ? group by horas order by secuencia";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$fecha, $turno]);
    if (odbc_num_rows($resultQuery) == 0) {
      $detalle = [];
      $detalle[] = ['error' => true];
      return json_encode($detalle);
    } else {
      $detalle = [];
      while ($row = odbc_fetch_array($resultQuery)) {
        $detalle[] = ['error' => false, 'kilos' => $row['kilos'], 'bultos' => $row['bultos'], 'tramoHora' => $row['tramoHora'], 'turno' => $row['turno']];
      }
      return json_encode($detalle);
    }
  }
  function getTotalVaciado($conex, $fecha)
  {
    $query = "SELECT sum(opvd_pesone) as kilos, count(opve_nrtar1) as bultos FROM DBA.spro_ordenprocvacdeta where opve_fecvac = ?";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, [$fecha]);
    if (odbc_num_rows($resultQuery) == 0) {
      $detalle = [];
      $detalle[] = ['error' => true];
      return json_encode($detalle);
    } else {
      $detalle = [];
      $row = odbc_fetch_array($resultQuery);
      $detalle[] = ['error' => false, 'kilos' => $row['kilos'], 'bultos' => $row['bultos']];
      return json_encode($detalle);
    }
  }
}
