<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Productos</h4>
    <a href="index.php?c=producto&a=crear" class="btn btn-primary">Nuevo</a>
  </div>

  <form class="row g-2 mb-3">
    <input type="hidden" name="c" value="producto">
    <div class="col-sm-4"><input type="text" name="q" value="<?= View::e($q ?? '') ?>" class="form-control" placeholder="Buscar nombre o SKU"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Buscar</button></div>
  </form>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm table-striped mb-0">
        <thead><tr>
          <th>ID</th><th>SKU</th><th>Nombre</th><th>Cat.</th><th>Marca</th><th>Precio</th><th>Estado</th><th class="text-end">Acciones</th>
        </tr></thead>
        <tbody>
        <?php foreach(($productos ?? []) as $p): ?>
          <tr>
            <td><?= (int)$p['id_producto'] ?></td>
            <td><?= View::e($p['sku']) ?></td>
            <td><?= View::e($p['nombre']) ?></td>
            <td><?= View::e($p['categoria'] ?? '') ?></td>
            <td><?= View::e($p['marca'] ?? '') ?></td>
            <td>S/ <?= number_format((float)$p['precio_mayorista'],2) ?></td>
            <td><?= $p['activo'] ? '<span class="badge text-bg-success">Activo</span>' : '<span class="badge text-bg-secondary">Inactivo</span>' ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="index.php?c=producto&a=editar&id=<?= (int)$p['id_producto'] ?>">Editar</a>
              <a class="btn btn-sm btn-outline-warning" href="index.php?c=producto&a=stock&id=<?= (int)$p['id_producto'] ?>">Stock</a>
              <a class="btn btn-sm btn-outline-danger" href="index.php?c=producto&a=eliminar&id=<?= (int)$p['id_producto'] ?>" onclick="return confirm('¿Desactivar producto?')">Desactivar</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
