<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Delivery extends Model
{

    public function upsertProgramacion(int $id_pedido, array $data): int
    {
        $ex = $this->one("SELECT id_delivery FROM delivery WHERE id_pedido=?", [$id_pedido]);

        if ($ex) {
   
            $data['id_delivery'] = (int)$ex['id_delivery'];
            $sql = "UPDATE delivery
                       SET direccion_envio = :direccion_envio,
                           contacto        = :contacto,
                           telefono        = :telefono,
                           fecha_programa  = :fecha_programa,
                           observacion     = :observacion
                     WHERE id_delivery    = :id_delivery";
            $this->run($sql, $data);
            return (int)$ex['id_delivery'];
        } else {
    
            $sql = "INSERT INTO delivery
                        (id_pedido, direccion_envio, contacto, telefono, fecha_programa, observacion, estado)
                    VALUES
                        (?, ?, ?, ?, ?, ?, 'PENDIENTE')";
            $this->run($sql, [
                $id_pedido,
                $data['direccion_envio'] ?? '',
                $data['contacto']        ?? null,
                $data['telefono']        ?? null,
                $data['fecha_programa']  ?? null,
                $data['observacion']     ?? null
            ]);
            return (int)$this->lastId();
        }
    }

    
    public function programar(array $data): int
    {
  
        $id_pedido = (int)($data['id_pedido'] ?? 0);
        if ($id_pedido <= 0) {
            throw new InvalidArgumentException('id_pedido inválido');
        }
        return $this->upsertProgramacion($id_pedido, [
            'direccion_envio' => $data['direccion_envio'] ?? '',
            'contacto'        => $data['contacto'] ?? null,
            'telefono'        => $data['telefono'] ?? null,
            'fecha_programa'  => $data['fecha_programa'] ?? null,
            'observacion'     => $data['observacion'] ?? null,
        ]);
    }


    public function obtener(int $id_delivery): ?array
    {
        $sql = "SELECT d.*,
                       p.id_pedido, p.id_local_salida, p.canal_venta, p.fecha_pedido,
                       c.razon_social, c.nombres, c.apellidos,
                       l.nombre AS local_salida
                  FROM delivery d
                  JOIN pedido  p ON p.id_pedido = d.id_pedido
                  JOIN cliente c ON c.id_cliente= p.id_cliente
                  JOIN local   l ON l.id_local  = p.id_local_salida
                 WHERE d.id_delivery = ?";
        return $this->one($sql, [$id_delivery]);
    }

    public function obtenerPorPedido(int $id_pedido): ?array
    {
        return $this->one("SELECT * FROM delivery WHERE id_pedido=?", [$id_pedido]);
    }


    public function listar(?string $estado = null): array
    {
        $sql = "SELECT d.*, p.id_pedido,
                       c.razon_social, c.nombres, c.apellidos,
                       l.nombre AS local_salida
                  FROM delivery d
                  JOIN pedido  p ON p.id_pedido = d.id_pedido
                  JOIN cliente c ON c.id_cliente= p.id_cliente
                  JOIN local   l ON l.id_local  = p.id_local_salida";
        $args = [];
        if ($estado) { $sql .= " WHERE d.estado=?"; $args[] = $estado; }
        $sql .= " ORDER BY d.id_delivery DESC";
        return $this->all($sql, $args);
    }

    public function cambiarEstado(int $id_delivery, string $nuevoEstado, ?string $campoFecha = null): void
    {
        $params = [
            ':estado' => $nuevoEstado,
            ':id'     => $id_delivery,
        ];

        if ($campoFecha) {
            $sql = "UPDATE delivery SET estado = :estado, {$campoFecha} = NOW() WHERE id_delivery = :id";
        } else {
            $sql = "UPDATE delivery SET estado = :estado WHERE id_delivery = :id";
        }

        $this->run($sql, $params);
    }
}
