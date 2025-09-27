<?php
declare(strict_types=1);

if (!class_exists('TransferenciaController')) {
class TransferenciaController extends Controller
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
        $Transfer = $this->model('Transferencia');
        $transf = method_exists($Transfer,'listar') ? $Transfer->listar() : [];
        $this->render('transferencia/index', compact('transf') + ['title'=>'Transferencias']);
    }

    // === NUEVO flujo: en la carga inicial NO enviamos productos; se piden por AJAX según el local de origen.
    public function crear()
    {
        $this->checkAuth();
        $Local = $this->model('Local');
        $locales = method_exists($Local,'listar') ? $Local->listar() : [];
        $productos = []; // se llenará vía fetch cuando el usuario elija el local de origen
        $this->render('transferencia/crear', compact('locales','productos') + ['title'=>'Nueva Transferencia']);
    }

    // === NUEVO: endpoint JSON para listar productos con stock>0 en un local dado
    // Llamada desde la vista: GET index.php?c=transferencia&a=productos&id_local=XX
    public function productos()
    {
        $this->checkAuth();
        $id_local = (int)($_GET['id_local'] ?? 0);

        $rows = [];
        if ($id_local > 0) {
            $Producto = $this->model('Producto');
            if (method_exists($Producto, 'listarPorLocal')) {
                $rows = $Producto->listarPorLocal($id_local); // debe usar stock_local y filtrar stock > 0
            } else {
                // Fallback: si aún no implementas listarPorLocal(), devolvemos vacío para evitar errores
                $rows = [];
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_values($rows)); // forzamos array indexado para que .map() funcione en JS
        exit;
    }

    public function guardar()
    {
        $this->checkAuth();
        $id_origen  = (int)($_POST['id_local_origen'] ?? 0);
        $id_destino = (int)($_POST['id_local_destino'] ?? 0);
        $observ     = trim($_POST['observacion'] ?? '');

        $ids  = $_POST['item_id_producto'] ?? [];
        $cant = $_POST['item_cantidad']    ?? [];

        // Normalizamos ítems válidos
        $items = [];
        $n = min(count($ids), count($cant));
        for ($i=0; $i<$n; $i++) {
            $pid = (int)$ids[$i];
            $qty = (float)$cant[$i];
            if ($pid > 0 && $qty > 0) {
                $items[] = ['id_producto'=>$pid, 'cantidad'=>$qty];
            }
        }

        if ($id_origen<=0 || $id_destino<=0 || $id_origen===$id_destino || empty($items)) {
            $_SESSION['flash_error'] = 'Complete origen, destino distintos y al menos 1 ítem válido.';
            header('Location: index.php?c=transferencia&a=crear'); exit;
        }

        try {
            $Transfer = $this->model('Transferencia');
            $id_transf = $Transfer->crear([
                'id_local_origen'  => $id_origen,
                'id_local_destino' => $id_destino,
                'observacion'      => $observ
            ], $items);

            $_SESSION['flash_success'] = 'Transferencia registrada (ID ' . $id_transf . ').';
            header('Location: index.php?c=transferencia&a=ver&id=' . $id_transf); exit;
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'No se pudo registrar: ' . $e->getMessage();
            header('Location: index.php?c=transferencia&a=crear'); exit;
        }
    }

    public function ver()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        $Transfer = $this->model('Transferencia');
        $transf = $Transfer->obtener($id);
        $det    = $Transfer->detalles($id);
        if (!$transf) {
            $_SESSION['flash_error'] = 'Transferencia no encontrada.';
            header('Location: index.php?c=transferencia'); exit;
        }
        $this->render('transferencia/ver', compact('transf','det') + ['title'=>'Detalle de Transferencia']);
    }

    public function enviar()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try {
            $Transfer = $this->model('Transferencia');
            $Transfer->enviar($id); // Debe generar kardex TRANSFERENCIA_OUT
            $_SESSION['flash_success'] = 'Transferencia enviada.';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'No se pudo enviar: ' . $e->getMessage();
        }
        header('Location: index.php?c=transferencia&a=ver&id='.$id); exit;
    }

    public function recibir()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try {
            $Transfer = $this->model('Transferencia');
            $Transfer->recibir($id); // Debe generar kardex TRANSFERENCIA_IN
            $_SESSION['flash_success'] = 'Transferencia recibida.';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'No se pudo recibir: ' . $e->getMessage();
        }
        header('Location: index.php?c=transferencia&a=ver&id='.$id); exit;
    }

    public function anular()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try {
            $Transfer = $this->model('Transferencia');
            $Transfer->anular($id);
            $_SESSION['flash_success'] = 'Transferencia anulada.';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'No se pudo anular: ' . $e->getMessage();
        }
        header('Location: index.php?c=transferencia&a=ver&id='.$id); exit;
    }
}}
