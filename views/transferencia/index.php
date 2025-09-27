<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Transferencias</h4>
    <a href="index.php?c=transferencia&a=crear" class="btn btn-primary">Nueva</a>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm table-striped mb-0">
        <thead><tr><th>ID</th><th>Origen</th><th>Destino</th><th>Fecha</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
        <tbody>
        <?php foreach(($transf ?? []) as $t): ?>
          <tr>
            <td>#<?= (int)$t['id_transferencia'] ?></td>
            <td><?= View::e($t['origen']) ?></td>
            <td><?= View::e($t['destino']) ?></td>
            <td><?= View::e($t['fecha_transf']) ?></td>
            <td><span class="badge text-bg-secondary"><?= View::e($t['estado']) ?></span></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="index.php?c=transferencia&a=ver&id=<?= (int)$t['id_transferencia'] ?>">Ver</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
