const express = require('express');
const router = express.Router();
const productoController = require('../controllers/productoController');

router.get('/', productoController.getAll);
router.get('/:id', productoController.getOne);
router.post('/', productoController.store);
router.put('/:id', productoController.update);
router.delete('/:id', productoController.destroy);

module.exports = router;
