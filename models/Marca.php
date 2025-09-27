<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Marca extends Model
{
    public function listar(): array
    {
        return $this->all("SELECT * FROM marca ORDER BY nombre");
    }
}
