<?php
declare(strict_types=1);

if (!class_exists('UsuarioController')) {
class UsuarioController extends Controller
{
    private function checkAuth(){ if (empty($_SESSION['user'])) { header('Location: index.php?c=auth&a=login'); exit; } }
    private function checkAdmin(){
        $this->checkAuth();
        if (($_SESSION['user']['rol'] ?? '') !== 'admin') {
            $_SESSION['flash_error'] = 'Solo ADMIN puede acceder.';
            header('Location: index.php'); exit;
        }
    }

    public function index(){
        $this->checkAdmin();
        $Usuario = $this->model('Usuario');
        $q = trim($_GET['q'] ?? '');
        $usuarios = $Usuario->listar($q);
        $this->render('usuario/index', compact('usuarios','q') + ['title'=>'Usuarios']);
    }

    public function crear(){
        $this->checkAdmin();
        $this->render('usuario/crear', ['title'=>'Nuevo Usuario']);
    }

    public function guardar(){
        $this->checkAdmin();
        $data = [
            'usuario'  => $_POST['usuario']  ?? '',
            'nombres'  => $_POST['nombres']  ?? '',
            'rol'      => $_POST['rol']      ?? 'operador',
            'password' => $_POST['password'] ?? '',
            'activo'   => (int)($_POST['activo'] ?? 1),
        ];
        try{
            if (strlen($data['password']) < 6) throw new \Exception('La contraseña debe tener al menos 6 caracteres.');
            $id = $this->model('Usuario')->crear($data);
            $_SESSION['flash_success'] = "Usuario creado (ID $id).";
            header('Location: index.php?c=usuario'); exit;
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo crear: ' . $e->getMessage();
            header('Location: index.php?c=usuario&a=crear'); exit;
        }
    }

    public function editar(){
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $usuario = $this->model('Usuario')->obtener($id);
        if (!$usuario){ $_SESSION['flash_error']='Usuario no encontrado.'; header('Location: index.php?c=usuario'); exit; }
        $this->render('usuario/editar', compact('usuario') + ['title'=>'Editar Usuario']);
    }

    public function actualizar(){
        $this->checkAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'usuario' => $_POST['usuario'] ?? '',
            'nombres' => $_POST['nombres'] ?? '',
            'rol'     => $_POST['rol']     ?? 'operador',
            'activo'  => (int)($_POST['activo'] ?? 1),
        ];
        try{
            $this->model('Usuario')->actualizar($id, $data);
            $_SESSION['flash_success'] = 'Usuario actualizado.';
            header('Location: index.php?c=usuario'); exit;
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo actualizar: ' . $e->getMessage();
            header('Location: index.php?c=usuario&a=editar&id='.$id); exit;
        }
    }

    public function clave(){
        $this->checkAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $usuario = $this->model('Usuario')->obtener($id);
        if (!$usuario){ $_SESSION['flash_error']='Usuario no encontrado.'; header('Location: index.php?c=usuario'); exit; }
        $this->render('usuario/clave', compact('usuario') + ['title'=>'Cambiar Contraseña']);
    }

    public function actualizarClave(){
        $this->checkAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $pass  = (string)($_POST['password']  ?? '');
        $pass2 = (string)($_POST['password2'] ?? '');
        try{
            if ($pass !== $pass2) throw new \Exception('Las contraseñas no coinciden.');
            if (strlen($pass) < 6) throw new \Exception('Mínimo 6 caracteres.');
            $this->model('Usuario')->cambiarPassword($id, $pass);
            $_SESSION['flash_success'] = 'Contraseña actualizada.';
            header('Location: index.php?c=usuario'); exit;
        }catch(\Throwable $e){
            $_SESSION['flash_error'] = 'No se pudo actualizar: ' . $e->getMessage();
            header('Location: index.php?c=usuario&a=clave&id='.$id); exit;
        }
    }

    public function activar(){    $this->checkAdmin(); $id=(int)($_GET['id']??0); $this->model('Usuario')->cambiarEstado($id,1); $_SESSION['flash_success']='Usuario activado.';   header('Location: index.php?c=usuario'); exit; }
    public function desactivar(){ $this->checkAdmin(); $id=(int)($_GET['id']??0); $this->model('Usuario')->cambiarEstado($id,0); $_SESSION['flash_success']='Usuario desactivado.'; header('Location: index.php?c=usuario'); exit; }
}}
