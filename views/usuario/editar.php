<?php $v = fn($k)=>View::e($usuario[$k] ?? ''); ?>
<div class="container" style="max-width:720px">
  <h4 class="mb-3">Editar Usuario</h4>
  <form method="post" action="index.php?c=usuario&a=actualizar">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)$usuario['id_usuario'] ?>">
    <div class="card"><div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Usuario</label><input name="usuario" class="form-control" value="<?= $v('usuario') ?>" required></div>
        <div class="col-md-8"><label class="form-label">Nombres</label><input name="nombres" class="form-control" value="<?= $v('nombres') ?>" required></div>
        <div class="col-md-4">
          <label class="form-label">Rol</label>
          <select name="rol" class="form-select">
            <?php foreach(['admin','vendedor','operador'] as $r): ?>
              <option value="<?= $r ?>" <?= ($usuario['rol']===$r?'selected':'') ?>><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Estado</label>
          <select name="activo" class="form-select">
            <option value="1" <?= $usuario['activo']?'selected':'' ?>>Activo</option>
            <option value="0" <?= !$usuario['activo']?'selected':'' ?>>Inactivo</option>
          </select>
        </div>
      </div>
    </div></div>
    <div class="mt-3 d-flex gap-2">
      <a href="index.php?c=usuario" class="btn btn-outline-secondary">Volver</a>
      <a href="index.php?c=usuario&a=clave&id=<?= (int)$usuario['id_usuario'] ?>" class="btn btn-outline-warning">Cambiar contraseña</a>
      <button class="btn btn-primary">Actualizar</button>
    </div>
  </form>
</div>
