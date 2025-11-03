USE mydb;
SHOW TABLES;
DESCRIBE detalleproductos;

INSERT INTO detalleproductos (
idPedido,
idProductos,
cantidadDetalleProducto,
valorUnitarioDetalleProducto,
totalPagarDetalleProducto,
ivaDetalleProducto,
totalDetalleProducto
)
VALUES 
(1, 1, '5', '5500', '50000.00', '9500.00', '59500.00');

select * from Pedidos;

select * from productos;

select * from detalleproductos;

select p.idProducto,.nombreProducto
