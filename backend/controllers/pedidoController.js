const Pedido = require('../models/pedidoModel');

exports.getAll = async (req, res) => {
    try {
        const data = await Pedido.findAll();
        res.json(data);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.getOne = async (req, res) => {
    try {
        const row = await Pedido.findById(req.params.id);
        if (!row) return res.status(404).json({ message: "Pedido no encontrado" });
        res.json(row);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.store = async (req, res) => {
    try {
        const { usuario_id } = req.body;
        if (!usuario_id) return res.status(400).json({ message: "El ID del usuario es obligatorio" });
        
        const id = await Pedido.create(req.body);
        res.status(201).json({ message: "Pedido creado con éxito", id_pedido: id });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.update = async (req, res) => {
    try {
        const { id } = req.params;
        const actualizado = await Pedido.update(id, req.body);
        if (!actualizado) return res.status(404).json({ message: "Pedido no encontrado" });
        res.json({ message: "Pedido actualizado" });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.destroy = async (req, res) => {
    try {
        const { id } = req.params;
        const eliminado = await Pedido.delete(id);
        if (!eliminado) return res.status(404).json({ message: "Pedido no encontrado" });
        res.json({ message: "Pedido eliminado" });
    } catch (error) {
        if (error.code === 'ER_ROW_IS_REFERENCED_2') {
            return res.status(400).json({ 
                error: "No se puede eliminar este pedido porque tiene facturas o detalles asociados." 
            });
        }
        res.status(500).json({ error: error.message });
    }
};
