<?php
$cli = $delivery['razon_social'] ?: trim(($delivery['nombres']??'').' '.($delivery['apellidos']??''));
?>
<div class="container">
  <h4 class="mb-3">Delivery #<?= (int)$delivery['id_delivery'] ?>  <small class="text-muted">Pedido #<?= (int)$delivery['id_pedido'] ?></small></h4>

  <div class="card mb-3"><div class="card-body">
    <div class="row g-3">
      <div class="col-12"><strong>Cliente:</strong> <?= View::e($cli) ?></div>
      <div class="col-md-6"><strong>Local de salida:</strong> <?= View::e($delivery['local_salida'] ?? '') ?></div>
      <div class="col-md-6"><strong>Estado:</strong> <span class="badge bg-secondary"><?= View::e($delivery['estado']) ?></span></div>
      <div class="col-12"><strong>Dirección de envío:</strong> <?= View::e($delivery['direccion_envio'] ?? '') ?></div>
      <div class="col-md-4"><strong>Contacto:</strong> <?= View::e($delivery['contacto'] ?? '') ?></div>
      <div class="col-md-4"><strong>Teléfono:</strong> <?= View::e($delivery['telefono'] ?? '') ?></div>
      <div class="col-md-4"><strong>Fecha programada:</strong> <?= View::e($delivery['fecha_programa'] ?? '') ?></div>
      <div class="col-md-4"><strong>Salida:</strong> <?= View::e($delivery['fecha_salida'] ?? '') ?></div>
      <div class="col-md-4"><strong>Entrega:</strong> <?= View::e($delivery['fecha_entrega'] ?? '') ?></div>
      <div class="col-12"><strong>Observación:</strong> <?= View::e($delivery['observacion'] ?? '') ?></div>
    </div>
  </div></div>

  <div class="d-flex gap-2">
<a class="btn btn-outline-secondary" href="index.php?c=delivery">Volver</a>
<a class="btn btn-outline-primary" href="index.php?c=delivery&a=programar&id=<?= (int)$delivery['id_delivery'] ?>">
  Editar programación
</a>

<a class="btn btn-warning" href="index.php?c=delivery&a=marcarEnRuta&id=<?= (int)$delivery['id_delivery'] ?>">
  Marcar En Ruta
</a>
<a class="btn btn-success" href="index.php?c=delivery&a=marcarEntregado&id=<?= (int)$delivery['id_delivery'] ?>">
  Marcar Entregado
</a>
<a class="btn btn-danger" href="index.php?c=delivery&a=marcarFallido&id=<?= (int)$delivery['id_delivery'] ?>">
  Marcar Fallido
</a>>
  </div>
</div>
