<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of productoutil
 *
 * @author baci5
 */
class ProductoUtil {
    public static function getCodigoProducto($producto, DbManager $db){
        if(!isEmpty($producto['id_subtipo'])){
            $sub = $db->query_select('subtipo', sprintf('id_subtipo=%s', $producto['id_subtipo']));
            $pref_subtipo = $sub[0]['prefijo'];
            $es_subtipo = true;
        }

        $proveedor = $db->query_select("proveedor", sprintf("id_proveedor=%s", $producto['id_proveedor']));
        $pref_prov = $proveedor[0]['es_internacional'] == 1 ? "I" : "L";

        $marca = $db->query_select('marca', sprintf('id_marca=%s', $producto['id_marca']));
        $pref_marca = $marca[0]['prefijo'];
        
        $codigo = !isEmpty($producto['sku']) && strlen($producto['sku']) > 4 ? substr($producto['sku'], -4) : $producto['id_producto'];

        $codigo_producto = $pref_prov . $pref_subtipo . $pref_marca . $codigo;

        return $codigo_producto;
    }
    
    public static function getInventarioPorSucursal($id_producto, $id_sucursal, DbManager $db){
        $query = 'select'
                . ' sum(case when id_sucursal=%s and id_producto=%s then debe else 0 end) debe,'
                . ' sum(case when id_sucursal=%s and id_producto=%s then haber else 0 end) haber'
                . ' from trx_transacciones'
                . ' where id_producto=%s and id_sucursal=%s';
        $queryEnTraslado = 'select sum(d.unidades) cantidad from trx_movimiento_sucursales_detalle d '
                . 'join trx_movimiento_sucursales m on m.id_movimiento_sucursales=d.id_movimiento_sucursales and id_movimiento_sucursales_estado <> %s and m.id_sucursal_origen=%s '
                . 'where d.id_producto=%s ';
        $resultadoTrx = $db->queryToArray(sprintf($query, $id_sucursal, $id_producto, $id_sucursal, $id_producto, $id_producto, $id_sucursal));
        $resultadoEnTraslado = $db->queryToArray(sprintf($queryEnTraslado, Catalogos::MovimientoSucursalesEstado_Entregada, $id_sucursal, $id_producto));
        
        return (intval($resultadoTrx[0]['haber']) - (intval($resultadoEnTraslado[0]['cantidad']) + intval($resultadoTrx[0]['debe'])));
    }
    
    public static function getInventario($producto, DbManager $db){
        $empleado = EmpleadoUtil::getEmpleado();
        $id_sucursal = 0;
        if($empleado !== false){
            $id_sucursal = $empleado['id_sucursal'];
        }
        $query = 'select'
                . ' sum(case when id_sucursal=%s and id_producto=%s then debe else 0 end) debe_local,'
                . ' sum(case when id_producto=%s then debe else 0 end) debe_global,'
                . ' sum(case when id_sucursal=%s and id_producto=%s then haber else 0 end) haber_local,'
                . ' sum(case when id_producto=%s then haber else 0 end) haber_global'
                . ' from trx_transacciones'
                . ' where id_producto=%s';
        $queryEnTraslado = 'select sum(d.unidades) cantidad from trx_movimiento_sucursales_detalle d '
                . 'join trx_movimiento_sucursales m on m.id_movimiento_sucursales=d.id_movimiento_sucursales and id_movimiento_sucursales_estado <> %s and m.id_sucursal_origen=%s '
                . 'where d.id_producto=%s ';
        $resultadoEnTraslado = $db->queryToArray(sprintf($queryEnTraslado, Catalogos::MovimientoSucursalesEstado_Entregada, $id_sucursal, $producto['id_producto']));
        $resultado = $db->queryToArray(sprintf($query, $id_sucursal, $producto['id_producto'], $producto['id_producto'], $id_sucursal, $producto['id_producto'], $producto['id_producto'], $producto['id_producto']));
        $resultado[0]['en_traslado'] = $resultadoEnTraslado[0]['cantidad'];
        return $resultado[0];
    }
    
