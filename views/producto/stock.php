<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Stock por Local - <?= View::e($producto['nombre'] ?? '') ?></h4>
    <a href="index.php?c=producto" class="btn btn-outline-secondary">Volver</a>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm table-striped mb-0">
        <thead><tr><th>Local</th><th class="text-end">Stock</th></tr></thead>
        <tbody>
        <?php foreach(($stock ?? []) as $s): ?>
          <tr><td><?= View::e($s['nombre']) ?></td><td class="text-end"><?= number_format((float)$s['stock'],2) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
