<?php
$user = $_SESSION['user'] ?? null;
$isAdmin = isset($user['rol']) && $user['rol'] === 'admin';
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom mb-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">🐾 Pet Happy</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if ($user): ?>
          <li class="nav-item"><a class="nav-link" href="index.php?c=cliente">Clientes</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=producto">Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=pedido">Pedidos</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=kardex">Kardex</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?c=transferencia">Transferencias</a></li>

          <?php if ($isAdmin): ?>
          <!-- Menú solo para ADMIN -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Configuración</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="index.php?c=usuario">Usuarios</a></li>
              <!-- Agrega más pantallas de configuración aquí -->
            </ul>
          </li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav">
        <?php if ($user): ?>
          <li class="nav-item me-3">
            <span class="navbar-text">👋 <?= View::e($user['nombres'] ?? $user['usuario']) ?><?= $isAdmin ? ' • ADMIN' : '' ?></span>
          </li>
          <li class="nav-item"><a class="btn btn-outline-danger btn-sm" href="index.php?c=auth&a=logout">Salir</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-primary btn-sm" href="index.php?c=auth&a=login">Ingresar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success flash"><?= View::e($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger flash"><?= View::e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
  <?php endif; ?>
</div>
