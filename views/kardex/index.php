<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Kardex</h4>
    <div class="btn-group">
      <a class="btn btn-primary" href="index.php?c=kardex&a=ingreso">Ingreso</a>
      <a class="btn btn-warning" href="index.php?c=kardex&a=salida">Salida</a>
    </div>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm table-striped mb-0">
        <thead><tr><th>Fecha</th><th>Local</th><th>Producto</th><th>Tipo</th><th class="text-end">Cantidad</th><th>Detalle</th></tr></thead>
        <tbody>
        <?php foreach(($movs ?? []) as $m): ?>
          <tr>
            <td><?= View::e($m['fecha_mov']) ?></td>
            <td><?= View::e($m['local']) ?></td>
            <td><?= View::e($m['producto']) ?></td>
            <td><span class="badge text-bg-<?= $m['tipo_mov']==='INGRESO'?'success':($m['tipo_mov']==='SALIDA'?'danger':'info') ?>"><?= View::e($m['tipo_mov']) ?></span></td>
            <td class="text-end"><?= number_format((float)$m['cantidad'],2) ?></td>
            <td><?= View::e($m['detalle']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
