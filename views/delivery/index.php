<div class="container">
  <h4 class="mb-3">Deliveries</h4>

  <form class="row g-2 mb-3">
    <div class="col-auto">
      <select name="estado" class="form-select" onchange="this.form.submit()">
        <option value="">-- Todos --</option>
        <?php foreach (['PENDIENTE','EN_RUTA','ENTREGADO','FALLIDO'] as $e): ?>
          <option value="<?= $e ?>" <?= ($estado??'')===$e?'selected':'' ?>><?= $e ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead><tr>
        <th>#</th><th>Pedido</th><th>Cliente</th><th>Local salida</th>
        <th>Fecha programada</th><th>Estado</th><th class="text-end">Acciones</th>
      </tr></thead>
      <tbody>
      <?php foreach(($rows ?? []) as $r): ?>
        <tr>
          <td><?= (int)$r['id_delivery'] ?></td>
          <td>#<?= (int)$r['id_pedido'] ?></td>
          <td><?= View::e($r['razon_social'] ?: trim(($r['nombres']??'').' '.($r['apellidos']??''))) ?></td>
          <td><?= View::e($r['local_salida'] ?? '') ?></td>
          <td><?= View::e($r['fecha_programa'] ?? '') ?></td>
          <td><span class="badge bg-secondary"><?= View::e($r['estado']) ?></span></td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="index.php?c=delivery&a=ver&id=<?= (int)$r['id_delivery'] ?>">Ver</a>
            <a class="btn btn-sm btn-outline-secondary" href="index.php?c=delivery&a=programar&id_pedido=<?= (int)$r['id_pedido'] ?>">Editar</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
