<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Transferencia extends Model
{
    public function listar(): array
    {
        return $this->all("SELECT t.*,lo.nombre as origen,ld.nombre as destino
                           FROM transferencia t
                           JOIN local lo ON t.id_local_origen=lo.id_local
                           JOIN local ld ON t.id_local_destino=ld.id_local
                           ORDER BY t.id_transferencia DESC");
    }

    public function crear(array $transf,array $items): int
    {
        $this->run("INSERT INTO transferencia (id_local_origen,id_local_destino,observacion)
                    VALUES (:id_local_origen,:id_local_destino,:observacion)",$transf);
        $id=(int)$this->lastId();
        foreach($items as $it){
            $it['id_transferencia']=$id;
            $this->run("INSERT INTO transferencia_detalle (id_transferencia,id_producto,cantidad)
                        VALUES (:id_transferencia,:id_producto,:cantidad)",$it);
        }
        return $id;
    }

    public function obtener(int $id): ?array
    {
        return $this->one("SELECT * FROM transferencia WHERE id_transferencia=?",[$id]);
    }

    public function detalles(int $id): array
    {
        return $this->all("SELECT d.*,p.nombre FROM transferencia_detalle d JOIN producto p ON d.id_producto=p.id_producto WHERE d.id_transferencia=?",[$id]);
    }

    public function enviar(int $id): void
    {
        $this->run("UPDATE transferencia SET estado='ENVIADA' WHERE id_transferencia=?",[$id]);
    }

    public function recibir(int $id): void
    {
        $this->run("UPDATE transferencia SET estado='RECIBIDA' WHERE id_transferencia=?",[$id]);
    }

    public function anular(int $id): void
    {
        $this->run("UPDATE transferencia SET estado='ANULADA' WHERE id_transferencia=?",[$id]);
    }
}
