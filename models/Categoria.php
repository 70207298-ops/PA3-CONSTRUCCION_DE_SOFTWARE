<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Categoria extends Model
{
    public function listar(): array
    {
        return $this->all("SELECT * FROM categoria ORDER BY nombre");
    }
}
