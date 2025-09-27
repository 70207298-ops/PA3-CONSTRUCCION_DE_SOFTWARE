<?php
require_once __DIR__.'/config/config.php';
require_once __DIR__.'/core/Database.php';

try {
  $pdo = Database::get()->pdo();
  // Crea la tabla si no existe (por si acaso)
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS usuarios (
      id_usuario INT AUTO_INCREMENT PRIMARY KEY,
      nombres VARCHAR(100) NOT NULL,
      apellidos VARCHAR(100) NOT NULL,
      username VARCHAR(50) NOT NULL UNIQUE,
      email VARCHAR(120) NOT NULL UNIQUE,
      password_hash VARCHAR(255) NOT NULL,
      rol ENUM('admin','vendedor','despacho') DEFAULT 'admin',
      estado TINYINT(1) DEFAULT 1,
      last_login DATETIME NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");

  // (Re)crear admin con password 'admin123'
  $hash = password_hash('admin123', PASSWORD_DEFAULT);
  $sql = "INSERT INTO usuarios (nombres, apellidos, username, email, password_hash, rol, estado)
          VALUES ('Administrador', 'Principal', 'admin', 'admin@local', :h, 'admin', 1)
          ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash), rol='admin', estado=1";
  $st = $pdo->prepare($sql);
  $st->execute([':h'=>$hash]);

  echo "Listo: admin / admin123 creado/actualizado.";
} catch (Throwable $e) {
  echo "Error: ".$e->getMessage();
}
