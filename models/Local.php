<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Local extends Model
{
    public function listar(): array
    {
        return $this->all("SELECT * FROM local WHERE activo=1 ORDER BY nombre");
    }
}
