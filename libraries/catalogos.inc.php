<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Catalogos
 *
 * @author baci5
 */
class Catalogos {
    const Cuentas_Inventario = 1;
    const Cuentas_Monetaria = 2;
    const Cuentas_Cliente = 3;
    
    const MovimientoSucursalesEstado_EnRuta = 1;
    const MovimientoSucursalesEstado_Entregada = 2;
    const MovimientoSucursalesEstado_Rechazada = 3;
    
    const MonedaTipo_Billete = 1;
    const MonedaTipo_Moneda = 2;
    
    const VentaTipo_Normal = 1;
    const VentaTipo_Docena = 2;
    const VentaTipo_Mayorista = 3;
    
    const DescuentoTipo_VentaGeneral = 1;
    const DescuentoTipo_Producto = 2;
    const DescuentoTipo_Marca = 3;
    const DescuentoTipo_SegMitadPrecio = 4;
    const DescuentoTipo_DosxUno = 5;
    const DescuentoTipo_Tipo = 6;
    const DescuentoTipo_SubTipo = 7;
    const DescuentoTipo_3x2 = 8;
}
