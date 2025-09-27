<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Usuarios</h4>
    <a class="btn btn-primary" href="index.php?c=usuario&a=crear">Nuevo</a>
  </div>

  <form class="row g-2 mb-3">
    <div class="col-auto"><input name="q" class="form-control" placeholder="Buscar..." value="<?= View::e($q ?? '') ?>"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Buscar</button></div>
  </form>

  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead><tr><th>#</th><th>Usuario</th><th>Nombres</th><th>Rol</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
      <tbody>
        <?php foreach(($usuarios ?? []) as $u): ?>
        <tr>
          <td><?= (int)$u['id_usuario'] ?></td>
          <td><?= View::e($u['usuario']) ?></td>
          <td><?= View::e($u['nombres']) ?></td>
          <td><span class="badge bg-info"><?= View::e($u['rol']) ?></span></td>
          <td><?= $u['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
          <td class="text-end d-flex gap-2 justify-content-end">
            <a class="btn btn-sm btn-outline-primary" href="index.php?c=usuario&a=editar&id=<?= (int)$u['id_usuario'] ?>">Editar</a>
            <a class="btn btn-sm btn-outline-warning" href="index.php?c=usuario&a=clave&id=<?= (int)$u['id_usuario'] ?>">Contraseña</a>
            <?php if ($u['activo']): ?>
              <a class="btn btn-sm btn-outline-danger" href="index.php?c=usuario&a=desactivar&id=<?= (int)$u['id_usuario'] ?>">Desactivar</a>
            <?php else: ?>
              <a class="btn btn-sm btn-outline-success" href="index.php?c=usuario&a=activar&id=<?= (int)$u['id_usuario'] ?>">Activar</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
