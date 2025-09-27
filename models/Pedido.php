<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Pedido extends Model
{
    public function listar(string $estado=''): array
    {
        $sql="SELECT p.*, c.nombres,c.apellidos,c.razon_social,l.nombre as local
              FROM pedido p
              JOIN cliente c ON p.id_cliente=c.id_cliente
              JOIN local l ON p.id_local_salida=l.id_local";
        $params=[];
        if ($estado) {
            $sql.=" WHERE p.estado=?";
            $params[]=$estado;
        }
        $sql.=" ORDER BY p.id_pedido DESC";
        return $this->all($sql,$params);
    }

    public function crear(array $pedido,array $items): int
    {
        $this->run("INSERT INTO pedido (id_cliente,id_local_salida,canal_venta,observacion,total_bruto,total_descuento,total_neto)
                    VALUES (:id_cliente,:id_local_salida,:canal_venta,:observacion,:total_bruto,:total_descuento,:total_neto)",$pedido);
        $id=(int)$this->lastId();
        foreach($items as $it){
            $it['id_pedido']=$id;
            $this->run("INSERT INTO pedido_detalle (id_pedido,id_producto,cantidad,precio_unit,descuento,subtotal)
                        VALUES (:id_pedido,:id_producto,:cantidad,:precio_unit,:descuento,:subtotal)",$it);
        }
        return $id;
    }

    public function obtener(int $id): ?array
    {
        return $this->one("SELECT * FROM pedido WHERE id_pedido=?",[$id]);
    }

    public function detalles(int $id): array
    {
        return $this->all("SELECT d.*, p.nombre FROM pedido_detalle d JOIN producto p ON d.id_producto=p.id_producto WHERE d.id_pedido=?",[$id]);
    }

    public function cambiarEstado(int $id,string $estado): void
    {
        $this->run("UPDATE pedido SET estado=? WHERE id_pedido=?",[$estado,$id]);
    }

    public function despachar(int $id): void
    {
        // rebajar stock (simplificado)
        $items=$this->detalles($id);
        foreach($items as $it){
            $this->run("UPDATE stock_local SET stock=stock-? WHERE id_producto=? AND id_local=(SELECT id_local_salida FROM pedido WHERE id_pedido=?)",
                [$it['cantidad'],$it['id_producto'],$id]);
        }
        $this->cambiarEstado($id,'DESPACHADO');
    }

    public function anular(int $id): void
    {
        $this->cambiarEstado($id,'ANULADO');
    }
}
