<?php require __DIR__.'/../partials/header.php'; ?>
<div class="container py-4">
  <h3>Bienvenido, Administrador Principal</h3>
  <div class="row mt-3 g-3">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Pedidos</h5>
          <p class="card-text">Registro digital de pedidos</p>
          <a class="btn btn-primary" href="<?=$base?>/index.php?c=pedido&a=index">Ir a Pedidos</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Stock</h5>
          <p class="card-text">Consulta en tiempo real</p>
          <a class="btn btn-primary" href="<?=$base?>/index.php?c=stock&a=index">Ir a Stock</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Reportes</h5>
          <p class="card-text">Ventas por cliente/producto/región</p>
          <a class="btn btn-primary" href="<?=$base?>/index.php?c=reportes&a=index">Ver Reportes</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__.'/../partials/footer.php'; ?>
