const express = require('express');
const router = express.Router();
const categoriaController = require('../controllers/categoriaController');

router.get('/', categoriaController.getAll);
router.get('/:id', categoriaController.getOne);
router.post('/', categoriaController.store);
router.put('/:id', categoriaController.update);
router.delete('/:id', categoriaController.destroy);

module.exports = router;
