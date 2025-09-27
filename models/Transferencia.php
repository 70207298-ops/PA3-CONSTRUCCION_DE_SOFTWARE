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
        // Obtener datos de la transferencia
        $transf = $this->obtener($id);
        if (!$transf) {
            throw new \Exception('Transferencia no encontrada');
        }

        // Verificar si la columna estado existe  
        $estado_actual = $transf['estado'] ?? 'REGISTRADA';
        if ($estado_actual !== 'REGISTRADA') {
            throw new \Exception("Transferencia ya procesada. Estado actual: {$estado_actual}");
        }

        // Obtener detalles
        $detalles = $this->detalles($id);
        if (empty($detalles)) {
            throw new \Exception('No hay productos en la transferencia');
        }
        
        // Generar salidas del local origen
        foreach ($detalles as $det) {
            // Verificar stock disponible
            $stock_disponible = $this->one("SELECT stock FROM stock_local 
                                           WHERE id_local=? AND id_producto=?", 
                                          [$transf['id_local_origen'], $det['id_producto']]);
            
            if (!$stock_disponible || (float)$stock_disponible['stock'] < (float)$det['cantidad']) {
                $stock_actual = $stock_disponible ? $stock_disponible['stock'] : 0;
                throw new \Exception("Stock insuficiente para {$det['nombre']}. Disponible: {$stock_actual}, Requerido: {$det['cantidad']}");
            }

            // Registrar salida en kardex (intentar primero, por si hay errores de tabla)
            try {
                $this->run("INSERT INTO kardex (id_local,id_producto,tipo_mov,cantidad,detalle,fecha_mov)
                           VALUES (?,?,'TRANSFERENCIA_OUT',?,?,NOW())",
                          [$transf['id_local_origen'], $det['id_producto'], $det['cantidad'], 
                           "Transferencia #{$id} enviada a local destino"]);
            } catch (\Exception $e) {
                throw new \Exception("Error al registrar kardex: " . $e->getMessage());
            }

            // Reducir stock del local origen  
            try {
                $this->run("UPDATE stock_local SET stock=stock-? 
                           WHERE id_local=? AND id_producto=?",
                          [$det['cantidad'], $transf['id_local_origen'], $det['id_producto']]);
            } catch (\Exception $e) {
                throw new \Exception("Error al actualizar stock: " . $e->getMessage());
            }
        }

        // Actualizar estado (usar ENVIADA como estado final)
        try {
            $this->run("UPDATE transferencia SET estado='ENVIADA' WHERE id_transferencia=?",[$id]);
        } catch (\Exception $e) {
            // Si la columna estado no existe, agregar un comentario temporal
            $this->run("UPDATE transferencia SET observacion=CONCAT(COALESCE(observacion,''), ' [ENVIADA]') WHERE id_transferencia=?",[$id]);
        }
    }

    public function recibir(int $id): void
    {
        // Obtener datos de la transferencia
        $transf = $this->obtener($id);
        if (!$transf) {
            throw new \Exception('Transferencia no encontrada');
        }

        // Verificar estado (flexible si no existe la columna)
        $estado_actual = $transf['estado'] ?? null;
        if ($estado_actual !== null && $estado_actual !== 'ENVIADA') {
            throw new \Exception("Transferencia no enviada. Estado actual: {$estado_actual}");
        }

        // Obtener detalles
        $detalles = $this->detalles($id);
        if (empty($detalles)) {
            throw new \Exception('No hay productos en la transferencia');
        }
        
        // Generar ingresos en el local destino
        foreach ($detalles as $det) {
            // Registrar ingreso en kardex
            try {
                $this->run("INSERT INTO kardex (id_local,id_producto,tipo_mov,cantidad,detalle,fecha_mov)
                           VALUES (?,?,'TRANSFERENCIA_IN',?,?,NOW())",
                          [$transf['id_local_destino'], $det['id_producto'], $det['cantidad'], 
                           "Transferencia #{$id} recibida desde local origen"]);
            } catch (\Exception $e) {
                throw new \Exception("Error al registrar kardex: " . $e->getMessage());
            }

            // Aumentar stock del local destino
            try {
                $this->run("INSERT INTO stock_local (id_local,id_producto,stock) 
                           VALUES (?,?,?)
                           ON DUPLICATE KEY UPDATE stock=stock+?",
                          [$transf['id_local_destino'], $det['id_producto'], 
                           $det['cantidad'], $det['cantidad']]);
            } catch (\Exception $e) {
                throw new \Exception("Error al actualizar stock: " . $e->getMessage());
            }
        }

        // Actualizar estado (flexible si no existe la columna)
        try {
            $this->run("UPDATE transferencia SET estado='RECIBIDA' WHERE id_transferencia=?",[$id]);
        } catch (\Exception $e) {
            // Si la columna estado no existe, agregar un comentario temporal
            $this->run("UPDATE transferencia SET observacion=CONCAT(COALESCE(observacion,''), ' [RECIBIDA]') WHERE id_transferencia=?",[$id]);
        }
    }

    // Método simplificado: procesa la transferencia directamente (quita stock origen, agrega stock destino)
    public function procesar(int $id): void
    {
        // Obtener datos de la transferencia
        $transf = $this->obtener($id);
        if (!$transf) {
            throw new \Exception('Transferencia no encontrada');
        }

        // Solo procesar si está en estado REGISTRADA
        $estado_actual = $transf['estado'] ?? 'REGISTRADA';
        if ($estado_actual !== 'REGISTRADA') {
            throw new \Exception("Transferencia ya procesada. Estado actual: {$estado_actual}");
        }

        // Obtener detalles
        $detalles = $this->detalles($id);
        if (empty($detalles)) {
            throw new \Exception('No hay productos en la transferencia');
        }
        
        // Procesar cada producto
        foreach ($detalles as $det) {
            // 1. Verificar stock disponible en origen
            $stock_disponible = $this->one("SELECT stock FROM stock_local 
                                           WHERE id_local=? AND id_producto=?", 
                                          [$transf['id_local_origen'], $det['id_producto']]);
            
            if (!$stock_disponible || (float)$stock_disponible['stock'] < (float)$det['cantidad']) {
                $stock_actual = $stock_disponible ? $stock_disponible['stock'] : 0;
                throw new \Exception("Stock insuficiente para {$det['nombre']}. Disponible: {$stock_actual}, Requerido: {$det['cantidad']}");
            }

            // 2. Registrar salida del local origen en kardex
            $this->run("INSERT INTO kardex (id_local,id_producto,tipo_mov,cantidad,detalle,fecha_mov)
                       VALUES (?,?,'TRANSFERENCIA_OUT',?,?,NOW())",
                      [$transf['id_local_origen'], $det['id_producto'], $det['cantidad'], 
                       "Transferencia #{$id} - salida del local origen"]);

            // 3. Reducir stock del local origen  
            $this->run("UPDATE stock_local SET stock=stock-? 
                       WHERE id_local=? AND id_producto=?",
                      [$det['cantidad'], $transf['id_local_origen'], $det['id_producto']]);

            // 4. Registrar ingreso en local destino en kardex
            $this->run("INSERT INTO kardex (id_local,id_producto,tipo_mov,cantidad,detalle,fecha_mov)
                       VALUES (?,?,'TRANSFERENCIA_IN',?,?,NOW())",
                      [$transf['id_local_destino'], $det['id_producto'], $det['cantidad'], 
                       "Transferencia #{$id} - ingreso al local destino"]);

            // 5. Aumentar stock del local destino
            $this->run("INSERT INTO stock_local (id_local,id_producto,stock) 
                       VALUES (?,?,?)
                       ON DUPLICATE KEY UPDATE stock=stock+?",
                      [$transf['id_local_destino'], $det['id_producto'], 
                       $det['cantidad'], $det['cantidad']]);
        }

        // Actualizar estado a PROCESADA
        $this->run("UPDATE transferencia SET estado='PROCESADA' WHERE id_transferencia=?",[$id]);
    }

    public function anular(int $id): void
    {
        // Obtener datos de la transferencia
        $transf = $this->obtener($id);
        if (!$transf) {
            throw new \Exception('Transferencia no encontrada');
        }

        $estado_actual = $transf['estado'] ?? 'REGISTRADA';
        if ($estado_actual === 'ANULADA') {
            throw new \Exception('La transferencia ya está anulada');
        }

        // Si está PROCESADA, revertir los movimientos
        if ($estado_actual === 'PROCESADA') {
            $detalles = $this->detalles($id);
            
            foreach ($detalles as $det) {
                // Revertir: devolver stock al origen
                $this->run("INSERT INTO kardex (id_local,id_producto,tipo_mov,cantidad,detalle,fecha_mov)
                           VALUES (?,?,'TRANSFERENCIA_IN',?,?,NOW())",
                          [$transf['id_local_origen'], $det['id_producto'], $det['cantidad'], 
                           "Anulación transferencia #{$id} - devolución a origen"]);
                
                $this->run("INSERT INTO stock_local (id_local,id_producto,stock) 
                           VALUES (?,?,?)
                           ON DUPLICATE KEY UPDATE stock=stock+?",
                          [$transf['id_local_origen'], $det['id_producto'], 
                           $det['cantidad'], $det['cantidad']]);

                // Revertir: quitar stock del destino
                $this->run("INSERT INTO kardex (id_local,id_producto,tipo_mov,cantidad,detalle,fecha_mov)
                           VALUES (?,?,'TRANSFERENCIA_OUT',?,?,NOW())",
                          [$transf['id_local_destino'], $det['id_producto'], $det['cantidad'], 
                           "Anulación transferencia #{$id} - retiro de destino"]);
                
                $this->run("UPDATE stock_local SET stock=stock-? 
                           WHERE id_local=? AND id_producto=?",
                          [$det['cantidad'], $transf['id_local_destino'], $det['id_producto']]);
            }
        }

        // Actualizar estado
        $this->run("UPDATE transferencia SET estado='ANULADA' WHERE id_transferencia=?",[$id]);
    }
}
