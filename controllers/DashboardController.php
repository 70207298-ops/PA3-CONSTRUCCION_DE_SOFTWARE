<?php
require_once __DIR__.'/../core/Auth.php';

final class DashboardController {
  public function index(): void {
    Auth::require();
    require __DIR__.'/../views/dashboard/index.php';
  }
}
