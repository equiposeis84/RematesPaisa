const jwt = require('jsonwebtoken');
const Usuario = require('../models/usuarioModel');

const SECRET_KEY = process.env.JWT_SECRET || "mi_clave_secreta_super_segura";

// Función para generar el token JWT
function generarToken(payload) {
    console.log("Generando token con expiración de 24 horas...");
    return jwt.sign(payload, SECRET_KEY, { expiresIn: '24h' });
}

// Listar todos
exports.getAll = async (req, res) => {
    try {
        const data = await Usuario.findAll();
        res.json(data);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

// Obtener uno solo por ID
exports.getOne = async (req, res) => {
    try {
        const user = await Usuario.findById(req.params.id);
        if (!user) {
            return res.status(404).json({ message: "Usuario no encontrado" });
        }
        res.json(user);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

// Crear nuevo usuario (Recibe todos los campos del formulario)
exports.store = async (req, res) => {
    try {
        // Extraemos explícitamente los campos para validar que lleguen
        const {
            rol_id, nombre, email, password,
            tipo_documento, numero_documento, telefono, direccion
        } = req.body;

        // Validamos que los campos obligatorios no estén vacíos
        if (!rol_id || !nombre || !email || !password) {
            return res.status(400).json({ message: "Faltan campos obligatorios" });
        }

        const id = await Usuario.create({
            rol_id, nombre, email, password,
            tipo_documento, numero_documento, telefono, direccion
        });

        res.status(201).json({
            message: "Usuario creado con éxito",
            id_usuario: id
        });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

// Actualizar usuario existente
exports.update = async (req, res) => {
    try {
        const { id } = req.params;

        // El body debe contener los campos actualizados y el estado 'activo'
        const actualizado = await Usuario.update(id, req.body);

        if (!actualizado) {
            return res.status(404).json({ message: "No se encontró el registro para actualizar" });
        }

        res.json({ message: "Usuario actualizado correctamente" });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

// Obtener lista de roles
exports.getRoles = async (req, res) => {
    try {
        const roles = await Usuario.getRoles();
        res.json(roles);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

// Eliminar usuario físicamente de la DB
exports.destroy = async (req, res) => {
    try {
        const { id } = req.params;
        const eliminado = await Usuario.delete(id);

        if (!eliminado) {
            return res.status(404).json({ message: "Usuario no encontrado" });
        }

        res.json({ message: "Usuario eliminado físicamente de la base de datos" });
    } catch (error) {
        if (error.code === 'ER_ROW_IS_REFERENCED_2') {
            return res.status(400).json({
                error: "No se puede eliminar este usuario porque ya tiene registros asociados (ventas, compras, etc.). Te recomendamos editarlo y cambiar su estado a 'Inactivo'."
            });
        }
        res.status(500).json({ error: error.message });
    }
};

// Autenticación de Usuario con JWT
exports.login = async (req, res) => {
    try {
        const { email, password } = req.body;
        const user = await Usuario.findByEmail(email);

        if (!user || user.password !== password) {
            return res.status(401).json({ message: "Correo o contraseña incorrectos" });
        }

        if (!user.activo) {
            return res.status(403).json({ message: "La cuenta está inactiva. Contacta soporte." });
        }

        delete user.password; // Evitamos enviar la contraseña al cliente en texto plano

        // Generamos el JWT con datos del usuario (expira en 24 horas)
        const payload = {
            userId: user.id_usuario,
            user: user.nombre,
            rol_id: user.rol_id
        };
        const token = generarToken(payload);
        console.log(`Token generado: ${token.substring(0, 20)}...`);

        res.json({ message: "¡Inicio de sesión exitoso!", token, user });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};