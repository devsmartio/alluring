var express = require('express');
var router = express.Router();
var mysql = require('mysql');
const nodemailer = require('nodemailer');
const config = require('../config');
const transport = nodemailer.createTransport({
    service: 'gmail',
    auth: config.mailAuth
})
const mysqlConfig = {
    connLimit : 10,
    host            : 'localhost',
    user            : 'devsmartio',
    password        : 'rootio',
    database        : 'alluring'
  };

 
const insertCliente = (conn, cliente) => {
    return new Promise((resolve, reject) => {
        let SQL_NOW = mysql.raw('now()');
        let insertCliente = {
            nombres: cliente.nombres,
            apellidos: cliente.apellidos,
            direccion: "",
            identificacion: "",
            correo: cliente.correo ? cliente.correo : "",
            tiene_credito: false,
            fecha_creacion: SQL_NOW,
            usuario_creacion: "dev-catalogo"
        }
        let clienteId = 0;
        conn.query('INSERT INTO clientes SET ?', insertCliente, function (error, results, fields) {
            if (error) {
                reject(error);
            } else {
                clienteId = results.insertId;
                let telefonoInsert = {
                    numero: cliente.telefono,
                    id_cliente: results.insertId
                }
                conn.query('INSERT INTO clientes_telefonos SET ?', telefonoInsert, function (error, results, fields) {
                    if (error) {
                        reject(error);
                    } else {
                        resolve(clienteId);
                    }
                    
                });
            }
            
        });
    })
}

const insertProducto = (conn, producto, ventaId, cliente) => {
    let SQL_NOW = mysql.raw('now()');
    console.log("INSERTANDO PROD:", producto);
    return new Promise((resolve, reject) => {
        let venta_detalle = {
            'id_venta': ventaId,
            'id_producto':producto.id_producto,
            'id_sucursal':producto.id_sucursal,
            'cantidad': producto.cantidad_vender,
            'precio_venta': producto.precio_descuento || producto.precio_venta,
            'fecha_creacion': SQL_NOW,
            'usuario_creacion': 'dev_catalogo'
        };
        conn.query('INSERT INTO trx_venta_detalle SET ?', venta_detalle, function (error, results, fields) {
            if (error){
                reject(error);
            } else {
                let transaccion = {
                    'id_cuenta':2, //QUEMADO CUENTA INVENTARIO
                    'id_sucursal':producto.id_sucursal,
                    'descripcion': 'Venta',
                    'id_moneda':1, //QUEMADO QUETZAL!!
                    'id_producto':producto.id_producto,
                    'debe': producto.cantidad_vender,
                    'haber': 0,
                    'fecha_creacion': SQL_NOW,
                    'id_cliente': cliente.id_cliente
                };
        
                conn.query('INSERT INTO trx_transacciones SET ?', transaccion, function (error, results, fields) {
                    if (error){
                        reject(error);
                    } else {
                        resolve(true);
                    }
                });
            }
        });
    })
}

/* GET users listing. */
router.post('/', function(req, res, next) {
    try {
        let cliente = req.body.cliente;
        let cart = req.body.cart;
        let productos = cart.productos;
        let outofstock = [];
        var conn  = mysql.createConnection(mysqlConfig);
        let query = `
        SELECT id_producto,total_existencias,id_sucursal
        FROM reporte_inventario ri
        WHERE ri.total_existencias > 0
        AND ri.id_sucursal IN (
            SELECT valor 
            FROM variables_sistema 
            WHERE nombre = 'BODEGA_CAT'
        )
        AND id_producto in (${productos.map(p => p.id_producto).join(",")})
        `;
        let SQL_NOW = mysql.raw('now()');
        conn.query(query, async function (error, results, fields) {
            if (error)  throw error;
            for(let i = 0; productos.length > i; i++){
                let p = results.find(r => r.id_producto == productos[i].id_producto && r.id_sucursal == productos[i].id_sucursal);
                
                if(!p){
                    outofstock.push({...productos[i], existencia: 0})
                } else if(p.total_existencias < productos[i].cantidad_vender){
                    outofstock.push({...productos[i], existencia: p.total_existencias})
                }
            }
            if(outofstock.length){
                res.status(400).send(outofstock);
            } else {
                console.log("Verificando CLIENTE");
                console.log("CLIENTE", cliente);
                if(!cliente.id_cliente){
                    console.log("INSERTANDO CLIENTE");
                    cliente.id_cliente = await insertCliente(conn, cliente).catch(e => {
                        console.log("ERROR INSERTANDO CLIENTES", e);
                        res.sendStatus(500);
                    })
                }

                if(cliente.id_cliente){
                    console.log("INSERTANDO VENTA");
                    let result = await insertVenta(conn,cart,cliente).catch(err => {
                        console.log("ERROR INSERTANDO venta", err);
                        res.sendStatus(500);
                    })

                    console.log("VERIFICANDO RESULTADO");
                    if(result){
                        conn.destroy();
                        console.log("ENVIANDO NOTIFICACION");
                        let mailData = {...config.toMailData};
                        let sumProd = 0;
                        cart.productos.forEach(p => {
                            sumProd+=p.cantidad_vender;
                        })
                        mailData.html = `<b>Se ha realizado un pedido a través del catálogo</b>
                        <p>
                            Cliente: ${cliente.nombres} ${cliente.apellidos} Telefono: ${cliente.telefono} <br/>
                            Fecha: ${new Date().toLocaleString()} <br/>
                            Total: Q. ${cart.total_mixed.toFixed(2)} <br/>
                            Piezas: ${sumProd} <br/>
                        </p>`;
                        transport.sendMail(mailData, (err,info) => {
                            if(err) console.log("ERROR SENDING NOTIFICATION", err);
                            if(!err) console.log("NOTIFICATION SENT");
                            res.sendStatus(200);
                        })
                    }
                } else {
                    console.log("NO SE INSERTO EL CLIENTE");
                    res.sendStatus(500);
                }

            }
        });
    } catch(err){
        console.log(err);
        next(err);
    }
    
});

const insertVenta = (conn, cart, cliente, productos) => {
    let SQL_NOW = mysql.raw('now()');
    return new Promise((resolve, reject) => {
        conn.beginTransaction(err => {
            if(err) reject(err);
            let venta = {
                total: cart.total_mixed,
                id_cliente: cliente.id_cliente,
                estado: 'P',
                usuario_creacion: "dev-catalogo",
                fecha_creacion: SQL_NOW,
            }
            conn.query('INSERT INTO trx_venta SET ?', venta, async function (error, results, fields) {
                if (error){
                    conn.rollback( _=> {
                        reject(error);
                    })
                }
                let ventaId = results.insertId;
                for(let i = 0; cart.productos.length > i; i++){
                    let result = await insertProducto(conn, cart.productos[i], ventaId, cliente).catch(err => {
                        conn.rollback(function() {
                            reject(err);
                          });
                    })
                }
                
                conn.commit(function(err) {
                    if (err) {
                      conn.rollback(function() {
                        reject(err);
                      });
                    }
                    console.log('success!');
                    resolve(true);
                  });
            });
        })
    })
}

module.exports = router;
