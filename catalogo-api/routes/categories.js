var express = require('express');
var router = express.Router();
var mysql = require('mysql');
const mysqlConfig = require('../config').mysqlConnectionData;

 


/* GET users listing. */
router.get('/', function(req, res, next) {
    var conn  = mysql.createConnection(mysqlConfig);
    conn.query('SELECT id_tipo, nombre, id_tipo_agrupacion FROM tipo', function (error, results, fields) {
        if (error) throw error;
        conn.destroy();
        res.send(results);
      });
});

router.get('/groups', function(req, res, next) {
    var conn  = mysql.createConnection(mysqlConfig);
    conn.query('SELECT id_tipo_agrupacion, nombre FROM tipos_agrupaciones', function (error, results, fields) {
        if (error) throw error;
        conn.destroy();
        res.send(results);
      });
});

module.exports = router;
