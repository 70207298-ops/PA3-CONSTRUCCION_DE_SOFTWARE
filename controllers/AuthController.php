<?php

declare(strict_types=1);

if (!class_exists('AuthController')) {
class AuthController extends Controller
{
    public function login()
    {
        if (!empty($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }
        $this->render('auth/login', ['title' => 'Iniciar sesión']);
    }

    public function doLogin()
    {
        $usuario  = trim($_POST['usuario'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($usuario === '' || $password === '') {
            $_SESSION['flash_error'] = 'Ingrese usuario y contraseña.';
            header('Location: index.php?c=auth&a=login');
            exit;
        }

        try {
            $Usuario = $this->model('Usuario');
            // Garantiza admin por defecto (admin / admin123) si no existe
            if (method_exists($Usuario, 'asegurarAdmin')) {
                $Usuario->asegurarAdmin();
            }
            $user = null;
            if (method_exists($Usuario, 'login')) {
                $user = $Usuario->login($usuario, $password);
            } elseif (method_exists($Usuario, 'findByUsuario')) {
                $row = $Usuario->findByUsuario($usuario);
                if ($row && password_verify($password, $row['password'])) {
                    $user = $row;
                }
            }

            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id_usuario'] ?? $user['id'] ?? null,
                    'usuario' => $user['usuario'] ?? $usuario,
                    'nombres' => $user['nombres'] ?? 'Administrador',
                    'rol' => $user['rol'] ?? 'admin'
                ];
                $_SESSION['flash_success'] = 'Bienvenido(a).';
                header('Location: index.php');
            } else {
                $_SESSION['flash_error'] = 'Credenciales incorrectas.';
                header('Location: index.php?c=auth&a=login');
            }
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Error de autenticación: ' . $e->getMessage();
            header('Location: index.php?c=auth&a=login');
        }
        exit;
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: index.php?c=auth&a=login');
        exit;
    }
}}
