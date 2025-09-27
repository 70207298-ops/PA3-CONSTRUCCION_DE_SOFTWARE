<div class="container" style="max-width: 420px;">
  <div class="card mt-5">
    <div class="card-body">
      <h3 class="mb-3">Iniciar sesión</h3>
      <form method="post" action="index.php?c=auth&a=doLogin">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label">Usuario o Email</label>
          <input type="text" class="form-control" name="usuario" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <button class="btn btn-primary w-100">Ingresar</button>
      </form>
    </div>
  </div>
</div>
