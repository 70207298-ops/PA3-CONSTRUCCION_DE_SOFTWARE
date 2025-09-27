<?php
$editing = isset($producto);
$action  = $editing ? 'actualizar' : 'guardar';

// helpers para array/objeto
$val = function($k, $d='') use ($producto) {
  if (is_array($producto))  $v = $producto[$k] ?? $d;
  elseif (is_object($producto)) $v = $producto->$k ?? $d;
  else $v = $d;
  return View::e((string)$v);
};
$raw = function($k, $d='') use ($producto) {
  if (is_array($producto))  return $producto[$k] ?? $d;
  if (is_object($producto)) return $producto->$k ?? $d;
  return $d;
};

// para listas (categorías/marcas) también soportar obj/array
$get = function($row, $k, $d='') {
  if (is_array($row))  return $row[$k] ?? $d;
  if (is_object($row)) return $row->$k ?? $d;
  return $d;
};

$idProducto = $raw('id_producto', '');
$idCatSel   = (string)$raw('id_categoria', '');
$idMarSel   = (string)$raw('id_marca', '');
$activoSel  = (int)$raw('activo', 1);
?>
<div class="container" style="max-width: 820px;">
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3"><?= $editing ? 'Editar Producto' : 'Nuevo Producto' ?></h4>
      <form method="post" action="index.php?c=producto&a=<?= $action ?>">
        <?= csrf_field() ?>
        <?php if($editing): ?>
          <input type="hidden" name="id" value="<?= View::e($idProducto) ?>">
        <?php endif; ?>

        <div class="row g-3">
          <div class="col-sm-6">
            <label class="form-label">Categoría</label>
            <select name="id_categoria" class="form-select" required>
              <option value="">-- Seleccione --</option>
              <?php foreach(($categorias ?? []) as $c): 
                $idc = (string)$get($c,'id_categoria');
                $nom = $get($c,'nombre');
              ?>
                <option value="<?= View::e($idc) ?>" <?= $idCatSel===$idc ? 'selected' : '' ?>>
                  <?= View::e($nom) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-sm-6">
            <label class="form-label">Marca</label>
            <select name="id_marca" class="form-select" required>
              <option value="">-- Seleccione --</option>
              <?php foreach(($marcas ?? []) as $m):
                $idm = (string)$get($m,'id_marca');
                $nom = $get($m,'nombre');
              ?>
                <option value="<?= View::e($idm) ?>" <?= $idMarSel===$idm ? 'selected' : '' ?>>
                  <?= View::e($nom) ?>
                </option>
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
              <option value="1" <?= $activoSel===1 ? 'selected' : '' ?>>Activo</option>
              <option value="0" <?= $activoSel===0 ? 'selected' : '' ?>>Inactivo</option>
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
