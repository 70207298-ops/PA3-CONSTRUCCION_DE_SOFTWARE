<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Clientes</h4>
    <a href="index.php?c=cliente&a=crear" class="btn btn-primary">Nuevo</a>
  </div>

  <form class="row g-2 mb-3">
    <input type="hidden" name="c" value="cliente">
    <div class="col-sm-4"><input type="text" name="q" value="<?= View::e($q ?? '') ?>" class="form-control" placeholder="Buscar por nombre / razón social"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Buscar</button></div>
  </form>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm table-striped mb-0">
        <thead><tr>
          <th>ID</th><th>Tipo</th><th>Identidad</th><th>Contacto</th><th>Estado</th><th class="text-end">Acciones</th>
        </tr></thead>
        <tbody>
        <?php foreach(($clientes ?? []) as $c): ?>
          <tr>
            <td><?= (int)$c['id_cliente'] ?></td>
            <td><span class="badge bg-info-subtle text-info-emphasis"><?= View::e($c['tipo']) ?></span></td>
            <td>
              <?php if ($c['tipo']==='NATURAL'): ?>
                <?= View::e(($c['nombres'] ?? '').' '.($c['apellidos'] ?? '')) ?> <?php if(!empty($c['dni'])): ?><small class="text-muted">(DNI <?= View::e($c['dni']) ?>)</small><?php endif; ?>
              <?php else: ?>
                <?= View::e($c['razon_social']) ?> <?php if(!empty($c['ruc'])): ?><small class="text-muted">(RUC <?= View::e($c['ruc']) ?>)</small><?php endif; ?>
              <?php endif; ?>
            </td>
            <td>
              <?php if(!empty($c['telefono'])): ?>📞 <?= View::e($c['telefono']) ?><?php endif; ?>
              <?php if(!empty($c['email'])): ?><br>✉️ <?= View::e($c['email']) ?><?php endif; ?>
            </td>
            <td><?= $c['activo'] ? '<span class="badge text-bg-success">Activo</span>' : '<span class="badge text-bg-secondary">Inactivo</span>' ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="index.php?c=cliente&a=editar&id=<?= (int)$c['id_cliente'] ?>">Editar</a>
              <a class="btn btn-sm btn-outline-danger" href="index.php?c=cliente&a=eliminar&id=<?= (int)$c['id_cliente'] ?>" onclick="return confirm('¿Desactivar cliente?')">Desactivar</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
