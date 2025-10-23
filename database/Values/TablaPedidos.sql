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
(1, '2025-10-20', '13:45:00', '1002456789', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez'),
(2, '2025-10-21', '12:45:00', '1034865921', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez'),
(3, '2025-10-22', '13:05:00', '1034692352', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez'),
(4, '2025-10-23', '15:35:00', '1067432893', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez'),
(5, '2025-10-24', '09:50:00', '1089345621', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez'),
(6, '2025-10-25', '07:45:00', '2843298452', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez'),
(7, '2025-10-26', '21:45:00', '1054826931', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez'),
(8, '2025-10-27', '14:35:00', '1078952146', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez'),
(9, '2025-10-28', '15:25:00', '1045689321', 50000.00, 9500.00, 59500.00, 'Pendiente', 'Juan Pérez');

 -- Ver los datos insertados
SELECT * FROM pedidos;