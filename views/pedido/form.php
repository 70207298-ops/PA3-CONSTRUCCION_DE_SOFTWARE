<?php require __DIR__ . '/../partials/header.php'; ?>
<div class="container py-3">
  <h3>Nuevo Pedido</h3>
  <form method="post" action="index.php?c=pedido&a=guardar">
    <div class="mb-3">
      <label>Cliente (ID)</label>
      <input type="text" name="id_cliente" class="form-control">
    </div>
    <div class="mb-3">
      <label>Observación</label>
      <textarea name="observacion" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="index.php?c=pedido&a=index" class="btn btn-secondary">Volver</a>
  </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
