<?php
function opts($rows,$id,$txt){
  foreach($rows as $r){
    echo '<option value="'.(int)$r[$id].'">'.View::e($r[$txt]).'</option>';
  }
}
?>
<div class="container">
  <h4 class="mb-3">Nueva Transferencia</h4>
  <form method="post" action="index.php?c=transferencia&a=guardar" id="frmTransf">
    <?= csrf_field() ?>

    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Local Origen</label>
            <select class="form-select" name="id_local_origen" id="id_local_origen" required>
              <option value="">-- Seleccione --</option>
              <?php opts(($locales ?? []),'id_local','nombre'); ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Local Destino</label>
            <select class="form-select" name="id_local_destino" id="id_local_destino" required>
              <option value="">-- Seleccione --</option>
              <?php opts(($locales ?? []),'id_local','nombre'); ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Observación</label>
            <input name="observacion" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <strong>Ítems</strong>
          <button type="button" class="btn btn-sm btn-primary" id="btnAdd" disabled>Agregar</button>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle" id="tblItemsT">
            <thead>
              <tr>
                <th style="width:60%">Producto</th>
                <th style="width:20%">Cantidad</th>
                <th class="text-end">-</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <small class="text-muted">* Se listan productos con stock &gt; 0 en el local de origen.</small>
      </div>
    </div>

    <div class="mb-4">
      <button class="btn btn-success">Registrar Transferencia</button>
      <a href="index.php?c=transferencia" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>

<script>
// Lista de productos disponible según el local de origen
let prods = [];

function optionList() {
  if (!prods.length) return '<option value="">-- No hay productos con stock --</option>';
  return '<option value="">-- Seleccione --</option>' + prods.map(p =>
    `<option value="${p.id_producto}" data-stock="${p.stock}">
       ${p.nombre} (SKU ${p.sku}) [Stock: ${p.stock}]
     </option>`
  ).join('');
}

function rowT(){
  return `<tr>
    <td>
      <select name="item_id_producto[]" class="form-select form-select-sm" required>
        ${optionList()}
      </select>
    </td>
    <td>
      <input name="item_cantidad[]" type="number" min="0.01" step="0.01"
             class="form-control form-control-sm text-end" value="1" required>
    </td>
    <td class="text-end">
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">X</button>
    </td>
  </tr>`;
}

function addItemT(){
  const body = document.querySelector('#tblItemsT tbody');
  body.insertAdjacentHTML('beforeend', rowT());

  // Limitar cantidad al stock del producto elegido
  const tr  = body.lastElementChild;
  const sel = tr.querySelector('select');
  const qty = tr.querySelector('input[name="item_cantidad[]"]');

  sel.addEventListener('change', () => {
    const st = parseFloat(sel.selectedOptions[0]?.dataset.stock || '0');
    qty.max = st > 0 ? st : '';
    if (st > 0 && parseFloat(qty.value) > st) qty.value = st;
  });
}

async function cargarProductosPorLocal(idLocal) {
  prods = [];
  document.querySelector('#tblItemsT tbody').innerHTML = ''; // limpiar filas
  document.getElementById('btnAdd').disabled = true;

  if (!idLocal) return;

  try {
    const res = await fetch(`index.php?c=transferencia&a=productos&id_local=${encodeURIComponent(idLocal)}`);
    prods = await res.json(); // array indexado [{id_producto,...,stock}, ...]
  } catch (e) {
    console.error(e);
    prods = [];
  }

  // Agrega al menos una fila y habilita el botón si hay productos
  if (prods.length) {
    addItemT();
    document.getElementById('btnAdd').disabled = false;
  }
}

// Eventos
document.getElementById('btnAdd').addEventListener('click', addItemT);

document.getElementById('id_local_origen').addEventListener('change', (e) => {
  const origen = e.target.value;
  const destino = document.getElementById('id_local_destino');
  if (destino.value && destino.value === origen) destino.value = ''; // no permitir mismo local
  cargarProductosPorLocal(origen);
});

// Si el select de origen ya viene con valor, cargar al iniciar
document.addEventListener('DOMContentLoaded', () => {
  const v = document.getElementById('id_local_origen')?.value;
  if (v) cargarProductosPorLocal(v);
});
</script>
