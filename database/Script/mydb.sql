USE mydb;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `Cliente` (
  `idCliente` varchar(40) NOT NULL,
  `tipoDocumentoCliente` varchar(45) NOT NULL,
  `nombreCliente` varchar(45) NOT NULL,
  `apellidoCliente` varchar(45) NOT NULL,
  `direccionCliente` varchar(45) NOT NULL,
  `telefonoCliente` varchar(45) NOT NULL,
  `emailCliente` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `detalleproductos` (
  `idPedido` int(5) NOT NULL,
  `idProductos` int(11) NOT NULL,
  `cantidadDetalleProducto` int(5) NOT NULL,
  `valorUnitarioDetalleProducto` float(10,2) NOT NULL,
  `totalPagarDetalleProducto` float(10,2) NOT NULL,
  `ivaDetalleProducto` float(10,2) NOT NULL,
  `totalDetalleProducto` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `detallesentradas` (
  `idEntradas` int(11) NOT NULL,
  `idProductos` int(11) NOT NULL,
  `cantidadEntradaDetalle` int(11) NOT NULL,
  `precioUEntradaDetalle` float(10,2) NOT NULL,
  `precioTEntradaDetalle` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `entrada` (
  `idEntrada` int(11) NOT NULL,
  `fechaEntrada` date NOT NULL,
  `reciboEntrada` float(10,2) NOT NULL,
  `idProveedores` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `pedidos` (
  `idPedidos` int(11) NOT NULL,
  `fechaPedido` date NOT NULL,
  `horaPedido` time NOT NULL,
  `idCliente` varchar(40) NOT NULL,
  `valorPedido` float(10,2) NOT NULL,
  `ivaPedido` varchar(45) NOT NULL,
  `totalPedido` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `productos` (
  `idProductos` int(11) NOT NULL,
  `nombreProducto` varchar(45) NOT NULL,
  `entradaProducto` int(5) NOT NULL,
  `salidaProducto` int(5) NOT NULL,
  `categoriaProducto` varchar(45) NOT NULL,
  `idProveedores` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `proveedores` (
  `idProveedores` int(11) NOT NULL,
  `tipoDocumentoProveedor` varchar(45) NOT NULL,
  `nombreProveedor` varchar(45) NOT NULL,
  `telefonoProveedor` varchar(45) NOT NULL,
  `correoProveedor` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `roles` (
  `idRoles` int(5) NOT NULL,
  `nombreRoles` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `usuarios` (
  `nombreUsuario` varchar(40) NOT NULL,
  `passwordUsuario` varchar(45) NOT NULL,
  `idRoles` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `venta` (
  `idVenta` int(11) NOT NULL,
  `idPedidos` int(11) NOT NULL,
  `fechaVenta` date NOT NULL,
  `horaVenta` time NOT NULL,
  `totalVenta` float(10,2) NOT NULL,
  `ivaVenta` float(10,2) NOT NULL,
  `pagaVenta` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idCliente`);

ALTER TABLE `detalleproductos`
  ADD KEY `idx_idPedido` (`idPedido`),
  ADD KEY `idx_idProductos` (`idProductos`);

ALTER TABLE `detallesentradas`
  ADD KEY `idx_idEntradas` (`idEntradas`),
  ADD KEY `idx_idProductos` (`idProductos`);

ALTER TABLE `entrada`
  ADD PRIMARY KEY (`idEntrada`),
  ADD KEY `idx_idProveedoresEntrada` (`idProveedores`);

ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`idPedidos`),
  ADD KEY `idx_idCliente` (`idCliente`);

ALTER TABLE `productos`
  ADD PRIMARY KEY (`idProductos`),
  ADD KEY `idx_idProveedores` (`idProveedores`);

ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`idProveedores`);

ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRoles`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`nombreUsuario`),
  ADD KEY `idx_idRoles` (`idRoles`);

ALTER TABLE `venta`
  ADD PRIMARY KEY (`idVenta`),
  ADD KEY `idx_idPedidos` (`idPedidos`);

--
-- Filtros para la tabla `detalleproductos`
--
ALTER TABLE `detalleproductos`
  ADD CONSTRAINT `fk_detalle_pedidos` FOREIGN KEY (`idPedido`) REFERENCES `pedidos` (`idPedidos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_detalle_productos` FOREIGN KEY (`idProductos`) REFERENCES `productos` (`idProductos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `detallesentradas`
--
ALTER TABLE `detallesentradas`
  ADD CONSTRAINT `fk_detallesEntradas_entrada` FOREIGN KEY (`idEntradas`) REFERENCES `entrada` (`idEntrada`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_detallesEntradas_productos` FOREIGN KEY (`idProductos`) REFERENCES `productos` (`idProductos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `entrada`
--
ALTER TABLE `entrada`
  ADD CONSTRAINT `fk_entrada_proveedores` FOREIGN KEY (`idProveedores`) REFERENCES `proveedores` (`idProveedores`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`idCliente`) REFERENCES `cliente` (`idCliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_proveedores` FOREIGN KEY (`idProveedores`) REFERENCES `proveedores` (`idProveedores`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`idRoles`) REFERENCES `roles` (`idRoles`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `fk_venta_pedidos` FOREIGN KEY (`idPedidos`) REFERENCES `pedidos` (`idPedidos`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
