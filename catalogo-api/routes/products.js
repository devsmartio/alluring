var express = require('express');
var router = express.Router();
var mysql = require('mysql');
const mysqlConfig = require('../config').mysqlConnectionData;
const bodega = require('../config').bodega;

 


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
        ri.id_sucursal
    FROM producto p
    JOIN reporte_inventario ri on ri.id_producto=p.id_producto 
        AND ri.total_existencias > 0
        AND ri.id_sucursal IN (
            SELECT id_sucursal 
            FROM sucursales 
            WHERE lower(identificador_excel) = '${bodega}' 
        )`, function (error, results, fields) {
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
