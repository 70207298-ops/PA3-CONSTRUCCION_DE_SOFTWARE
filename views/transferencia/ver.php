<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Transferencia #<?= (int)$transf['id_transferencia'] ?></h4>
    <div class="btn-group">
      <a class="btn btn-outline-secondary" href="index.php?c=transferencia">Volver</a>
      <?php if(($transf['estado'] ?? '')==='REGISTRADA'): ?>
        <a class="btn btn-primary" href="index.php?c=transferencia&a=enviar&id=<?= (int)$transf['id_transferencia'] ?>">Enviar</a>
      <?php endif; ?>
      <?php if(($transf['estado'] ?? '')==='ENVIADA'): ?>
        <a class="btn btn-success" href="index.php?c=transferencia&a=recibir&id=<?= (int)$transf['id_transferencia'] ?>">Recibir</a>
      <?php endif; ?>
      <?php if(($transf['estado'] ?? '')!=='ANULADA'): ?>
        <a class="btn btn-outline-danger" href="index.php?c=transferencia&a=anular&id=<?= (int)$transf['id_transferencia'] ?>" onclick="return confirm('¿Anular transferencia?')">Anular</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm mb-0">
        <thead><tr><th>Producto</th><th class="text-end">Cantidad</th></tr></thead>
        <tbody>
        <?php foreach(($det ?? []) as $d): ?>
          <tr>
            <td><?= View::e($d['nombre']) ?></td>
            <td class="text-end"><?= number_format((float)$d['cantidad'],2) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
