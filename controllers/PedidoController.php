<?php

declare(strict_types=1);

if (!class_exists('PedidoController')) {
class PedidoController extends Controller
{
    private function checkAuth()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php?c=auth&a=login'); exit;
        }
    }

    public function index()
    {
        $this->checkAuth();
        $Pedido = $this->model('Pedido');
        $estado = $_GET['estado'] ?? '';
        $pedidos = method_exists($Pedido,'listar') ? $Pedido->listar($estado) : [];
        $this->render('pedido/index', compact('pedidos','estado') + ['title'=>'Pedidos']);
    }
public function registrar()
{
    $this->checkAuth();
    $Cliente = $this->model('Cliente');
    $Local   = $this->model('Local');

    $clientes = method_exists($Cliente,'listar') ? $Cliente->listar('') : [];
    $locales  = method_exists($Local,'listar')   ? $Local->listar()   : [];

    // NOTA: no pasamos $productos; el <select> se pobla con AJAX por local
    $this->render('pedido/registrar', compact('clientes','locales') + ['title'=>'Nuevo Pedido']);
}

public function guardar()
{
    $this->checkAuth();
    $id_cliente      = (int)($_POST['id_cliente'] ?? 0);
    $id_local_salida = (int)($_POST['id_local_salida'] ?? 0); // <== este name debe coincidir con el select
    $canal_venta     = $_POST['canal_venta'] ?? 'PRESENCIAL';
    $observacion     = trim($_POST['observacion'] ?? '');

    // Arrays paralelos de ítems
    $ids    = $_POST['item_id_producto'] ?? [];
    $cant   = $_POST['item_cantidad']    ?? [];
    $precio = $_POST['item_precio']      ?? [];
    $desc   = $_POST['item_descuento']   ?? [];

    $items = [];
    $total_bruto = 0.0;
    $total_desc  = 0.0;

    for ($i=0; $i<count($ids); $i++) {
        $pid = (int)$ids[$i];
        $qty = (float)$cant[$i];
        $pvu = (float)$precio[$i];
        $dsc = (float)($desc[$i] ?? 0);

        if ($pid>0 && $qty>0 && $pvu>=0) {
            $subtotal = round($qty*$pvu - $dsc, 2);
            $items[] = [
                'id_producto' => $pid,
                'cantidad'    => $qty,
                'precio_unit' => $pvu,
                'descuento'   => $dsc,
                'subtotal'    => $subtotal,
            ];
            $total_bruto += $qty*$pvu;
            $total_desc  += $dsc;
        }
    }
    $total_neto = round($total_bruto - $total_desc, 2);

    if ($id_cliente<=0 || $id_local_salida<=0 || empty($items)) {
        $_SESSION['flash_error'] = 'Complete cliente, local y agregue al menos 1 ítem.';
        header('Location: index.php?c=pedido&a=registrar'); exit;
    }

    // ✅ Validación de stock en servidor (anti-manipulación del HTML)
    $Producto = $this->model('Producto');
    foreach ($items as $it) {
        $stock = $Producto->stockEnLocal($id_local_salida, (int)$it['id_producto']);
        if ($stock === null || $stock <= 0) {
            $_SESSION['flash_error'] = 'El producto seleccionado no tiene stock en el local.';
            header('Location: index.php?c=pedido&a=registrar'); exit;
        }
        if ($stock < (float)$it['cantidad']) {
            $_SESSION['flash_error'] = 'Stock insuficiente. Disponible: ' . $stock;
            header('Location: index.php?c=pedido&a=registrar'); exit;
        }
    }

    try {
        $Pedido = $this->model('Pedido');
        $id_pedido = $Pedido->crear([
            'id_cliente'      => $id_cliente,
            'id_local_salida' => $id_local_salida,
            'canal_venta'     => $canal_venta,
            'observacion'     => $observacion,
            'total_bruto'     => $total_bruto,
            'total_descuento' => $total_desc,
            'total_neto'      => $total_neto,
        ], $items);

        $_SESSION['flash_success'] = 'Pedido registrado (ID '.$id_pedido.').';
        header('Location: index.php?c=pedido&a=ver&id='.$id_pedido); exit;
    } catch (\Throwable $e) {
        $_SESSION['flash_error'] = 'No se pudo registrar: '.$e->getMessage();
        header('Location: index.php?c=pedido&a=registrar'); exit;
    }
}


public function ver()
{
    $this->checkAuth();
    $id = (int)($_GET['id'] ?? 0);

    $Pedido = $this->model('Pedido');
    $pedido = $Pedido->obtener($id);
    $det    = $Pedido->detalles($id);

    // 👇 NUEVO: traer programación de delivery (si existe)
    $Delivery = $this->model('Delivery');
    $delivery = $Delivery->obtenerPorPedido($id); // null si no hay

    if (!$pedido) { $_SESSION['flash_error']='Pedido no encontrado.'; header('Location: index.php?c=pedido'); exit; }

    // 👇 incluir $delivery en el render
    $this->render('pedido/ver', compact('pedido','det','delivery') + ['title'=>"Pedido #$id"]);
}


    public function alistar()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try{
            $Pedido = $this->model('Pedido');
            $Pedido->cambiarEstado($id, 'ALISTADO');
            $_SESSION['flash_success'] = 'Pedido alistado.';
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo alistar: ' . $e->getMessage();
        }
        header('Location: index.php?c=pedido&a=ver&id='.$id); exit;
    }

    public function despachar()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try{
            $Pedido = $this->model('Pedido');
            // El modelo debe rebajar stock y escribir en kardex como SALIDA
            $Pedido->despachar($id);
            $_SESSION['flash_success'] = 'Pedido despachado.';
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo despachar: ' . $e->getMessage();
        }
        header('Location: index.php?c=pedido&a=ver&id='.$id); exit;
    }

    public function entregar()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try{
            $Pedido = $this->model('Pedido');
            $Pedido->cambiarEstado($id, 'ENTREGADO');
            $_SESSION['flash_success'] = 'Pedido entregado.';
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo entregar: ' . $e->getMessage();
        }
        header('Location: index.php?c=pedido&a=ver&id='.$id); exit;
    }

    public function anular()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try{
            $Pedido = $this->model('Pedido');
            $Pedido->anular($id); // El modelo deberá revertir stock si ya estaba despachado
            $_SESSION['flash_success'] = 'Pedido anulado.';
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo anular: ' . $e->getMessage();
        }
        header('Location: index.php?c=pedido&a=ver&id='.$id); exit;
    }
  public function productosPorLocal()
{
    $this->checkAuth();
    $id_local = (int)($_GET['id_local'] ?? 0);

    header('Content-Type: application/json; charset=utf-8');
    if ($id_local <= 0) { echo json_encode([]); return; }

    $Producto = $this->model('Producto');
    $rows = $Producto->listarPorLocal($id_local); // solo activos y stock>0
    echo json_encode($rows);
}
  
}}
