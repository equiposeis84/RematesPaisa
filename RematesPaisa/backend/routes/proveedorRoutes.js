const express = require('express');
const router = express.Router();
const proveedorController = require('../controllers/proveedorController');

router.get('/', proveedorController.getAll);
router.get('/:id', proveedorController.getOne);
router.post('/', proveedorController.store);
router.put('/:id', proveedorController.update);
router.delete('/:id', proveedorController.destroy);

module.exports = router;
