const express = require('express');
const router = express.Router();
const facturaController = require('../controllers/facturaController');

router.get('/', facturaController.getAll);
router.get('/:id', facturaController.getOne);
router.post('/', facturaController.store);
router.put('/:id', facturaController.update);
router.delete('/:id', facturaController.destroy);

module.exports = router;
