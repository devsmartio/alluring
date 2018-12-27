var express = require('express');
var router = express.Router();
var mysql = require('mysql');
const mysqlConfig = {
    connectionLimit : 10,
    host            : 'localhost',
    user            : 'devsmartio',
    password        : 'rootio',
    database        : 'alluring'
  };

 


/* GET users listing. */
router.get('/', function(req, res, next) {
    var conn  = mysql.createConnection(mysqlConfig);
    conn.query('SELECT id_tipo, nombre FROM tipo', function (error, results, fields) {
        if (error) throw error;
        conn.destroy();
        res.send(results);
      });
});

module.exports = router;
