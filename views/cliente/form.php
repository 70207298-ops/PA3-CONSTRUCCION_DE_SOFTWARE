<?php require __DIR__.'/../partials/header.php'; ?>
<div class="container mt-3">
  <h3><?= $title ?></h3>
  <?php include __DIR__.'/../partials/flash.php'; ?>

  <form method="post" action="index.php?c=cliente&a=<?= isset($cliente['id_cliente'])?'actualizar':'guardar' ?>">
    <?php if(isset($cliente['id_cliente'])): ?>
      <input type="hidden" name="id" value="<?= (int)$cliente['id_cliente'] ?>">
    <?php endif; ?>

    <div class="mb-2">
      <label class="form-label">Tipo</label>
      <select class="form-select" name="tipo" id="tipo">
        <option value="NATURAL" <?= ($cliente['tipo']??'')=='NATURAL'?'selected':'' ?>>NATURAL</option>
        <option value="JURIDICA" <?= ($cliente['tipo']??'')=='JURIDICA'?'selected':'' ?>>JURIDICA</option>
      </select>
    </div>

    <div id="box-natural" class="row g-2">
      <div class="col-md-6">
        <label class="form-label">Nombres</label>
        <input class="form-control" name="nombres" value="<?= htmlspecialchars($cliente['nombres']??'') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Apellidos</label>
        <input class="form-control" name="apellidos" value="<?= htmlspecialchars($cliente['apellidos']??'') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">DNI</label>
        <input class="form-control" name="dni" maxlength="8" value="<?= htmlspecialchars($cliente['dni']??'') ?>">
      </div>
    </div>

    <div id="box-juridica" class="row g-2">
      <div class="col-md-8">
        <label class="form-label">Razón Social</label>
        <input class="form-control" name="razon_social" value="<?= htmlspecialchars($cliente['razon_social']??'') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">RUC</label>
        <input class="form-control" name="ruc" maxlength="11" value="<?= htmlspecialchars($cliente['ruc']??'') ?>">
      </div>
    </div>

    <div class="row g-2 mt-1">
      <div class="col-md-4">
        <label class="form-label">Teléfono</label>
        <input class="form-control" name="telefono" value="<?= htmlspecialchars($cliente['telefono']??'') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input class="form-control" name="email" value="<?= htmlspecialchars($cliente['email']??'') ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Dirección</label>
        <input class="form-control" name="direccion" value="<?= htmlspecialchars($cliente['direccion']??'') ?>">
      </div>
      <?php if(isset($cliente['id_cliente'])): ?>
      <div class="col-md-2 form-check mt-4">
        <input class="form-check-input" type="checkbox" name="activo" id="activo" <?= !empty($cliente['activo'])?'checked':'' ?>>
        <label class="form-check-label" for="activo">Activo</label>
      </div>
      <?php endif; ?>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-primary">Guardar</button>
      <a class="btn btn-secondary" href="index.php?c=cliente&a=index">Volver</a>
    </div>
  </form>
</div>

<script>
function toggleTipo() {
  const t = document.getElementById('tipo').value;
  document.getElementById('box-natural').style.display = (t === 'NATURAL') ? '' : 'none';
  document.getElementById('box-juridica').style.display = (t === 'JURIDICA') ? '' : 'none';
}
document.getElementById('tipo').addEventListener('change', toggleTipo);
toggleTipo();
</script>
<?php require __DIR__.'/../partials/footer.php'; ?>
