<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clienteutil
 *
 * @author baci5
 */
class ClienteExtension {
    
    private $cliente;
    private $db;
    
    public function __construct($cliente){
        if(is_array($cliente) && isset($cliente['id_cliente'])){
            $this->cliente = $cliente;
        }
        $this->db = DbManager::getMe();
    }
    
    public function saldoActual(){
        $query = 'select'
                . ' ifnull(sum(pendiente),0.00) pendiente'
                . ' from trx_venta'
                . ' where id_cliente=%s and pendiente > 0';
    $resultadoTrx = $this->db->queryToArray(sprintf($query, $this->cliente['id_cliente']));
        return $resultadoTrx[0]['pendiente'];
    }
    
    public function aplicarPagoAVenta($idPago){
        $empleado = EmpleadoUtil::getEmpleado();
        if(!$empleado){
            return false;
        }
        $pago = $this->db->query_select('trx_pago_cliente', sprintf('id_pago_cliente=%s', $idPago));
        if(count($pago) > 0){
            $pago = $pago[0];
            $ventas = $this->db->query_select('trx_venta', sprintf('pendiente > 0 and id_cliente=%s', $pago['id_cliente']));
            $pagado = $pago['total'];
            $fecha = (new DateTime())->format(SQL_DT_FORMAT);
            foreach($ventas as $v){
                if($v['pendiente'] <= $pagado){
                    $venta = [
                        'pendiente' => 0,
                        'id_empleado_modificacion' => $empleado['id_empleado'],
                        'fecha_modificacion' => sqlValue($fecha, 'date')
                    ];
                    $this->db->query_update('trx_venta', $venta, sprintf('id_venta=%s', $v['id_venta']));
                    $pagado -= $v['pendiente'];
                    if($pagado <= 0){
                        break;
                    }
                } else {
                    $venta = [
                        'pendiente' => ($v['pendiente'] - $pagado),
                        'id_empleado_modificacion' => $empleado['id_empleado'],
                        'fecha_modificacion' => sqlValue($fecha, 'date')
                    ];
                    $this->db->query_update('trx_venta', $venta, sprintf('id_venta=%s', $v['id_venta']));
                    $pagado = 0;
                    break;
                }
            }
            return TRUE;
        }
    }
}
