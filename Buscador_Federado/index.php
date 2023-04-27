<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="stylesIndex.css">

</head>

<body>
    <!-- Barra de busqueda del formulario-->
    <br>
    <h2 style="margin:auto; max-width:400px">Búsqueda distribuida y semántica</h2>
    <br>
    <br>
    <form class="example" method="post" action="index.php" style="margin:auto;max-width:500px;overflow-x:auto;">
        <input type="text" placeholder="Buscar..." name="mySearch" id="mySearch" required>
        <button type="submit" id="btnSearch" name="btnSearch"><i class="fa fa-search" id="btnSearch" name="btnSearch"></i></button>
    </form>

    <?php
    include 'funciones/logica_principal.php';
    ?>

</body>

</html>