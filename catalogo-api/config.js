module.exports = {
    mailAuth: {
        user: "bryan.cruz@getsmartio.com",
        pass: "io_dev2018!"
    },
    toMailData: {
        from: "notificaciones@alluring.com.gt",
        to: "bryan.cruz@getsmartio.com",
        subject: "PEDIDO REALIZADO CATALOGO",
        html: ""
    },
    mysqlConnectionData: {
        connectionLimit : 10,
        //socketPath: '/var/run/mysqld/mysqld.sock',
        host            : '127.0.0.1',
        user            : 'root',
        password        : '',
        database        : 'alluring_prod',
	    charset: 'utf8mb4'
    },
    bodega: "josecarlos"
}
