<?php
class Functions
{
  public function getClientesCod($conn)
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
  function getProcesoDetalle($conex, $cliente, $proceso)
  {
    $query = "SELECT plde_codigo, orpr_tipord, orpr_nrotur FROM DBA.spro_ordenproceso where clie_codigo = ? and orpr_numero = ?";
    $resultQuery = odbc_prepare($conex, $query);
    odbc_execute($resultQuery, array($cliente, $proceso));
    if (odbc_num_rows($resultQuery) == 0) {
      return 0;
    } else {
      $row = odbc_fetch_array($resultQuery);
      $info = [
        'planta' => $row['plde_codigo'],
        'tipoOrd' => $row['orpr_tipord'],
        'proceso' => $proceso,
        'cliente' => $cliente,
        'turno' => $row['orpr_nrotur']
      ];
      return json_encode($info);
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
  function getLotesXVaciarDeta($conex, $lotes, $especie)
  {
    $query = "SELECT fg_deta.lotd_totnet, fg_enca.prod_codigo, proc_deta.lote_codigo, proc_deta.orpd_canbul FROM 
DBA.spro_lotesfrutagrandeta as fg_deta join dba.spro_ordenprocdeta as proc_deta on fg_deta.lote_codigo = proc_deta.lote_codigo and fg_deta.lote_espcod = proc_deta.lote_espcod
join DBA.spro_lotesfrutagranel as fg_enca on fg_deta.lote_codigo = fg_enca.lote_codigo and fg_deta.lote_espcod = fg_enca.lote_espcod where
 proc_deta.lote_codigo IN (" . implode(',', array_fill(0, count($lotes), '?')) . ") AND fg_deta.lote_espcod = ?";
    $resultQuery = odbc_prepare($conex, $query);
    $params = array_merge($lotes, [$especie]);
    odbc_execute($resultQuery, $params);
    if (odbc_num_rows($resultQuery) == 0) {
      return 0;
    } else {
      while ($row = odbc_fetch_array($resultQuery)) {
        $info[] = [
          'lote' => $row['lote_codigo'],
          'codProd' => $row['prod_codigo'],
          'prodNombre' => $this->getNombreProductor($conex, $row['prod_codigo']),
          'kiloNeto' => $row['lotd_totnet'],
          'canBul' => $row['orpd_canbul']
        ];
      }
      return json_encode($info);
    }
  }
  function getTarjasXVaciar($conex, $lotes, $cliente)
  {
    $query = "SELECT pesa.lote_pltcod, pesa.lote_espcod, pesa.lote_codigo, bins.enva_tipoen, bins.enva_codigo, bins.cale_calida ,pesa.mfgp_canbul, pesa.fgmb_nrotar,(pesa.mfgp_pesore - bins.enva_pesone) as mfgp_pesone, pesa.mfgp_pesore
FROM DBA.spro_movtofrutagranpesa as pesa join (SELECT enva.enva_pesone, bin.enva_tipoen, bin.enva_codigo, bin.cale_calida, bin.bins_numero from dba.spro_bins as bin join dba.envases as enva on bin.enva_tipoen = enva.enva_tipoen 
and bin.enva_codigo = enva.enva_codigo where bin.clie_codigo = ?) as bins on  pesa.bins_numero = bins.bins_numero where pesa.lote_codigo = ? and pesa.clie_codigo = ?";
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
}
