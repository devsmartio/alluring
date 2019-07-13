var express = require('express');
var router = express.Router();
var mysql = require('mysql');
const mysqlConfig = require('../config').mysqlConnectionData;

 


/* GET users listing. */
router.get('/', function(req, res, next) {
    var conn  = mysql.createConnection(mysqlConfig);
    conn.query(`
    SELECT 
        p.id_producto, 
        p.descripcion, 
        p.precio_venta, 
        p.id_tipo, 
        p.imagen, 
        p.codigo,
        ri.total_existencias, 
        ri.id_sucursal,
        ifnull(dp.cantidad, 0) descuento_producto_cantidad,
        ifnull(dp.porcentaje_descuento, 0) descuento_producto_porcentaje,
        ifnull(dt.cantidad, 0) descuento_categoria_cantidad,
        ifnull(dt.porcentaje_descuento, 0) descuento_categoria_porcentaje,
        ifnull(dg.cantidad, 0) descuento_general_cantidad,
        ifnull(dg.porcentaje_descuento, 0) descuento_general_porcentaje
    FROM producto p
    JOIN reporte_inventario ri on ri.id_producto=p.id_producto 
        AND ri.total_existencias > 0
        AND ri.id_sucursal IN (
            SELECT valor 
            FROM variables_sistema 
            WHERE nombre = 'BODEGA_CAT'
        )
    LEFT JOIN descuentos dp on dp.id_producto=p.id_producto 
        AND dp.activo = 1 
        AND dp.id_tipo_precio is null 
        AND dp.id_tipo is null
    LEFT JOIN descuentos dt on dt.id_tipo=p.id_tipo 
        AND dt.activo = 1 
        AND dt.id_tipo_precio is null 
        AND dt.id_producto is null
    LEFT JOIN descuentos dg on 
        dg.activo = 1 
        AND dg.id_producto is null 
        AND dg.id_tipo is null 
        AND dg.id_tipo_precio is null
    ORDER BY p.fecha_creacion DESC
        `, function (error, results, fields) {
        if (error) throw error;
        conn.destroy();
        res.send(results.map(p => {
            return {
                ...p,
                imageUrl: `${req.app.locals.img_url}/${p.imagen}`
            }
        }));
      });
});

module.exports = router;
