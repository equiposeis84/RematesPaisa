const express = require('express');
const router = express.Router();
const usuarioController = require('../controllers/usuarioController');
const { verificarToken } = require('../middleware/authMiddleware');

// Rutas PÚBLICAS (no requieren token)
router.post('/login', usuarioController.login);             // POST /api/usuarios/login
router.get('/roles', usuarioController.getRoles); // GET /api/usuarios/roles

// Rutas PROTEGIDAS (requieren JWT válido)
router.get('/', verificarToken, usuarioController.getAll);          // GET /api/usuarios
router.get('/:id', verificarToken, usuarioController.getOne);       // GET /api/usuarios/1
router.post('/', usuarioController.store);          // POST /api/usuarios
router.put('/:id', verificarToken, usuarioController.update);       // PUT /api/usuarios/1
router.delete('/:id', verificarToken, usuarioController.destroy);   // DELETE /api/usuarios/1

module.exports = router;