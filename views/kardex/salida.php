<div class="container" style="max-width: 820px;">
  <h4 class="mb-3">Salida de Stock</h4>
  <form method="post" action="index.php?c=kardex&a=guardarSalida">
    <?= csrf_field() ?>
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Local</label>
            <select name="id_local" class="form-select" required>
              <option value="">-- Seleccione --</option>
              <?php foreach(($locales ?? []) as $l): ?>
                <option value="<?= (int)$l['id_local'] ?>"><?= View::e($l['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Producto</label>
            <select name="id_producto" class="form-select" required>
              <option value="">-- Seleccione --</option>
              <?php foreach(($productos ?? []) as $p): ?>
                <option value="<?= (int)$p['id_producto'] ?>"><?= View::e($p['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Cantidad</label>
            <input type="number" step="0.01" name="cantidad" class="form-control" required>
          </div>
          <div class="col-12">
            <label class="form-label">Detalle</label>
            <input name="detalle" class="form-control">
          </div>
        </div>
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <a class="btn btn-outline-secondary" href="index.php?c=kardex">Volver</a>
      <button class="btn btn-danger">Guardar</button>
    </div>
  </form>
</div>
