use alluring;

alter table app_user add EMAIL varchar(50) null;
alter table app_user add PHONE varchar(20) null;

create table if not exists usuarios_bodegas (
	id_bodega int(20) not null,
    id_usuario varchar(200) not null
);

-- alter table usuarios_bodegas add constraint FK_usuarios_bodegas_usuarios foreign key (id_usuario) references app_user(ID);
alter table usuarios_bodegas add constraint FK_usuarios_bodegas_bodegas foreign key (id_bodega) references sucursales(id_sucursal);
alter table clientes drop foreign key clientes_empleado;
alter table clientes drop column id_empleado;
alter table clientes add column id_usuario varchar(200) null;
alter table producto add column codigo varchar(20) null;
ALTER VIEW `reporte_inventario` AS
    SELECT 
        `p`.`codigo_origen` AS `codigo_origen`,
        `p`.`nombre` AS `nombre_producto`,
        MAX(`t`.`nombre`) AS `nombre_categoria`,
        MAX(`s`.`nombre`) AS `nombre_sucursal`,
        COALESCE((SUM(`trx`.`haber`) - SUM(`trx`.`debe`)),
                0) AS `total_existencias`,
        MAX(`s`.`id_sucursal`) AS `id_sucursal`,
        MAX(`t`.`id_tipo`) AS `id_tipo`
    FROM
        (((`producto` `p`
        LEFT JOIN `tipo` `t` ON ((`t`.`id_tipo` = `p`.`id_tipo`)))
        LEFT JOIN `trx_transacciones` `trx` ON ((`trx`.`id_producto` = `p`.`id_producto`)))
        LEFT JOIN `sucursales` `s` ON ((`s`.`id_sucursal` = `trx`.`id_sucursal`)))
    GROUP BY `p`.`id_producto`, s.id_sucursal;



alter table trx_venta drop column id_empleado;
alter table trx_venta add column usuario_venta varchar(100) null;
alter table trx_venta add column id_transaccion bigint(50) null;
alter table trx_venta add constraint fk_venta_transacciones foreign key (id_transaccion) references trx_transacciones(id_transaccion);
alter table trx_transacciones modify id_empleado int(20) null;

-- SABADO 10/6/2018
-- JUEVES 10/18/2018
alter table trx_venta add credito_devolucion decimal(10,2) null;
alter table trx_venta add fecha_devolucion datetime null; 
alter table trx_venta_detalle add cantidad_devolucion int null;
insert into formas_pago(nombre, usuario_creacion, fecha_creacion) values ('Credito devolucion', 'Dev', now());
alter table generacion_etiquetas add codigo varchar(50) null;
alter table generacion_etiquetas modify codigo_origen varchar(50) null;