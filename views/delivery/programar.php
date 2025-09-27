<?php
$hasDelivery = isset($delivery) && is_array($delivery);
$val = function($k,$d='') use ($hasDelivery,$delivery){ return View::e($hasDelivery?($delivery[$k]??$d):$d); };
$dt = ($hasDelivery && !empty($delivery['fecha_programa'])) ? date('Y-m-d\TH:i', strtotime($delivery['fecha_programa'])) : '';
?>
<div class="container" style="max-width: 820px;">
  <h4 class="mb-3">Programar Delivery - Pedido #<?= (int)$id_pedido ?></h4>

  <?php if ($hasDelivery): ?>
    <div class="alert alert-info py-2">
      Ya existe una programación.
      <a class="ms-2" href="index.php?c=delivery&a=ver&id=<?= (int)$delivery['id_delivery'] ?>">Ver detalle</a>
    </div>
  <?php endif; ?>

  <form method="post" action="index.php?c=delivery&a=guardar">
    <?= csrf_field() ?>
    <input type="hidden" name="id_pedido" value="<?= (int)$id_pedido ?>">

    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Dirección de envío</label>
            <input name="direccion_envio" class="form-control" required value="<?= $val('direccion_envio') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Contacto</label>
            <input name="contacto" class="form-control" value="<?= $val('contacto') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Teléfono</label>
            <input name="telefono" class="form-control" value="<?= $val('telefono') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Fecha Programada</label>
            <!-- nombre compatible con el controlador (acepta fecha_programa o fecha_programada) -->
            <input type="datetime-local" name="fecha_programa" class="form-control" value="<?= View::e($dt) ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Observación</label>
            <input name="observacion" class="form-control" value="<?= $val('observacion') ?>">
          </div>
        </div>
      </div>
    </div>

    <div class="mt-3 d-flex gap-2">
      <a class="btn btn-outline-secondary" href="index.php?c=pedido&a=ver&id=<?= (int)$id_pedido ?>">Volver</a>
      <button class="btn btn-success">Guardar</button>
    </div>
  </form>
</div>
