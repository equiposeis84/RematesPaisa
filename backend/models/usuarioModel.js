const db = require('../config/db');

const Usuario = {
    // LEER TODOS: Incluimos todos los campos necesarios para la tabla
    findAll: async () => {
        const [rows] = await db.query(`
            SELECT u.id_usuario, 
            u.rol_id, u.nombre, u.email, 
            u.tipo_documento, 
            u.numero_documento, 
            u.telefono, 
            u.direccion, 
            u.activo, 
            r.nombre AS rol_nombre 
            FROM usuarios u 
            INNER JOIN roles r ON u.rol_id = r.id_rol
            ORDER BY u.id_usuario ASC
        `);
        return rows;
    },

    // LEER UNO SOLO
    findById: async (id) => {
        const [rows] = await db.query(`
            SELECT u.*, r.nombre AS rol_nombre 
            FROM usuarios u 
            INNER JOIN roles r ON u.rol_id = r.id_rol 
            WHERE u.id_usuario = ?`, [id]);
        return rows[0];
    },

    // LEER POR EMAIL (Para Login)
    findByEmail: async (email) => {
        const [rows] = await db.query(`
            SELECT u.*, r.nombre AS rol_nombre 
            FROM usuarios u 
            INNER JOIN roles r ON u.rol_id = r.id_rol 
            WHERE u.email = ?`, [email]);
        return rows[0];
    },

    // CREAR (Create): Agregamos tipo_documento, telefono y direccion
    create: async (data) => {
        const {
            rol_id, nombre, email, password,
            tipo_documento, numero_documento, telefono, direccion
        } = data;

        const [result] = await db.query(
            `INSERT INTO usuarios 
            (rol_id, 
            nombre, 
            email, 
            password, 
            tipo_documento, 
            numero_documento, 
            telefono, 
            direccion, 
            activo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)`,
            [rol_id, nombre, email, password, tipo_documento, numero_documento, telefono, direccion]
        );
        return result.insertId;
    },

    // ACTUALIZAR (Update): Sincronizado con todos los campos del modal
    update: async (id, data) => {
        const {
            rol_id, nombre, email, tipo_documento,
            numero_documento, telefono, direccion, activo
        } = data;

        const [result] = await db.query(
            `UPDATE usuarios SET 
                rol_id = ?, 
                nombre = ?, 
                email = ?, 
                tipo_documento = ?, 
                numero_documento = ?, 
                telefono = ?, 
                direccion = ?, 
                activo = ? 
            WHERE id_usuario = ?`,
            [rol_id, nombre, email, tipo_documento, numero_documento, telefono, direccion, activo, id]
        );
        return result.affectedRows > 0;
    },

    // ELIMINAR (Delete)
    delete: async (id) => {
        const [result] = await db.query('DELETE FROM usuarios WHERE id_usuario = ?', [id]);
        return result.affectedRows > 0;
    },

    // OBTENER ROLES
    getRoles: async () => {
        const [rows] = await db.query('SELECT * FROM roles');
        return rows;
    }
};

module.exports = Usuario;