const Categoria = require('../models/categoriaModel');

exports.getAll = async (req, res) => {
    try {
        const data = await Categoria.findAll();
        res.json(data);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.getOne = async (req, res) => {
    try {
        const row = await Categoria.findById(req.params.id);
        if (!row) return res.status(404).json({ message: "Categoría no encontrada" });
        res.json(row);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.store = async (req, res) => {
    try {
        const { nombre, descripcion } = req.body;
        if (!nombre) {
            return res.status(400).json({ message: "Faltan campos obligatorios" });
        }
        const id = await Categoria.create({ nombre, descripcion });
        res.status(201).json({ message: "Categoría creada con éxito", id_categoria: id });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.update = async (req, res) => {
    try {
        const { id } = req.params;
        const actualizado = await Categoria.update(id, req.body);
        if (!actualizado) {
            return res.status(404).json({ message: "Categoría no encontrada para actualizar" });
        }
        res.json({ message: "Categoría actualizada correctamente" });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};

exports.destroy = async (req, res) => {
    try {
        const { id } = req.params;
        const eliminado = await Categoria.delete(id);
        if (!eliminado) {
            return res.status(404).json({ message: "Categoría no encontrada" });
        }
        res.json({ message: "Categoría eliminada" });
    } catch (error) {
        if (error.code === 'ER_ROW_IS_REFERENCED_2') {
            return res.status(400).json({ 
                error: "No se puede eliminar esta categoría porque existen productos asociados a ella." 
            });
        }
        res.status(500).json({ error: error.message });
    }
};
