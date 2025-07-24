<?php

class Connections
{
  private $dbServ = "ProdServFtgo_2024";
  private $dbRK = "ProdFrutango_2024";
  private $username = "fcarrasco";
  private $pass = "jygtsygp";

  public function __construct() {}

  public function connectTo($conn)
  {
    $connection = odbc_connect($conn, $this->username, $this->pass);
    return $connection;
  }

  public function connectToServ()
  {
    $connection = odbc_connect($this->dbServ, $this->username, $this->pass);
    return $connection;
  }
  public function connectToRK()
  {
    $connection = odbc_connect($this->dbRK, $this->username, $this->pass);
    return $connection;
  }
}
