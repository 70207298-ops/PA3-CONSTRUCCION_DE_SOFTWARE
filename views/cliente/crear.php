<?php
$editing = isset($cliente);
$action  = $editing ? 'actualizar' : 'guardar';
$val = function($k,$d=''){ return View::e($cliente[$k] ?? $d); };
?>
<div class="container" style="max-width: 820px;">
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3"><?= $editing ? 'Editar Cliente' : 'Nuevo Cliente' ?></h4>
      <form method="post" action="index.php?c=cliente&a=<?= $action ?>">
        <?= csrf_field() ?>
        <?php if($editing): ?><input type="hidden" name="id" value="<?= (int)$cliente['id_cliente'] ?>"><?php endif; ?>
        <div class="row g-3">
          <div class="col-sm-4">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
              <option value="NATURAL" <?= ($val('tipo')==='NATURAL'?'selected':'') ?>>NATURAL</option>
              <option value="JURIDICA" <?= ($val('tipo')==='JURIDICA'?'selected':'') ?>>JURIDICA</option>
            </select>
          </div>
          <div class="col-sm-4">
            <label class="form-label">DNI</label>
            <input name="dni" class="form-control" value="<?= $val('dni') ?>">
          </div>
          <div class="col-sm-4">
            <label class="form-label">RUC</label>
            <input name="ruc" class="form-control" value="<?= $val('ruc') ?>">
          </div>

          <div class="col-sm-6">
            <label class="form-label">Nombres</label>
            <input name="nombres" class="form-control" value="<?= $val('nombres') ?>">
          </div>
          <div class="col-sm-6">
            <label class="form-label">Apellidos</label>
            <input name="apellidos" class="form-control" value="<?= $val('apellidos') ?>">
          </div>

          <div class="col-sm-12">
            <label class="form-label">Razón Social</label>
            <input name="razon_social" class="form-control" value="<?= $val('razon_social') ?>">
          </div>

          <div class="col-sm-4">
            <label class="form-label">Teléfono</label>
            <input name="telefono" class="form-control" value="<?= $val('telefono') ?>">
          </div>
          <div class="col-sm-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= $val('email') ?>">
          </div>
          <div class="col-sm-4">
            <label class="form-label">Estado</label>
            <select name="activo" class="form-select">
              <option value="1" <?= ((int)$val('activo',1)===1?'selected':'') ?>>Activo</option>
              <option value="0" <?= ((int)$val('activo',1)===0?'selected':'') ?>>Inactivo</option>
            </select>
          </div>

          <div class="col-sm-12">
            <label class="form-label">Dirección</label>
            <input name="direccion" class="form-control" value="<?= $val('direccion') ?>">
          </div>
        </div>
        <div class="mt-3 d-flex gap-2">
          <a class="btn btn-outline-secondary" href="index.php?c=cliente">Volver</a>
          <button class="btn btn-primary"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
