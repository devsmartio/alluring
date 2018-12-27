var express = require('express');
var router = express.Router();
var mysql = require('mysql');
var md5 = require("md5");

const mysqlConfig = {
  connectionLimit : 10,
  host            : 'localhost',
  user            : 'devsmartio',
  password        : 'rootio',
  database        : 'alluring'
};

/* GET home page. */
router.post('/login', function(req, res, next) {
  let conn  = mysql.createConnection(mysqlConfig);
  let conditions = [req.body.user, md5(req.body.pass)];
  let query = conn.query(`
  SELECT c.id_cliente, c.nombres, c.apellidos, tp.porcentaje_descuento, c.correo, t.numero telefono,c.id_tipo_precio
  FROM clientes c
  JOIN clientes_tipos_precio tp ON tp.id_tipo_precio=c.id_tipo_precio 
  LEFT JOIN (
    select max(numero) numero, id_cliente from clientes_telefonos group by id_cliente
  ) t on t.id_cliente=c.id_cliente
  WHERE c.catalogo_usuario=? 
    AND c.catalogo_password_hash =?`,conditions, function (error, results, fields) {
      if (error) {
        next(error);
        return;
      }
      //conn.destroy()
      if(!results.length){
        
        res.sendStatus(404);
      } else {
        let cliente = results[0];
        let query = conn.query(`SELECT * FROM descuentos WHERE id_tipo_precio=? and activo = 1`,[cliente.id_tipo_precio], function(error, results){
          //console.log("ERROR:", error);
          if (error) {
            next(error);
            return;
          }
          conn.destroy();
          //console.log("DESCUENTOS:", results);
          res.json({...cliente, descuentos: results})
        })
      }
    });
});
/**
 * 
router.post('/login', function(req, res, next) {
  let conn  = mysql.createConnection(mysqlConfig);
  let conditions = [req.body.user, md5(req.body.pass)];
  let sql =  `SELECT * FROM clientes where catalogo_usuario="${conditions[0]}" and catalogo_password_hash = "${conditions[1]}"`;
  console.log(sql);
  conn.query(sql, function (error, results, fields) {
      if (error) throw error;
      conn.destroy();
      console.log("RESULTS:", results);
      if(!results.length){
        res.sendStatus(404);
      } else {
        res.send(results[0]);
      }
    });
});
 */
module.exports = router;
