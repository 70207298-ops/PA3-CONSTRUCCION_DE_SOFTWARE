<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Cliente extends Model
{
    public function listar(string $q=''): array
    {
        if ($q) {
            return $this->all("SELECT * FROM cliente WHERE nombres LIKE ? OR apellidos LIKE ? OR razon_social LIKE ? ORDER BY id_cliente DESC",
                ["%$q%","%$q%","%$q%"]);
        }
        return $this->all("SELECT * FROM cliente ORDER BY id_cliente DESC");
    }

    public function crear(array $data): int
    {
        $sql = "INSERT INTO cliente (tipo,nombres,apellidos,dni,razon_social,ruc,telefono,email,direccion,activo)
                VALUES (:tipo,:nombres,:apellidos,:dni,:razon_social,:ruc,:telefono,:email,:direccion,:activo)";
        $this->run($sql,$data);
        return (int)$this->lastId();
    }

    public function obtener(int $id): ?array
    {
        return $this->one("SELECT * FROM cliente WHERE id_cliente=?",[$id]);
    }

    public function actualizar(int $id, array $data): void
    {
        $data['id']=$id;
        $sql="UPDATE cliente SET tipo=:tipo,nombres=:nombres,apellidos=:apellidos,dni=:dni,
              razon_social=:razon_social,ruc=:ruc,telefono=:telefono,email=:email,direccion=:direccion,activo=:activo
              WHERE id_cliente=:id";
        $this->run($sql,$data);
    }

    public function eliminar(int $id): void
    {
        $this->run("DELETE FROM cliente WHERE id_cliente=?",[$id]);
    }

    public function cambiarEstado(int $id,int $estado): void
    {
        $this->run("UPDATE cliente SET activo=? WHERE id_cliente=?",[$estado,$id]);
    }
}
