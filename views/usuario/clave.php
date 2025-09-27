<div class="container" style="max-width:560px">
  <h4 class="mb-3">Cambiar Contraseña</h4>
  <form method="post" action="index.php?c=usuario&a=actualizarClave">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)$usuario['id_usuario'] ?>">
    <div class="card"><div class="card-body">
      <div class="row g-3">
        <div class="col-12"><label class="form-label">Nueva contraseña</label><input type="password" name="password" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Confirmar contraseña</label><input type="password" name="password2" class="form-control" required></div>
      </div>
    </div></div>
    <div class="mt-3 d-flex gap-2">
      <a href="index.php?c=usuario" class="btn btn-outline-secondary">Volver</a>
      <button class="btn btn-success">Guardar</button>
    </div>
  </form>
</div>
