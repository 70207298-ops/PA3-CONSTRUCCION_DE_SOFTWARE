<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Usuario extends Model
{
    /* ===== Autenticación / bootstrap ===== */
    public function asegurarAdmin(): void
    {
        $row = $this->one("SELECT * FROM usuario WHERE usuario='admin' LIMIT 1");
        if (!$row) {
            $hash = password_hash('admin123', PASSWORD_BCRYPT);
            $this->run(
                "INSERT INTO usuario (usuario, password, nombres, rol, activo)
                 VALUES ('admin', ?, 'Administrador', 'admin', 1)",
                [$hash]
            );
        }
    }

    public function login(string $usuario, string $password): ?array
    {
        $row = $this->one("SELECT * FROM usuario WHERE usuario=? AND activo=1", [$usuario]);
        if ($row && password_verify($password, $row['password'])) {
            return $row; // devuelve id_usuario, usuario, nombres, rol, etc.
        }
        return null;
    }

    public function findByUsuario(string $usuario): ?array
    {
        return $this->one("SELECT * FROM usuario WHERE usuario=?", [$usuario]);
    }

    /* ===== CRUD para Configuración → Usuarios ===== */
    public function listar(string $q=''): array
    {
        if ($q) {
            $like = "%$q%";
            return $this->all(
                "SELECT * FROM usuario
                 WHERE usuario LIKE ? OR nombres LIKE ?
                 ORDER BY id_usuario DESC",
                [$like, $like]
            );
        }
        return $this->all("SELECT * FROM usuario ORDER BY id_usuario DESC");
    }

    public function obtener(int $id): ?array
    {
        return $this->one("SELECT * FROM usuario WHERE id_usuario=?", [$id]);
    }

    // Crear con contraseña
    public function crear(array $data): int
    {
        if (empty($data['password'] ?? '')) {
            throw new \InvalidArgumentException('La contraseña es obligatoria.');
        }
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $this->run(
            "INSERT INTO usuario (usuario, nombres, rol, password, activo)
             VALUES (:usuario, :nombres, :rol, :password, :activo)",
            [
                'usuario'  => trim($data['usuario']),
                'nombres'  => trim($data['nombres']),
                'rol'      => $data['rol'] ?? 'operador',
                'password' => $hash,
                'activo'   => (int)($data['activo'] ?? 1),
            ]
        );
        return (int)$this->lastId();
    }

    // Editar datos (sin contraseña)
    public function actualizar(int $id, array $data): void
    {
        $this->run(
            "UPDATE usuario
               SET usuario=:usuario, nombres=:nombres, rol=:rol, activo=:activo
             WHERE id_usuario=:id",
            [
                'usuario' => trim($data['usuario']),
                'nombres' => trim($data['nombres']),
                'rol'     => $data['rol'] ?? 'operador',
                'activo'  => (int)($data['activo'] ?? 1),
                'id'      => $id,
            ]
        );
    }

    public function cambiarPassword(int $id, string $newPass): void
    {
        $hash = password_hash($newPass, PASSWORD_BCRYPT);
        $this->run("UPDATE usuario SET password=? WHERE id_usuario=?", [$hash, $id]);
    }

    public function cambiarEstado(int $id, int $estado): void
    {
        $this->run("UPDATE usuario SET activo=? WHERE id_usuario=?", [$estado, $id]);
    }
}