    public static function getCosto($producto, $empleado, DbManager $db){
        $queryIngreso = 'select d.cantidad, d.cantidad_vendida, d.costo_producto costo, d.id_ingreso_inventario_detalle '
                    . 'from trx_ingreso_inventario i '
                    . 'join trx_ingreso_inventario_detalle d on i.id_ingreso_inventario=d.id_ingreso_inventario '
                    . 'and id_producto=%s and cantidad_vendida < cantidad '
                    . 'where i.id_sucursal=%s '
                    . 'order by i.fecha_creacion ';
        $ingresos = $db->queryToArray(sprintf($queryIngreso, $producto['id_producto'], $empleado['id_sucursal']));
        $unidades = $producto['unidades'];
        $costos = [];
        foreach($ingresos as $i){
            $faltaVender = $i['cantidad'] - $i['cantidad_vendida'];
            if($faltaVender >= $unidades){
                $costos[] = $i['costo'] * $unidades;
                $nuevaCantidadVendida = $i['cantidad_vendida'] + $unidades;
                $update = [
                    'cantidad_vendida' => $nuevaCantidadVendida
                ];
                $db->query_update('trx_ingreso_inventario_detalle', $update, sprintf('id_ingreso_inventario_detalle=%s', $i['id_ingreso_inventario_detalle']));
                return array_sum($costos) / $producto['unidades'];
            } else {
                $unidades = $unidades - $faltaVender;
                $costos[] = $i['costo'] * $faltaVender;
                $nuevaCantidadVendida = $i['cantidad'];
                $update = [
                    'cantidad_vendida' => $nuevaCantidadVendida
                ];
                $db->query_update('trx_ingreso_inventario_detalle', $update, sprintf('id_ingreso_inventario_detalle=%s', $i['id_ingreso_inventario_detalle']));
            }
        }
        
        $p = $db->query_select('producto', sprintf('id_producto=%s', $producto['id_producto']));
        $costos[] = $p[0]['costo'] * $unidades;
        return array_sum($costos) / $producto['unidades'];
    }
    
    public static function reingresa($producto, $empleado, DbManager $db){
        $queryIngreso = 'select d.cantidad, d.cantidad_vendida, d.costo_producto costo, d.id_ingreso_inventario_detalle '
                    . 'from trx_ingreso_inventario i '
                    . 'join trx_ingreso_inventario_detalle d on i.id_ingreso_inventario=d.id_ingreso_inventario '
                    . 'and id_producto=%s and d.cantidad_vendida > 0 '
                    . 'where i.id_sucursal=%s '
                    . 'order by i.id_ingreso_inventario desc ';
        $ingresos = $db->queryToArray(sprintf($queryIngreso, $producto['id_producto'], $empleado['id_sucursal']));
        $unidades = $producto['unidades'];
        $costos = [];
        foreach($ingresos as $i){
            if($unidades <=  $i['cantidad_vendida']){
                $nuevaCantidadVendida = intval($i['cantidad_vendida']) - intval($producto['unidades']);
                $update = [
                    'cantidad_vendida' => $nuevaCantidadVendida
                ];
                $db->query_update('trx_ingreso_inventario_detalle', $update, sprintf('id_ingreso_inventario_detalle=%s', $i['id_ingreso_inventario_detalle']));
                return;
            } else {
                $unidades = $unidades - intval($i['cantidad_vendida']);
                $nuevaCantidadVendida = 0;
                $update = [
                    'cantidad_vendida' => $nuevaCantidadVendida
                ];
                $db->query_update('trx_ingreso_inventario_detalle', $update, sprintf('id_ingreso_inventario_detalle=%s', $i['id_ingreso_inventario_detalle']));
            }
        }
        
        return;
    }
    
}
