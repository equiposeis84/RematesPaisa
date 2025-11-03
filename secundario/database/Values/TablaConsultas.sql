USE mydb ;

SELECT
    -- datos de factura (cliente)
    dato.idCliente,
    dato.tipoDocumentoCliente,
    dato.nombreCliente,
    dato.apellidoCliente,
    
    -- datos de factura (pedidos)
    compra.idPedidos,
    compra.fechaPedido,
    compra.horaPedido,
    compra.valorPedido,
    compra.ivaPedido,
    compra.totalPedido,
    
    -- datos del detalle y productos
    detalle.idProductos,
    producto.nombreProducto,
    detalle.cantidadDetalleProducto,
    detalle.valorUnitarioDetalleProducto,
    detalle.totalPagarDetalleProducto,
    detalle.ivaDetalleProducto,
    detalle.totalDetalleProducto

FROM cliente dato
INNER JOIN pedidos compra
    ON dato.idCliente = compra.idCliente
INNER JOIN detalleproductos detalle
    ON compra.idPedidos = detalle.idPedido
INNER JOIN productos producto
    ON detalle.idProductos = producto.idProductos;
DESCRIBE productos ;
DESCRIBE proveedores ;
-- productos proveedores
SELECT 
    dato.idProveedores,
    dato.tipoDocumentoProveedor,
    dato.nombreProveedor,
    inventario.idProductos,
    inventario.nombreProducto
FROM proveedores dato
INNER JOIN productos inventario
    ON dato.idProveedores = inventario.idProveedores;
-- inventario 

-- Ver productos
SELECT * FROM productos;
-- Contar cuántos productos hay
SELECT COUNT(*) AS entradaProducto FROM productos;
-- Sumar todos los pedidos
SELECT SUM(totalPedido) AS totalPedidos FROM pedidos;
-- Calcular promedio de los totales
SELECT AVG(totalPedido) AS promedioTotalPedido FROM pedidos;
-- Valor total inventario
SELECT 
    SUM(precioUnitario * entradaProducto) AS valorTotalInventario
FROM productos;
SHOW TABLES;




