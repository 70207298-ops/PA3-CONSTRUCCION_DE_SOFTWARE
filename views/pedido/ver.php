<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Pedido #<?= (int)$pedido['id_pedido'] ?></h4>
    <div class="btn-group">
      <a class="btn btn-outline-secondary" href="index.php?c=pedido">Volver</a>
      <?php if(($pedido['estado'] ?? '')==='REGISTRADO'): ?>
        <a class="btn btn-primary" href="index.php?c=pedido&a=alistar&id=<?= (int)$pedido['id_pedido'] ?>">Alistar</a>
      <?php endif; ?>
      <?php if(($pedido['estado'] ?? '')==='ALISTADO'): ?>
        <a class="btn btn-warning" href="index.php?c=pedido&a=despachar&id=<?= (int)$pedido['id_pedido'] ?>">Despachar</a>
      <?php endif; ?>
      <?php if(($pedido['estado'] ?? '')==='DESPACHADO'): ?>
        <a class="btn btn-success" href="index.php?c=pedido&a=entregar&id=<?= (int)$pedido['id_pedido'] ?>">Entregar</a>
      <?php endif; ?>
      <?php if(($pedido['estado'] ?? '')!=='ANULADO'): ?>
        <a class="btn btn-outline-danger" href="index.php?c=pedido&a=anular&id=<?= (int)$pedido['id_pedido'] ?>" onclick="return confirm('¿Anular pedido?')">Anular</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr><th>Producto</th><th class="text-end">Cant.</th><th class="text-end">P.Unit</th><th class="text-end">Desc.</th><th class="text-end">Subtotal</th></tr>
              </thead>
              <tbody>
              <?php foreach(($det ?? []) as $d): ?>
                <tr>
                  <td><?= View::e($d['nombre']) ?></td>
                  <td class="text-end"><?= number_format((float)$d['cantidad'],2) ?></td>
                  <td class="text-end"><?= number_format((float)$d['precio_unit'],2) ?></td>
                  <td class="text-end"><?= number_format((float)$d['descuento'],2) ?></td>
                  <td class="text-end"><?= number_format((float)$d['subtotal'],2) ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr><th colspan="4" class="text-end">Total Bruto</th><th class="text-end"><?= number_format((float)$pedido['total_bruto'],2) ?></th></tr>
                <tr><th colspan="4" class="text-end">Total Descuento</th><th class="text-end"><?= number_format((float)$pedido['total_descuento'],2) ?></th></tr>
                <tr><th colspan="4" class="text-end">Total Neto</th><th class="text-end"><?= number_format((float)$pedido['total_neto'],2) ?></th></tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="mb-3">Datos del Pedido</h6>
          <p class="mb-1"><strong>Estado:</strong> <span class="badge text-bg-secondary"><?= View::e($pedido['estado']) ?></span></p>
          <p class="mb-1"><strong>Fecha:</strong> <?= View::e($pedido['fecha_pedido']) ?></p>

          <div class="mt-2">
            <?php if (!empty($delivery)): ?>
              <div class="small mb-2">
                <div><strong>Delivery:</strong> <span class="badge bg-secondary"><?= View::e($delivery['estado']) ?></span></div>
                <?php if (!empty($delivery['fecha_programa'])): ?>
                  <div><strong>Programado:</strong> <?= View::e($delivery['fecha_programa']) ?></div>
                <?php endif; ?>
                <?php if (!empty($delivery['contacto'])): ?>
                  <div><strong>Contacto:</strong> <?= View::e($delivery['contacto']) ?> <?= !empty($delivery['telefono']) ? ' · '.View::e($delivery['telefono']) : '' ?></div>
                <?php endif; ?>
                <?php if (!empty($delivery['direccion_envio'])): ?>
                  <div><strong>Dirección:</strong> <?= View::e($delivery['direccion_envio']) ?></div>
                <?php endif; ?>
              </div>

              <div class="d-flex gap-2">
                <a class="btn btn-outline-info btn-sm" href="index.php?c=delivery&a=ver&id=<?= (int)$delivery['id_delivery'] ?>">Ver Delivery</a>
                <a class="btn btn-outline-primary btn-sm" href="index.php?c=delivery&a=programar&id_pedido=<?= (int)$pedido['id_pedido'] ?>">Editar programación</a>
              </div>
            <?php else: ?>
              <a class="btn btn-outline-primary btn-sm" href="index.php?c=delivery&a=programar&id_pedido=<?= (int)$pedido['id_pedido'] ?>">Programar Delivery</a>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
