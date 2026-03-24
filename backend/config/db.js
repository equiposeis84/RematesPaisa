const mysql = require('mysql2');

const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',      // Tu usuario de MySQL
    password: '',      // Tu contraseña (si tienes)
    database: 'sistema_comercial', // <--- ¡ESTO ES LO QUE FALTA O ESTÁ MAL!
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

module.exports = pool.promise();