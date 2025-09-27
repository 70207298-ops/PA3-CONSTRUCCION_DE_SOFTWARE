<?php
$editing = isset($producto);
$action  = $editing ? 'actualizar' : 'guardar';
$val = function($k,$d=''){ return View::e($producto[$k] ?? $d); };
?>
<div class="container" style="max-width: 820px;">
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3"><?= $editing ? 'Editar Producto' : 'Nuevo Producto' ?></h4>
      <form method="post" action="index.php?c=producto&a=<?= $action ?>">
        <?= csrf_field() ?>
        <?php if($editing): ?><input type="hidden" name="id" value="<?= (int)$producto['id_producto'] ?>"><?php endif; ?>
        <div class="row g-3">
          <div class="col-sm-6">
            <label class="form-label">Categoría</label>
            <select name="id_categoria" class="form-select" required>
              <option value="">-- Seleccione --</option>
              <?php foreach(($categorias ?? []) as $c): ?>
                <option value="<?= (int)$c['id_categoria'] ?>" <?= ($val('id_categoria')==$c['id_categoria']?'selected':'') ?>><?= View::e($c['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Marca</label>
            <select name="id_marca" class="form-select" required>
              <option value="">-- Seleccione --</option>
              <?php foreach(($marcas ?? []) as $m): ?>
                <option value="<?= (int)$m['id_marca'] ?>" <?= ($val('id_marca')==$m['id_marca']?'selected':'') ?>><?= View::e($m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-4">
            <label class="form-label">SKU</label>
            <input name="sku" class="form-control" value="<?= $val('sku') ?>" required>
          </div>
          <div class="col-sm-8">
            <label class="form-label">Nombre</label>
            <input name="nombre" class="form-control" value="<?= $val('nombre') ?>" required>
          </div>
          <div class="col-sm-4">
            <label class="form-label">Unidad</label>
            <input name="unidad" class="form-control" value="<?= $val('unidad','UND') ?>">
          </div>
          <div class="col-sm-4">
            <label class="form-label">Costo Promedio</label>
            <input type="number" step="0.0001" name="costo_promedio" class="form-control" value="<?= $val('costo_promedio','0') ?>">
          </div>
          <div class="col-sm-4">
            <label class="form-label">Precio Mayorista</label>
            <input type="number" step="0.01" name="precio_mayorista" class="form-control" value="<?= $val('precio_mayorista','0') ?>">
          </div>
          <div class="col-sm-4">
            <label class="form-label">Estado</label>
            <select name="activo" class="form-select">
              <option value="1" <?= ((int)$val('activo',1)===1?'selected':'') ?>>Activo</option>
              <option value="0" <?= ((int)$val('activo',1)===0?'selected':'') ?>>Inactivo</option>
            </select>
          </div>
        </div>
        <div class="mt-3 d-flex gap-2">
          <a class="btn btn-outline-secondary" href="index.php?c=producto">Volver</a>
          <button class="btn btn-primary"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
