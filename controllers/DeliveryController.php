<?php
declare(strict_types=1);

if (!class_exists('DeliveryController')) {
class DeliveryController extends Controller
{
    private function checkAuth() {
        if (empty($_SESSION['user'])) { header('Location: index.php?c=auth&a=login'); exit; }
    }

    public function index() {
        $this->checkAuth();
        $Delivery = $this->model('Delivery');
        $estado = $_GET['estado'] ?? null; // opcional: filtrar por estado
        $rows = $Delivery->listar($estado ?: null);
        $this->render('delivery/index', compact('rows','estado') + ['title'=>'Deliveries']);
    }

public function programar() {
  $this->checkAuth();
  $id_pedido = (int)($_GET['id_pedido'] ?? 0);
  if ($id_pedido <= 0) { $_SESSION['flash_error']='Pedido inválido.'; header('Location: index.php?c=pedido'); exit; }

  $Delivery = $this->model('Delivery');
  $delivery = $Delivery->obtenerPorPedido($id_pedido); // puede ser null
  $this->render('delivery/programar', compact('id_pedido','delivery') + ['title'=>"Programar Delivery - Pedido #$id_pedido"]);
}


    public function guardar() {
        $this->checkAuth();
        $id_pedido = (int)($_POST['id_pedido'] ?? 0);
        if ($id_pedido<=0) { $_SESSION['flash_error']='Pedido inválido.'; header('Location: index.php?c=pedido'); exit; }

        // name del input puede ser fecha_programada o fecha_programa
        $fecha = $_POST['fecha_programada'] ?? $_POST['fecha_programa'] ?? null;
        $fecha = $fecha ? date('Y-m-d H:i:s', strtotime($fecha)) : null;

        $data = [
            'direccion_envio' => trim($_POST['direccion_envio'] ?? ''),
            'contacto'        => trim($_POST['contacto'] ?? ''),
            'telefono'        => trim($_POST['telefono'] ?? ''),
            'fecha_programa'  => $fecha,
            'observacion'     => trim($_POST['observacion'] ?? '')
        ];

        try {
            $Delivery = $this->model('Delivery');
            $id_delivery = $Delivery->upsertProgramacion($id_pedido, $data);
            $_SESSION['flash_success'] = 'Delivery programado correctamente.';
            header('Location: index.php?c=delivery&a=ver&id='.$id_delivery); exit;
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'No se pudo programar: ' . $e->getMessage();
            header('Location: index.php?c=delivery&a=programar&id_pedido='.$id_pedido); exit;
        }
    }

    public function ver() {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        $Delivery = $this->model('Delivery');
        $delivery = $Delivery->obtener($id);
        if (!$delivery) { $_SESSION['flash_error']='Delivery no encontrado.'; header('Location: index.php?c=delivery'); exit; }
        $this->render('delivery/ver', compact('delivery') + ['title'=>'Detalle Delivery']);
    }

   public function marcarEnRuta()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try {
            $Delivery = $this->model('Delivery');
            $Delivery->cambiarEstado($id, 'EN_RUTA', 'fecha_salida');
            $_SESSION['flash_success'] = 'Delivery marcado EN RUTA.';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'No se pudo marcar en ruta: ' . $e->getMessage();
        }
        header('Location: index.php?c=delivery&a=ver&id=' . $id); exit;
    }

    public function marcarEntregado()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try {
            $Delivery = $this->model('Delivery');
            $Delivery->cambiarEstado($id, 'ENTREGADO', 'fecha_entrega');
            $_SESSION['flash_success'] = 'Delivery marcado ENTREGADO.';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'No se pudo marcar entregado: ' . $e->getMessage();
        }
        header('Location: index.php?c=delivery&a=ver&id=' . $id); exit;
    }

    public function marcarFallido()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try {
            $Delivery = $this->model('Delivery');
            $Delivery->cambiarEstado($id, 'FALLIDO', 'fecha_fallo');
            $_SESSION['flash_success'] = 'Delivery marcado FALLIDO.';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'No se pudo marcar fallido: ' . $e->getMessage();
        }
        header('Location: index.php?c=delivery&a=ver&id=' . $id); exit;
    }
}}
