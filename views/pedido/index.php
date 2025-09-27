<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Pedidos</h4>
    <a href="index.php?c=pedido&a=registrar" class="btn btn-primary">Nuevo</a>
  </div>

  <form class="row g-2 mb-3">
    <input type="hidden" name="c" value="pedido">
    <div class="col-sm-4">
      <select name="estado" class="form-select" onchange="this.form.submit()">
        <option value="">-- Todos --</option>
        <?php foreach(['REGISTRADO','ALISTADO','DESPACHADO','ENTREGADO','ANULADO'] as $e): ?>
          <option value="<?= $e ?>" <?= (($estado ?? '')===$e?'selected':'') ?>><?= $e ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm table-striped mb-0">
        <thead><tr>
          <th>ID</th><th>Cliente</th><th>Local</th><th>Canal</th><th>Estado</th><th>Fecha</th><th class="text-end">Total</th><th class="text-end">Acciones</th>
        </tr></thead>
        <tbody>
        <?php foreach(($pedidos ?? []) as $p): ?>
          <tr>
            <td>#<?= (int)$p['id_pedido'] ?></td>
            <td><?= View::e($p['razon_social'] ?? (trim(($p['nombres'] ?? '').' '.($p['apellidos'] ?? '')))) ?></td>
            <td><?= View::e($p['local'] ?? '') ?></td>
            <td><span class="badge text-bg-info"><?= View::e($p['canal_venta']) ?></span></td>
            <td><span class="badge text-bg-secondary"><?= View::e($p['estado']) ?></span></td>
            <td><?= View::e($p['fecha_pedido']) ?></td>
            <td class="text-end">S/ <?= number_format((float)$p['total_neto'],2) ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="index.php?c=pedido&a=ver&id=<?= (int)$p['id_pedido'] ?>">Ver</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
