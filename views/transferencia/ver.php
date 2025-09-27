<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0">Transferencia #<?= (int)$transf['id_transferencia'] ?></h4>
    <div class="btn-group">
      <a class="btn btn-outline-secondary" href="index.php?c=transferencia">Volver</a>
      <?php if(($transf['estado'] ?? 'REGISTRADA')==='REGISTRADA'): ?>
        <a class="btn btn-success" href="index.php?c=transferencia&a=procesar&id=<?= (int)$transf['id_transferencia'] ?>" onclick="return confirm('¿Procesar transferencia? Esto actualizará el stock de ambos locales.')">
          Procesar Transferencia
        </a>
      <?php endif; ?>
      <?php if(($transf['estado'] ?? '')!=='ANULADA' && ($transf['estado'] ?? '')!=='PROCESADA'): ?>
        <a class="btn btn-outline-danger" href="index.php?c=transferencia&a=anular&id=<?= (int)$transf['id_transferencia'] ?>" onclick="return confirm('¿Anular transferencia?')">Anular</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <div class="row">
        <div class="col-md-6">
          <strong>Origen:</strong> <?= View::e($transf['origen'] ?? 'N/A') ?>
        </div>
        <div class="col-md-6">
          <strong>Destino:</strong> <?= View::e($transf['destino'] ?? 'N/A') ?>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-6">
          <strong>Estado:</strong> 
          <span class="badge <?= match($transf['estado'] ?? 'REGISTRADA') {
            'REGISTRADA' => 'bg-warning',
            'PROCESADA' => 'bg-success',
            'ANULADA' => 'bg-danger',
            default => 'bg-secondary'
          } ?>">
            <?= $transf['estado'] ?? 'REGISTRADA' ?>
          </span>
        </div>
        <div class="col-md-6">
          <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($transf['fecha_transf'] ?? 'now')) ?>
        </div>
      </div>
      <?php if (!empty($transf['observacion'])): ?>
      <div class="row mt-2">
        <div class="col-12">
          <strong>Observación:</strong> <?= View::e($transf['observacion']) ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
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
