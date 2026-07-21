<?php
include 'model/connections.php';
include 'model/functions.php';
$conn = new Connections();
$functions = new Functions();
?>
<!doctype html>
<html data-bs-theme="dark">

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oxanium:wght@200..800&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <script src="js/main.js?<?= md5(time()) ?>"></script>
    <style>
        .table-container {
            overflow-y: auto;
            width: 100%;
            max-height: 50dvh;
            border-radius: 0.25rem;
        }
    </style>
</head>

<body style="font-family: Quicksand, sans-serif;">
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header">
                <a style="position:absolute; z-index: 100" href="http://190.196.68.187/app_hub"><button class="btn btn-primary btn-lg" type="button" id="button-addon1"><i class="bi bi-house"></i></button></a>
                <h3 class="text-center mb-3">Vaciado orden de proceso</h3>
            </div>
            <div class="row g-0">
                <div class="col-xl-6 col-md-5 col-12">
                    <div class="form-floating">
                        <select name="cliente" id="cliente" class="form-select form-select-sm">
                            <?php
                            $functions->getClientesCodOrden($conn->connectToServ());
                            ?>
                        </select>
                        <label for="cliente">Cliente</label>
                    </div>
                </div>
                <div class="col-xl-4 col-md-5 col-8">
                    <div class="input-group">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="ordenes" data-bs-toggle="dropdown" aria-expanded="false">N°</button>
                        <ul class="dropdown-menu" id="list_procesos">
                        </ul>
                        <div class="form-floating">
                            <input type="number" name="proceso" id="proceso" class="form-control form-control-sm">
                            <label id="proceso_label" for="proceso">N° Orden</label>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-2 col-4">
                    <button type="submit" name="search" id="search" class="btn col-12 h-100 btn-primary btn-sm">
                        <span>
                            <i class="bi bi-arrow-clockwise"></i>
                        </span>
                        <span role="status">Cargar</span>
                    </button>
                </div>
            </div>
        </div>
        <div id="total_proc"></div>
        <div id="orden" class="table-container"></div>
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