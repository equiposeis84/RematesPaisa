const express = require('express');
const router = express.Router();
const rolController = require('../controllers/rolController');

router.get('/', rolController.getAll);
router.get('/:id', rolController.getOne);
router.post('/', rolController.store);
router.put('/:id', rolController.update);
router.delete('/:id', rolController.destroy);

module.exports = router;
