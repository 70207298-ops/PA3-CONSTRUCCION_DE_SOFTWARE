<?php

declare(strict_types=1);

if (!class_exists('ProductoController')) {
class ProductoController extends Controller
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
        $Producto = $this->model('Producto');
        $q = trim($_GET['q'] ?? '');
        $productos = method_exists($Producto,'listar') ? $Producto->listar($q) : [];
        $this->render('producto/index', compact('productos','q') + ['title'=>'Productos']);
    }

    public function crear()
    {
        $this->checkAuth();
        $Producto = $this->model('Producto');
        $categorias = method_exists($Producto,'listarCategorias') ? $Producto->listarCategorias() : [];
        $marcas     = method_exists($Producto,'listarMarcas') ? $Producto->listarMarcas() : [];
        $this->render('producto/crear', compact('categorias','marcas') + ['title'=>'Nuevo Producto']);
    }

    public function guardar()
    {
        $this->checkAuth();
        $data = [
            'id_categoria'   => (int)($_POST['id_categoria'] ?? 0),
            'id_marca'       => (int)($_POST['id_marca'] ?? 0),
            'nombre'         => trim($_POST['nombre'] ?? ''),
            'sku'            => trim($_POST['sku'] ?? ''),
            'unidad'         => trim($_POST['unidad'] ?? 'UND'),
            'costo_promedio' => (float)($_POST['costo_promedio'] ?? 0),
            'precio_mayorista'=> (float)($_POST['precio_mayorista'] ?? 0),
            'activo'         => (int)($_POST['activo'] ?? 1),
        ];
        try{
            $Producto = $this->model('Producto');
            $id = $Producto->crear($data);
            $_SESSION['flash_success'] = 'Producto creado (ID ' . $id . ').';
            header('Location: index.php?c=producto'); exit;
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo crear: ' . $e->getMessage();
            header('Location: index.php?c=producto&a=crear'); exit;
        }
    }

    public function editar()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        $Producto = $this->model('Producto');
        $producto = $Producto->obtener($id);
        $categorias = method_exists($Producto,'listarCategorias') ? $Producto->listarCategorias() : [];
        $marcas     = method_exists($Producto,'listarMarcas') ? $Producto->listarMarcas() : [];
        if (!$producto) { $_SESSION['flash_error']='Producto no encontrado.'; header('Location: index.php?c=producto'); exit; }
        $this->render('producto/editar', compact('producto','categorias','marcas') + ['title'=>'Editar Producto']);
    }

    public function actualizar()
    {
        $this->checkAuth();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'id_categoria'   => (int)($_POST['id_categoria'] ?? 0),
            'id_marca'       => (int)($_POST['id_marca'] ?? 0),
            'nombre'         => trim($_POST['nombre'] ?? ''),
            'sku'            => trim($_POST['sku'] ?? ''),
            'unidad'         => trim($_POST['unidad'] ?? 'UND'),
            'costo_promedio' => (float)($_POST['costo_promedio'] ?? 0),
            'precio_mayorista'=> (float)($_POST['precio_mayorista'] ?? 0),
            'activo'         => (int)($_POST['activo'] ?? 1),
        ];
        try{
            $Producto = $this->model('Producto');
            $Producto->actualizar($id,$data);
            $_SESSION['flash_success'] = 'Producto actualizado.';
            header('Location: index.php?c=producto'); exit;
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo actualizar: ' . $e->getMessage();
            header('Location: index.php?c=producto&a=editar&id='.$id); exit;
        }
    }

    public function eliminar()
    {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        try{
            $Producto = $this->model('Producto');
            if (method_exists($Producto,'cambiarEstado')) {
                $Producto->cambiarEstado($id, 0);
            } else {
                $Producto->eliminar($id);
            }
            $_SESSION['flash_success'] = 'Producto desactivado.';
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo desactivar: ' . $e->getMessage();
        }
        header('Location: index.php?c=producto'); exit;
    }

    public function stock()
    {
        $this->checkAuth();
        $id_producto = (int)($_GET['id'] ?? 0);
        $Producto = $this->model('Producto');
        $stock = method_exists($Producto,'stockPorLocal') ? $Producto->stockPorLocal($id_producto) : [];
        $producto = method_exists($Producto,'obtener') ? $Producto->obtener($id_producto) : null;
        $this->render('producto/stock', compact('stock','producto') + ['title'=>'Stock por Local']);
    }
}}
