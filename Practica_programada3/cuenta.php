<?php

$transacciones = [];

function registrarTransaccion($id, $descripcion, $monto) {
    global $transacciones;
    array_push($transacciones, [
        'id' => $id,
        'descripcion' => $descripcion,
        'monto' => $monto
    ]);
}

function generarEstadoDeCuenta() {
    global $transacciones;
    
    $montoContado = 0;
    $detalleTransacciones = "";
    
    foreach ($transacciones as $transaccion) {
        $montoContado += $transaccion['monto'];
        $detalleTransacciones .= $transaccion['id'] . " - " . $transaccion['descripcion'] . " - Monto: " . number_format($transaccion['monto'], 2) . "\n";
    }
    
    $interes = $montoContado * 0.026;
    $montoConInteres = $montoContado + $interes;
    $cashBack = $montoContado * 0.001;
    $montoFinal = $montoConInteres - $cashBack;
    
    echo "<h2>Cuenta</h2>";
    echo "<pre>$detalleTransacciones</pre>";
    echo "<p><strong>Monto Total de Contado:</strong> " . number_format($montoContado, 2) . "</p>";
    echo "<p><strong>Monto Total con Interés (2.6%):</strong> " . number_format($montoConInteres, 2) . "</p>";
    echo "<p><strong>Cashback (0.1%):</strong> " . number_format($cashBack, 2) . "</p>";
    echo "<p><strong>Monto Final:</strong> " . number_format($montoFinal, 2) . "</p>";
    
    $contenido = "Cuenta\n\n" . $detalleTransacciones . "\n";
    $contenido .= "Monto total de contado: " . number_format($montoContado, 2) . "\n";
    $contenido .= "Monto total con interés (2.6%): " . number_format($montoConInteres, 2) . "\n";
    $contenido .= "Cashback (0.1%): " . number_format($cashBack, 2) . "\n";
    $contenido .= "Monto Final: " . number_format($montoFinal, 2) . "\n";
    
    file_put_contents("estado_cuenta.txt", $contenido);
}

registrarTransaccion(1, "Compra en supermercado", 120.50);
registrarTransaccion(2, "Pago de servicio de internet", 55.75);
registrarTransaccion(3, "Cena en restaurante", 89.90);
registrarTransaccion(4, "Compra en tienda de ropa", 150.00);

generarEstadoDeCuenta();

?>