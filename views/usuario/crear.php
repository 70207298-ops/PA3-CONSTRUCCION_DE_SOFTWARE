<div class="container" style="max-width:720px">
  <h4 class="mb-3">Nuevo Usuario</h4>
  <form method="post" action="index.php?c=usuario&a=guardar">
    <?= csrf_field() ?>
    <div class="card"><div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Usuario</label><input name="usuario" class="form-control" required></div>
        <div class="col-md-8"><label class="form-label">Nombres</label><input name="nombres" class="form-control" required></div>
        <div class="col-md-4">
          <label class="form-label">Rol</label>
          <select name="rol" class="form-select">
            <option value="admin">admin</option>
            <option value="vendedor">vendedor</option>
            <option value="operador" selected>operador</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Estado</label>
          <select name="activo" class="form-select"><option value="1">Activo</option><option value="0">Inactivo</option></select>
        </div>
        <div class="col-md-4"><label class="form-label">Contraseña</label><input type="password" name="password" class="form-control" required></div>
      </div>
    </div></div>
    <div class="mt-3 d-flex gap-2">
      <a href="index.php?c=usuario" class="btn btn-outline-secondary">Volver</a>
      <button class="btn btn-success">Guardar</button>
    </div>
  </form>
</div>
