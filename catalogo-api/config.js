module.exports = {
    mailAuth: {
        user: "bryan.cruz@getsmartio.com",
        pass: "io_dev2018!"
    },
    toMailData: {
        from: "notificaciones@alluring.com.gt",
        to: "bryan.cruz@getsmartio.com;ventas@alluringconcept.com",
        subject: "PEDIDO REALIZADO CATALOGO",
        html: ""
    },
    mysqlConnectionData: {
        connectionLimit : 10,
        socketPath: '/var/run/mysqld/mysqld.sock',
        host            : 'localhost',
        user            : 'root',
        password        : 'rootio',
        database        : 'alluring',
	charset: 'utf8mb4'
    }
}
