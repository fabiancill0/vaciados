<?php
include 'model/connections.php';
include 'model/functions.php';
$conn = new Connections();
$functions = new Functions();
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <title>Vaciado Procesos</title>
    <link rel="icon" href="img/favicon.ico" sizes="32x32">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png" type="image/png">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://kit.fontawesome.com/34afac4ad4.js" crossorigin="anonymous"></script>
    <script src="js/main.js?<?= md5(time()) ?>"></script>
    <style>
        .table-container {
            overflow-y: auto;
            width: 100%;
            max-height: 50vh;
            border-radius: 0.25rem;
        }

        .table-container thead tr {
            position: sticky;
            top: 0;
            z-index: 1;
            line-height: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header">
                <a style="position:absolute; z-index: 100" href="http://190.196.68.187/app_hub"><button class="btn btn-primary btn-lg" type="button" id="button-addon1"><i class="fa-solid fa-house"></i></button></a>
                <h3 class="text-center mb-3">Vaciado orden de proceso</h3>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-5 mb-3">
                        <label for="cliente" class="form-label">Cliente</label>
                        <select name="cliente" id="cliente" class="form-select">
                            <?php
                            $functions->getClientesCodOrden($conn->connectToServ());
                            ?>
                        </select>
                    </div>
                    <div class="col-12 col-xl-3">
                        <label for="cliente" class="form-label">Procesos del día (seleccione o digite)</label>
                        <div class="input-group mb-3 dropdown-center">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="ordenes" data-bs-toggle="dropdown" aria-expanded="false">N°</button>
                            <ul class="dropdown-menu" id="list_procesos">
                            </ul>
                            <input type="number" name="proceso" id="proceso" class="form-control">
                        </div>
                    </div>
                    <div class="col-12 col-xl-4 d-flex align-items-middle justify-content-center">
                        <button type="submit" name="search" id="search" class="btn btn-primary col-12">Cargar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="prod"></div>
        <div id="orden" class="table-container"></div>
        <div id="total_proc"></div>
        <div style="height: 70px"></div>
        <div class="modal modal-xl fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" id="deta_proc">
                </div>
            </div>
        </div>

    </div>
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>