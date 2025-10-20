USE mydb;
SHOW TABLES;
DESCRIBE pedidos;

INSERT INTO pedidos (
	idPedidos,
	fechaPedido,
	horaPedido,
	idCliente,
	valorPedido,
	ivaPedido,
	totalPedido,
	estadoPedido,
	repartidorPedido
)
VALUES
(1, '2025-10-20', '13:45:00', '1034865921', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez');

 -- Ver los datos insertados
SELECT * FROM pedidos;