<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Producto extends Model
{
    public function listar(string $q=''): array
    {
        if ($q) {
            return $this->all("SELECT p.*, c.nombre AS categoria, m.nombre AS marca
                               FROM producto p
                               JOIN categoria c ON p.id_categoria=c.id_categoria
                               JOIN marca m ON p.id_marca=m.id_marca
                               WHERE p.nombre LIKE ? OR p.sku LIKE ?
                               ORDER BY p.id_producto DESC", ["%$q%","%$q%"]);
        }
        return $this->all("SELECT p.*, c.nombre AS categoria, m.nombre AS marca
                           FROM producto p
                           JOIN categoria c ON p.id_categoria=c.id_categoria
                           JOIN marca m ON p.id_marca=m.id_marca
                           ORDER BY p.id_producto DESC");
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO producto (id_categoria,id_marca,nombre,sku,unidad,costo_promedio,precio_mayorista,activo)
                VALUES (:id_categoria,:id_marca,:nombre,:sku,:unidad,:costo_promedio,:precio_mayorista,:activo)";
        $this->run($sql, $data);
        return (int)$this->lastId();
    }

    public function obtener(int $id): ?array
    {
        return $this->one("SELECT * FROM producto WHERE id_producto=?", [$id]);
    }

    public function actualizar(int $id, array $data): void
    {
        $data['id'] = $id;
        $sql="UPDATE producto SET id_categoria=:id_categoria,id_marca=:id_marca,nombre=:nombre,sku=:sku,unidad=:unidad,
              costo_promedio=:costo_promedio,precio_mayorista=:precio_mayorista,activo=:activo
              WHERE id_producto=:id";
        $this->run($sql,$data);
    }

    public function eliminar(int $id): void
    {
        $this->run("DELETE FROM producto WHERE id_producto=?", [$id]);
    }

    public function cambiarEstado(int $id, int $estado): void
    {
        $this->run("UPDATE producto SET activo=? WHERE id_producto=?", [$estado,$id]);
    }

    public function listarCategorias(): array
    {
        return $this->all("SELECT * FROM categoria ORDER BY nombre");
    }

    public function listarMarcas(): array
    {
        return $this->all("SELECT * FROM marca ORDER BY nombre");
    }

    // (1) Stock por local de un producto específico (para mostrar grilla/tooltip)
    public function stockPorLocal(int $id_producto): array
    {
        return $this->all("SELECT l.id_local, l.nombre, s.stock
                           FROM stock_local s
                           JOIN local l ON s.id_local=l.id_local
                           WHERE s.id_producto=?
                           ORDER BY l.nombre", [$id_producto]);
    }

    // (2) Listar SOLO productos con stock>0 en el local indicado (para el <select>)
    public function listarPorLocal(int $id_local): array
    {
        $sql = "SELECT p.id_producto, p.nombre, p.sku, p.unidad,
                       s.stock, m.nombre AS marca, c.nombre AS categoria
                FROM stock_local s
                JOIN producto p  ON p.id_producto = s.id_producto
                JOIN marca m     ON p.id_marca = m.id_marca
                JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE s.id_local = ? AND p.activo = 1 AND s.stock > 0
                ORDER BY p.nombre";
        $st = $this->db->prepare($sql);
        $st->execute([$id_local]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    // (3) Obtener stock puntual (para validar al guardar)
    public function stockEnLocal(int $id_local, int $id_producto): ?float
    {
        $v = $this->one("SELECT stock FROM stock_local
                         WHERE id_local=? AND id_producto=?", [$id_local,$id_producto]);
        if (!$v) return null;
        return (float)$v['stock'];
    }
}
