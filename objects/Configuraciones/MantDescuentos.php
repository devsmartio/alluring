<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MantDescuentos
 *
 * @author Bryan Cruz
 */
class MantDescuentos extends FastMaintenance{
    function __construct() {
        parent::__construct();
        $this->instanceName = 'MantDescuentos';
        $this->table = 'descuentos';
        $this->setTitle('Mantenimiento de Descuentos');
        $productos = (new Collection($this->db->queryToArray("SELECT CONCAT(codigo,' ',descripcion) nombre, id_producto FROM producto")))->toSelectList("id_producto", "nombre");
        $tipos = Collection::get($this->db,"tipo")->toSelectList("id_tipo","nombre");
        $tiposClientes = Collection::get($this->db,"clientes_tipos_precio")->toSelectList("id_tipo_precio","nombre");


        $this->fields = [
            new FastField('ID', 'id_descuento', 'text', 'int', true, null, array(), FALSE, null, true),
            new FastField('Producto', 'id_producto', 'select', 'int', true, null, $productos, false),
            new FastField('Categoria', 'id_tipo', 'select', 'int', true, null, $tipos, false),
            new FastField('Tipo Cliente', 'id_tipo_precio', 'select', 'int', true, null, $tiposClientes, false),
            new FastField('Estado', 'activo', 'select', 'int', true, null, ["Activo" => 1,"Inactivo" => 0], true),
            new FastField('Porcentaje Descuento', 'porcentaje_descuento','text', 'text', true, null, [], false),
            new FastField('Cantidad', 'cantidad','text', 'text', true, null, [], false)
        ];
        $this->gridCols = array(
            'Producto' => 'producto_name',
            'Categoria' => 'categoria_name',
            'Tipo Cliente' => 'tipo_precio_name',
            'Activo' => 'estado_label',
            '%' => 'porcentaje',
            'Cant.' => 'cantidad'
        );
    }

    protected function specialValidation($fields, $r, $mess, $pkFields)
    {
        $descuento = new Entity($fields);
        //print_r($fields);
        $idProducto = str_replace("'", "", $descuento->get('id_producto'));
        $idTipo = str_replace("'", "", $descuento->get('id_tipo'));
        $idTipoPrecio = str_replace("'", "", $descuento->get('id_tipo_precio'));
        $porcentajeDescuento = floatval(str_replace("'", "", $descuento->get('porcentaje_descuento')));
        $cantidad = floatval(str_replace("'", "", $descuento->get('cantidad')));
        if((!isEmpty($idTipo) && $idTipo != 0) && (!isEmpty($idProducto) && $idProducto != 0)){
            $r = 0;
            $mess = "Solo puede elegir uno: Producto o categoria";
            return array('r' => $r, 'mess' => $mess);
        };

        if($porcentajeDescuento != 0 && ($porcentajeDescuento > 99 || $porcentajeDescuento < 1)){
            $r = 0;
            $mess = "El valor de porcentaje es inválido";
            return array('r' => $r, 'mess' => $mess);
        }

        if($cantidad != 0 && ($cantidad > 99 || $cantidad < 1)){
            $r = 0;
            $mess = "El valor de cantidad es inválido";
            return array('r' => $r, 'mess' => $mess);
        }

        if($cantidad > 0 && $porcentajeDescuento > 0){
            $r = 0;
            $mess = "Solo puede elegir uno: Cantidad o porcentaje descuento";
            return array('r' => $r, 'mess' => $mess);
        }

        if($cantidad == 0 && $porcentajeDescuento == 0){
            $r = 0;
            $mess = "Debe elegir uno: Cantidad o porcentaje descuento";
            return array('r' => $r, 'mess' => $mess);
        }

        $where = [];
        $where[] = sprintf("id_producto%s", !isEmpty($idProducto) && $idProducto != 0 ? "=$idProducto" : " is null");
        $where[] = sprintf("id_tipo%s", !isEmpty($idTipo) && $idTipo != 0 ? "=$idTipo" : " is null");
        $where[] = sprintf("id_tipo_precio%s", !isEmpty($idTipoPrecio) && $idTipoPrecio != 0 ? "=$idTipoPrecio" : " is null");
        if(isset($pkFields['id_descuento'])){
            $where[] = sprintf("id_descuento != %s", $pkFields['id_descuento']);
        }
        //print_r($where);
        if((new Collection($this->db->query_select("descuentos", count($where) > 0 ? join($where, ' AND ') : "")))->any()){
            $r = 0; 
            $mess = "Ya existe un descuento con esas caracteristicas, porfavor modifique o habilite ese descuento";
            return array('r' => $r, 'mess' => $mess);
        }
        return ['r' => $r, 'mess' => $mess];
    }
	
    protected function specialProcessBeforeInsert($insertData){
        //print_r($insertData);
        $date = new DateTime();
        $insertData['fecha_creacion'] = sqlValue($date->format('Y-m-d H:i:s'), 'date');
        $insertData['usuario_creacion'] = sqlValue($this->user['ID'], 'text');
        return $insertData;
    }

    protected function specialProcessBeforeUpdate($updateData, $pkFields = []){
        $descuento = new Entity($updateData);
        $idProducto = str_replace("'", "", $descuento->get('id_producto'));
        $idTipo = str_replace("'", "", $descuento->get('id_tipo'));
        $idTipoPrecio = str_replace("'", "", $descuento->get('id_tipo_precio'));
        $updateData['id_producto'] = $idProducto == 0 ? sqlValue('','text') : $idProducto;
        $updateData['id_tipo'] = $idTipo == 0 ? sqlValue('','text') : $idTipo;
        $updateData['id_tipo_precio'] = $idTipoPrecio == 0 ? sqlValue('','text') : $idTipoPrecio;
        return $updateData;
    }
	
    protected function specialProcessBeforeShow($resultSet){
        $tipos = Collection::get($this->db,"tipo");
        $tiposClientes = Collection::get($this->db,"clientes_tipos_precio");
        $productos = Collection::get($this->db, 'producto');
        foreach($resultSet as &$des){
            $tipo = $tipos->find(['id_tipo' => $des['id_tipo']]);
            $des['categoria_name'] = isset($tipo['nombre']) ? $tipo['nombre'] : "";
            $prod = $productos->find(['id_producto' => $des['id_producto']]);
            $des['producto_name'] = isset($prod['codigo']) ? $prod['codigo'] . " " . $prod["descripcion"] : "";
            $tipoCliente = $tiposClientes->find(['id_tipo_precio' => $des['id_tipo_precio']]);
            $des['tipo_precio_name'] = isset($tipoCliente['nombre']) ? $tipoCliente['nombre'] : "";
            $des['estado_label'] = $des['activo'] == 1 ? 'Sí' : 'No';
            if(!isEmpty($des['porcentaje_descuento'])){
                $des['porcentaje'] = sprintf("%s%%",number_format($des['porcentaje_descuento'],2));
            }
        }
        return sanitize_array_by_keys($resultSet, ['categoria_name','producto_name','tipo_precio_name','estado_label']);
    }
}

?>