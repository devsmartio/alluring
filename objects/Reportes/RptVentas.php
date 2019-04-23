<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TrxAvancesProyecto
 *
 * @author baci5
 */
class RptVentas extends FastTransaction {
    function __construct(){
        parent::__construct();
        $this->setTitle(' Reporte Ventas');
        $this->hasCustomSave = true;
        $this->excelFileName = "ventas";
        $this->columns = [
            new FastReportColumn("Fecha", "fecha_creacion"),
            new FastReportColumn("Nombre Cliente", "nombres", "sanitize"),
            new FastReportColumn("Apellido Cliente", "apellidos", "sanitize"),
            new FastReportColumn("Vendedor", "usuario_venta", "sanitize"),
            new FastReportColumn("Bodega", "nombre_bodega", "sanitize"),
            new FastReportColumn("Total", "total_real", "number_format"),
            new FastReportColumn("Piezas", "piezas", "number_format_inverse"),
            new FastReportColumn("Tipo", "tipo_venta", "sanitize")
        ];
    }
    
    protected function showModule() {
        include VIEWS . "/rpt_ventas.phtml";
    }
    
    public function myJavascript() {
        parent::myJavascript();
        ?>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.2/dist/sweetalert2.all.min.js"></script>
    <script>
        $("#fechaDe, #fechaA").datetimepicker({
            format: 'DD/MM/YYYY',
            maxDate: moment(),
        })
        app.controller('ModuleCtrl', function($scope, $http, $rootScope, $timeout){
            $scope.sucursales = [];
            $scope.searchVentas = function(){
                if($scope.isAdmin || $scope.search.id_sucursal){
                    let list = ['nombres', 'apellidos'];
                    let query = "";
                    for(let i = 0; list.length > i; i++){
                        let q = list[i];
                        query = query ? `${query}&` : query;
                        query += `${q}=${$scope.search[q]}`
                    }
                    let fechaDe = $("#fechaDe").val();
                    let fechaA = $("#fechaA").val();
                    if(fechaDe){
                        query = query ? `&${query}` : query;
                        query+='&fechaDe=' + fechaDe;
                        query+='&fechaA=' + fechaA;
                        query+='&sucursal=' + ((!$scope.search.id_sucursal || $scope.search.id_sucursal == null) ? "" : $scope.search.id_sucursal);
                        $http.get(`${$scope.ajaxUrl}&act=getVentasFiltradas${query}`).success(res => {
                            $scope.ventas = res;
                        })
                    } else {
                        swal("Campo requerido", "El campo \"fecha de\" es requerido", "warning");
                    }
                    $("#nombres").select();
                } else {
                    swal("Campo requerido", "El campo \"Bodega\" es requerido", "warning");
                }
            }
            
            $scope.startAgain = function(){
                let date = moment();  
                $scope.isAdmin = <?php echo ($this->user['FK_PROFILE'] == 1 ? 'true' : 'false'); ?>;
                $scope.search = {
                    fechaDe: date.format('DD/MM/YYYY'),
                    fechaA: date.format('DD/MM/YYYY'),
                    nombres: "",
                    apellidos: "",
                    id_sucursal: ""
                } 
                $scope.ventas = [];
                $scope.getSucursales();
            };

            $scope.getSucursales = function() {
                $http.get(`${$scope.ajaxUrl}&act=getSucursales`).success(response => {
                    $scope.sucursales = response;
                    if($scope.sucursales.length){
                        if(!$scope.isAdmin){
                            $scope.search.id_sucursal = $scope.sucursales[0].id_sucursal;
                        }
                        $scope.searchVentas();
                    }
                })
            }

            $scope.generaExcel = function() {
                if($scope.isAdmin || $scope.search.id_sucursal){
                    let list = ['nombres', 'apellidos'];
                    let query = "";
                    for(let i = 0; list.length > i; i++){
                        let q = list[i];
                        query = query ? `${query}&` : query;
                        query += `${q}=${$scope.search[q]}`
                    }
                    let fechaDe = $("#fechaDe").val();
                    let fechaA = $("#fechaA").val();
                    if(fechaDe){
                        query = query ? `&${query}` : query;
                        query+='&fechaDe=' + fechaDe;
                        query+='&fechaA=' + fechaA;
                        query+='&sucursal=' + ((!$scope.search.id_sucursal || $scope.search.id_sucursal == null) ? "" : $scope.search.id_sucursal);
                        window.open(`${$scope.ajaxUrl}&act=generarExcel${query}`, "_blank");
                    } else {
                        swal("Campo requerido", "El campo \"fecha de\" es requerido", "warning");
                    }
                    $("#nombres").select();
                } else {
                    swal("Campo requerido", "El campo \"Bodega\" es requerido", "warning");
                }
            }
/*
            $scope.generarPdf = function(idVenta){
                window.open("?action=pdf&tmp=VT&id_venta=" + idVenta, '_blank');
            }
  */          
            

            $(".searchEnter").keyup(e => {
                e.which == 13 && $scope.searchVentas();
            })

            $scope.cancelar = function(){
                $scope.cancel();
            };
            
            $scope.startAgain();
            $rootScope.addCallback(function(){
                $scope.startAgain(); 
            });
        });
    </script>
        <?php
    }

    public function getSucursales(){
        if($this->user['FK_PROFILE'] == 1 /* SUPER ADMIN */){
            $bods = Collection::get($this->db, "sucursales")->toArray();
        } else {
            $accessBods = 
            join(
                array_map(function($bod) {
                    return $bod['id_bodega'];
                }, $this->db->query_select("usuarios_bodegas", sprintf("id_usuario='%s'", $this->user['ID']))
                ), 
            ",");
            if(empty($accessBods)){
                $bods = []; 
            } else {
                $bods = $this->db->query_select("sucursales", sprintf("id_sucursal in (%s)", $accessBods));
            }
        }
        echo json_encode(sanitize_array_by_keys($bods, ['nombre', 'usuario_creacion']));
    }

