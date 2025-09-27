<?php
// Helpers para combos
function optionize($arr,$id,$txt){ foreach($arr as $r){ echo '<option value="'.(int)$r[$id].'">'.View::e($r[$txt]).'</option>'; } }
?>
<div class="container">
  <h4 class="mb-3">Nuevo Pedido</h4>
  <form method="post" action="index.php?c=pedido&a=guardar" id="frmPedido">
    <?= csrf_field() ?>
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Cliente</label>
            <select class="form-select" name="id_cliente" required>
              <option value="">-- Seleccione --</option>
              <?php foreach(($clientes ?? []) as $c): ?>
                <option value="<?= (int)$c['id_cliente'] ?>"><?= View::e(($c['razon_social'] ?: ($c['nombres'].' '.$c['apellidos']))) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
<div class="col-md-3">
  <label class="form-label">Local de Salida</label>
  <!-- 👇 agrega id para el JS -->
  <select id="id_local_salida" class="form-select" name="id_local_salida" required>
    <option value="">-- Seleccione --</option>
    <?php optionize(($locales ?? []),'id_local','nombre'); ?>
  </select>
</div>
          <div class="col-md-3">
            <label class="form-label">Canal</label>
            <select class="form-select" name="canal_venta">
              <option value="PRESENCIAL">PRESENCIAL</option>
              <option value="TELEFONO">TELEFONO</option>
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
          <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">Agregar</button>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle" id="tblItems">
            <thead><tr>
              <th style="width:40%">Producto</th>
              <th style="width:12%">Cantidad</th>
              <th style="width:16%">Precio</th>
              <th style="width:16%">Desc.</th>
              <th style="width:16%">Subtotal</th>
              <th class="text-end">-</th>
            </tr></thead>
            <tbody></tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="text-end"><strong>Total Bruto:</strong></td>
                <td><input class="form-control form-control-sm text-end" id="totBruto" readonly></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="4" class="text-end"><strong>Total Descuento:</strong></td>
                <td><input class="form-control form-control-sm text-end" id="totDesc" readonly></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="4" class="text-end"><strong>Total Neto:</strong></td>
                <td><input class="form-control form-control-sm text-end" id="totNeto" readonly></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <button class="btn btn-success">Registrar Pedido</button>
      <a href="index.php?c=pedido" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
<script>
// ===============================
// Catálogo (cargado por local)
// ===============================
let catalogo = [];          // [{id_producto,nombre,sku,stock,precio_mayorista,...}]
let opcionesHTML = '<option value="">-- Seleccione --</option>';

async function cargarCatalogoPorLocal() {
  const idLocal = document.getElementById('id_local_salida').value || 0;
  if (!idLocal) {
    catalogo = [];
    opcionesHTML = '<option value="">-- Seleccione --</option>';
    refrescarSelectsProducto();
    return;
  }

  const res  = await fetch(`index.php?c=pedido&a=productosPorLocal&id_local=${idLocal}`);
  catalogo   = await res.json(); // asegura que tu endpoint devuelva precio_mayorista
  opcionesHTML = ['<option value="">-- Seleccione --</option>']
    .concat(catalogo.map(p =>
      `<option value="${p.id_producto}"
               data-precio="${p.precio_mayorista ?? 0}"
               data-stock="${p.stock ?? 0}">
         ${p.nombre} (SKU ${p.sku} · Stock: ${p.stock ?? 0})
       </option>`
    )).join('');

  // Rellena todos los selects existentes
  refrescarSelectsProducto();
}

function refrescarSelectsProducto() {
  document.querySelectorAll('#tblItems .sel-producto').forEach(sel => {
    const prev = sel.value;
    sel.innerHTML = opcionesHTML;
    // si el anterior ya no existe en este local, queda vacío
    if (!catalogo.find(p => String(p.id_producto) === String(prev))) sel.value = '';
    // re-sincroniza precio y límite de cantidad
    syncPrecioYLimite(sel);
  });
}

// ===============================
// Fábrica de filas
// ===============================
function rowTemplate() {
  return `<tr>
    <td>
      <select name="item_id_producto[]" class="form-select form-select-sm sel-producto"
              onchange="syncPrecioYLimite(this)">
        ${opcionesHTML}
      </select>
    </td>
    <td>
      <input name="item_cantidad[]" type="number" min="0" step="0.01"
             class="form-control form-control-sm text-end" value="1"
             oninput="validarCantidad(this); recalc();">
    </td>
    <td><input name="item_precio[]" type="number" min="0" step="0.01"
               class="form-control form-control-sm text-end" value="0" oninput="recalc()"></td>
    <td><input name="item_descuento[]" type="number" min="0" step="0.01"
               class="form-control form-control-sm text-end" value="0" oninput="recalc()"></td>
    <td><input class="form-control form-control-sm text-end" name="item_subtotal[]" readonly></td>
    <td class="text-end">
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="delRow(this)">X</button>
    </td>
  </tr>`;
}

function addItem(){
  document.querySelector('#tblItems tbody').insertAdjacentHTML('beforeend', rowTemplate());
}

function delRow(btn){
  btn.closest('tr').remove(); recalc();
}

// ===============================
// Sincronizar precio y límite
// ===============================
function syncPrecioYLimite(sel){
  const opt = sel.selectedOptions[0];
  const tr  = sel.closest('tr');
  const $precio = tr.querySelector('[name="item_precio[]"]');
  const $cant   = tr.querySelector('[name="item_cantidad[]"]');

  if (opt) {
    const precio = parseFloat(opt.dataset.precio || '0') || 0;
    const stock  = parseFloat(opt.dataset.stock  || '0') || 0;

    // set precio por defecto
    $precio.value = precio.toFixed(2);

    // limitar cantidad al stock
    $cant.max = stock > 0 ? stock : null;
    validarCantidad($cant);
  }
  recalc();
}

function validarCantidad(input){
  const max = parseFloat(input.max || '0');
  let v = parseFloat(input.value || '0') || 0;
  if (max > 0 && v > max) {
    v = max; input.value = v;
    input.classList.add('is-invalid');   // feedback visual
  } else {
    input.classList.remove('is-invalid');
  }
}

// ===============================
// Totales
// ===============================
function recalc(){
  let bruto=0, desc=0, neto=0;
  document.querySelectorAll('#tblItems tbody tr').forEach(tr=>{
    const qty = parseFloat(tr.querySelector('[name="item_cantidad[]"]').value)||0;
    const pv  = parseFloat(tr.querySelector('[name="item_precio[]"]').value)||0;
    const dsc = parseFloat(tr.querySelector('[name="item_descuento[]"]').value)||0;
    const sub = Math.max(qty*pv - dsc, 0);
    tr.querySelector('[name="item_subtotal[]"]').value = sub.toFixed(2);
    bruto += qty*pv; desc += dsc; neto += sub;
  });
  document.getElementById('totBruto').value = bruto.toFixed(2);
  document.getElementById('totDesc').value  = desc.toFixed(2);
  document.getElementById('totNeto').value  = neto.toFixed(2);
}

// ===============================
// Inicio
// ===============================
document.getElementById('id_local_salida').addEventListener('change', cargarCatalogoPorLocal);
document.addEventListener('DOMContentLoaded', async () => {
  await cargarCatalogoPorLocal(); // carga catálogo del local (si ya viene seleccionado)
  addItem();                      // crea la primera fila
});
</script>