<?php
final class Auth {
  public static function check(): bool {
    return !empty($_SESSION['user']);
  }
  public static function require(): void {
    if (!self::check()) { header('Location: index.php?c=auth&a=loginForm'); exit; }
  }
  public static function requireRole(array $roles): void {
    self::require();
    if (!in_array($_SESSION['user']['rol'] ?? '', $roles, true)) {
      http_response_code(403); exit('Acceso denegado');
    }
  }
}