    public function getVentasFiltradas(){
        echo json_encode($this->getResultSet());
    }

    private function getResultSet(){
        $nombres = getParam('nombres');
        $apellidos = getParam('apellidos');
        $fechaDe = getParam('fechaDe');
        $fechaA = getParam('fechaA');
        $sucursal = getParam('sucursal');
        $where = "";
        $user = decode_email_address($this->user['ID']);
        if(!empty($nombres)){
            $where = "c.nombres like '%$nombres%'";
        }

        if(!empty($apellidos)){
            $where = empty($where) ? $where : "$where AND ";
            $where.= "c.apellidos like '%$apellidos%'";
        }

        if(!empty($fechaDe)){
            $fechaDe = DateTime::createFromFormat('d/m/Y', $fechaDe)->format('Y-m-d');
            $where = empty($where) ? $where : "$where AND ";
            $where.= "cast(v.fecha_creacion as date) between cast('$fechaDe' as date) ";

            if(!empty($fechaA)){
                $fechaA = DateTime::createFromFormat('d/m/Y', $fechaA)->format('Y-m-d');
                $where.= "AND cast('$fechaA' as date)";
            } else {
                $where.= "AND now()";
            }
        }

        if(!empty($sucursal)){
            $where = empty($where) ? $where : "$where AND ";
            $where.= "vt.id_sucursal = $sucursal";
        }

        if($this->user['FK_PROFILE'] == 1){
            $query = sprintf("
                SELECT 
                    v.fecha_creacion, 
                    c.nombres, 
                    c.apellidos,
                    v.usuario_venta, 
                    v.total, 
                    sum(vt.cantidad) piezas, 
                    v.total - ifnull(max(fp.cantidad), 0) total_real,
                    CASE
                        WHEN v.estado ='D' THEN 'Venta con devolución'
                        WHEN v.estado = 'VC' THEN 'Venta consignación'
                    ELSE 'Venta' END as tipo_venta,
                    max(s.nombre) nombre_bodega
                FROM trx_venta v
                JOIN clientes c on c.id_cliente=v.id_cliente
                JOIN trx_venta_detalle vt on vt.id_venta=v.id_venta
                JOIN sucursales s on s.id_sucursal=vt.id_sucursal
                LEFT JOIN   trx_venta_formas_pago fp on fp.id_venta=v.id_venta AND fp.id_forma_pago = 4
                WHERE %s
                AND v.estado in ('D', 'V', 'VC')
                GROUP BY v.id_venta
                ORDER BY v.fecha_creacion desc
            ", $where);
        } else {
            $query = sprintf("
                SELECT 
                    v.fecha_creacion, 
                    c.nombres, 
                    c.apellidos,
                    v.usuario_venta, 
                    v.total, 
                    sum(vt.cantidad) piezas, 
                    v.total - ifnull(max(fp.cantidad), 0) total_real,
                    CASE
                        WHEN v.estado ='D' THEN 'Venta con devolución'
                        WHEN v.estado = 'VC' THEN 'Venta consignación'
                    ELSE 'Venta' END as tipo_venta,
                    max(s.nombre) nombre_bodega
                FROM trx_venta v
                JOIN clientes c on c.id_cliente=v.id_cliente
                JOIN trx_venta_detalle vt on vt.id_venta=v.id_venta 
                JOIN sucursales s on s.id_sucursal=vt.id_sucursal
                LEFT JOIN   trx_venta_formas_pago fp on fp.id_venta=v.id_venta AND fp.id_forma_pago = 4
                WHERE %s
                AND v.usuario_venta='%s'
                AND v.estado in ('D','V','VC')
                GROUP BY v.id_venta
                ORDER by v.fecha_creacion desc
            ", $where, $user);
        }
        return sanitize_array_by_keys($this->db->queryToArray($query), ['nombres', 'apellidos', 'usuario_venta', 'tipo_venta', 'nombre_bodega']);
    }

    public function generarExcel(){
        $resultSet = $this->getResultSet();
        $result = "<table>";
        $result .= "<tr>";
        for($i = 0; count($this->columns) > $i; $i++){
            $result .= "<th>";
            if($this->columns[$i] instanceof FastReportColumn){
                $result.= $this->columns[$i]->name;
            }
            $result .= "</th>";
        }
        $result .= "</tr>";
        for($i = 0; count($resultSet) > $i; $i++){
            $result .= "<tr>";
            for($i2 = 0; count($this->columns) > $i2; $i2++){
                $result .= "<td>";
                if($this->columns[$i2] instanceof FastReportColumn){
                    $result .= $this->columns[$i2]->serveValue($resultSet[$i]);
                }
                $result .= "</td>";
            }
            $result .= "</tr>";
        }
        $result .= "<table>";
        $date = new Datetime();
        header('Content-Type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment;filename=%s_%s.xls', $this->excelFileName, $date->format(SQL_DT_FORMAT)));
        
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        print($result);
    }
    
    public function dataIsValid($data) {
        $comentario = $data["comentario"];
        if(isEmpty($comentario)){
            $this->r = 0;
            $this->msg = "El comentario no puede estar vacio";
        }
        if($this->r == 0){
            return false;
        }
        return true;
    }
    
    public function doSave($data){
        $this->r = 1;
        $this->msg = 'Usted envio ' . $data["comentario"];
    }
}
