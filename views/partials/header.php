<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); // p.ej. /pet_happy_mvc
function navActive($c){ return (strtolower($_GET['c'] ?? 'dashboard') === $c) ? 'active' : ''; }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Pet Happy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 desde CDN para evitar 404 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark navbar-expand-lg bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?=$base?>/index.php?c=dashboard&a=index">Pet Happy</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link <?=navActive('dashboard')?>" href="<?=$base?>/index.php?c=dashboard&a=index">Inicio</a></li>
        <li class="nav-item"><a class="nav-link <?=navActive('pedido')?>"    href="<?=$base?>/index.php?c=pedido&a=index">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link <?=navActive('cliente')?>"   href="<?=$base?>/index.php?c=cliente&a=index">Clientes</a></li>
        <li class="nav-item"><a class="nav-link <?=navActive('producto')?>"  href="<?=$base?>/index.php?c=producto&a=index">Productos</a></li>
        <li class="nav-item"><a class="nav-link <?=navActive('despacho')?>"  href="<?=$base?>/index.php?c=despacho&a=index">Despacho</a></li>
        <li class="nav-item"><a class="nav-link <?=navActive('reportes')?>"  href="<?=$base?>/index.php?c=reportes&a=index">Reportes</a></li>
        <li class="nav-item"><a class="nav-link <?=navActive('usuarios')?>"  href="<?=$base?>/index.php?c=usuarios&a=index">Usuarios</a></li>
      </ul>
    </div>
  </div>
</nav>
