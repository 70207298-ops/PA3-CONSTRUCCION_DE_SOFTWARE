<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Kardex extends Model
{
    public function ultimosMovimientos(int $n=50): array
    {
        return $this->all("SELECT k.*,p.nombre as producto,l.nombre as local
                           FROM kardex k
                           JOIN producto p ON k.id_producto=p.id_producto
                           JOIN local l ON k.id_local=l.id_local
                           ORDER BY k.id_kardex DESC LIMIT ?",[$n]);
    }

    public function ingreso(array $data): void
    {
        $this->run("INSERT INTO kardex (id_local,id_producto,tipo_mov,cantidad,costo_unit,detalle,fecha_ingreso)
                    VALUES (:id_local,:id_producto,'INGRESO',:cantidad,:costo_unit,:detalle,:fecha_ingreso)",$data);
        $this->run("INSERT INTO stock_local (id_local,id_producto,stock) VALUES (:id_local,:id_producto,:cantidad)
                    ON DUPLICATE KEY UPDATE stock=stock+:cantidad",$data);
    }

    public function salida(array $data): void
    {
        $this->run("INSERT INTO kardex (id_local,id_producto,tipo_mov,cantidad,detalle)
                    VALUES (:id_local,:id_producto,'SALIDA',:cantidad,:detalle)",$data);
        $this->run("UPDATE stock_local SET stock=stock-:cantidad WHERE id_local=:id_local AND id_producto=:id_producto",$data);
    }
}
