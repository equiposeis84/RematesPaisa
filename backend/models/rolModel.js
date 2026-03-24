const db = require('../config/db');

const Rol = {
    findAll: async () => {
        const [rows] = await db.query('SELECT * FROM roles ORDER BY id_rol ASC');
        return rows;
    },
    findById: async (id) => {
        const [rows] = await db.query('SELECT * FROM roles WHERE id_rol = ?', [id]);
        return rows[0];
    },
    create: async (data) => {
        const { nombre, descripcion } = data;
        const [result] = await db.query(
            'INSERT INTO roles (nombre, descripcion) VALUES (?, ?)',
            [nombre, descripcion]
        );
        return result.insertId;
    },
    update: async (id, data) => {
        const { nombre, descripcion } = data;
        const [result] = await db.query(
            'UPDATE roles SET nombre = ?, descripcion = ? WHERE id_rol = ?',
            [nombre, descripcion, id]
        );
        return result.affectedRows > 0;
    },
    delete: async (id) => {
        const [result] = await db.query('DELETE FROM roles WHERE id_rol = ?', [id]);
        return result.affectedRows > 0;
    }
};

module.exports = Rol;
