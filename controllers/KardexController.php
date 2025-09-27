<?php

declare(strict_types=1);

if (!class_exists('KardexController')) {
class KardexController extends Controller
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
        $Kardex = $this->model('Kardex');
        $movs = method_exists($Kardex,'ultimosMovimientos') ? $Kardex->ultimosMovimientos(50) : [];
        $this->render('kardex/index', compact('movs') + ['title'=>'Kardex']);
    }

    public function ingreso()
    {
        $this->checkAuth();
        $Producto = $this->model('Producto');
        $Local    = $this->model('Local');
        $productos= method_exists($Producto,'listar') ? $Producto->listar('') : [];
        $locales  = method_exists($Local,'listar') ? $Local->listar() : [];
        $this->render('kardex/ingreso', compact('productos','locales') + ['title'=>'Ingreso de Stock']);
    }

    public function guardarIngreso()
    {
        $this->checkAuth();
        $id_local    = (int)($_POST['id_local'] ?? 0);
        $id_producto = (int)($_POST['id_producto'] ?? 0);
        $cantidad    = (float)($_POST['cantidad'] ?? 0);
        $costo_unit  = (float)($_POST['costo_unit'] ?? 0);
        $detalle     = trim($_POST['detalle'] ?? '');
        $fecha_ing   = $_POST['fecha_ingreso'] ?? null;

        if ($id_local<=0 || $id_producto<=0 || $cantidad<=0) {
            $_SESSION['flash_error']='Complete local, producto y cantidad.';
            header('Location: index.php?c=kardex&a=ingreso'); exit;
        }

        try{
            $Kardex = $this->model('Kardex');
            $Kardex->ingreso([
                'id_local'=>$id_local,'id_producto'=>$id_producto,'cantidad'=>$cantidad,
                'costo_unit'=>$costo_unit,'detalle'=>$detalle,'fecha_ingreso'=>$fecha_ing
            ]);
            $_SESSION['flash_success']='Ingreso registrado.';
            header('Location: index.php?c=kardex'); exit;
        }catch(\Throwable $e){
            $_SESSION['flash_error']='No se pudo registrar: ' . $e->getMessage();
            header('Location: index.php?c=kardex&a=ingreso'); exit;
        }
    }

    public function salida()
    {
        $this->checkAuth();
        $Producto = $this->model('Producto');
        $Local    = $this->model('Local');
        $productos= method_exists($Producto,'listar') ? $Producto->listar('') : [];
        $locales  = method_exists($Local,'listar') ? $Local->listar() : [];
        $this->render('kardex/salida', compact('productos','locales') + ['title'=>'Salida de Stock']);
    }

    public function guardarSalida()
    {
        $this->checkAuth();
        $id_local    = (int)($_POST['id_local'] ?? 0);
        $id_producto = (int)($_POST['id_producto'] ?? 0);
        $cantidad    = (float)($_POST['cantidad'] ?? 0);
        $detalle     = trim($_POST['detalle'] ?? '');

        if ($id_local<=0 || $id_producto<=0 || $cantidad<=0) {
            $_SESSION['flash_error']='Complete local, producto y cantidad.';
            header('Location: index.php?c=kardex&a=salida'); exit;
        }

        try{
            $Kardex = $this->model('Kardex');
            $Kardex->salida([
                'id_local'=>$id_local,'id_producto'=>$id_producto,'cantidad'=>$cantidad,
                'detalle'=>$detalle
            ]);
            $_SESSION['flash_success']='Salida registrada.';
            header('Location: index.php?c=kardex'); exit;
        }catch(\Throwable $e){
            $_SESSION['flash_error']='No se pudo registrar: ' . $e->getMessage();
            header('Location: index.php?c=kardex&a=salida'); exit;
        }
    }
}}
