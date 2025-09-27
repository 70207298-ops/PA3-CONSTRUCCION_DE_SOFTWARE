<?php

declare(strict_types=1);

if (!class_exists('ClienteController')) {
class ClienteController extends Controller
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
        $Cliente = $this->model('Cliente');
        $q = trim($_GET['q'] ?? '');
        $clientes = method_exists($Cliente,'listar')
            ? $Cliente->listar($q)
            : [];
        $this->render('cliente/index', compact('clientes','q') + ['title'=>'Clientes']);
    }

    public function crear()
    {
        $this->checkAuth();
        $this->render('cliente/crear', ['title'=>'Nuevo Cliente']);
    }

    public function guardar()
    {
        $this->checkAuth();
        $data = [
            'tipo'         => $_POST['tipo'] ?? 'NATURAL',
            'nombres'      => trim($_POST['nombres'] ?? ''),
            'apellidos'    => trim($_POST['apellidos'] ?? ''),
            'dni'          => trim($_POST['dni'] ?? ''),
            'razon_social' => trim($_POST['razon_social'] ?? ''),
            'ruc'          => trim($_POST['ruc'] ?? ''),
            'telefono'     => trim($_POST['telefono'] ?? ''),
            'email'        => trim($_POST['email'] ?? ''),
            'direccion'    => trim($_POST['direccion'] ?? ''),
            'activo'       => (int)($_POST['activo'] ?? 1),
        ];
        try{
            $Cliente = $this->model('Cliente');
            $id = $Cliente->crear($data);
            $_SESSION['flash_success'] = 'Cliente creado (ID ' . $id . ').';
            header('Location: index.php?c=cliente&a=index'); exit;
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo crear: ' . $e->getMessage();
            header('Location: index.php?c=cliente&a=crear'); exit;
        }
    }

    public function editar()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        $Cliente = $this->model('Cliente');
        $cliente = $Cliente->obtener($id);
        if (!$cliente) { $_SESSION['flash_error']='Cliente no encontrado.'; header('Location: index.php?c=cliente'); exit; }
        $this->render('cliente/editar', compact('cliente') + ['title'=>'Editar Cliente']);
    }

    public function actualizar()
    {
        $this->checkAuth();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'tipo'         => $_POST['tipo'] ?? 'NATURAL',
            'nombres'      => trim($_POST['nombres'] ?? ''),
            'apellidos'    => trim($_POST['apellidos'] ?? ''),
            'dni'          => trim($_POST['dni'] ?? ''),
            'razon_social' => trim($_POST['razon_social'] ?? ''),
            'ruc'          => trim($_POST['ruc'] ?? ''),
            'telefono'     => trim($_POST['telefono'] ?? ''),
            'email'        => trim($_POST['email'] ?? ''),
            'direccion'    => trim($_POST['direccion'] ?? ''),
            'activo'       => (int)($_POST['activo'] ?? 1),
        ];
        try{
            $Cliente = $this->model('Cliente');
            $Cliente->actualizar($id,$data);
            $_SESSION['flash_success'] = 'Cliente actualizado.';
            header('Location: index.php?c=cliente'); exit;
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo actualizar: ' . $e->getMessage();
            header('Location: index.php?c=cliente&a=editar&id='.$id); exit;
        }
    }

    public function eliminar()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try{
            $Cliente = $this->model('Cliente');
            if (method_exists($Cliente,'cambiarEstado')) {
                $Cliente->cambiarEstado($id, 0);
            } else {
                $Cliente->eliminar($id);
            }
            $_SESSION['flash_success'] = 'Cliente desactivado.';
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo desactivar: ' . $e->getMessage();
        }
        header('Location: index.php?c=cliente'); exit;
    }
}}
