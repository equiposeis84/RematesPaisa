-- Crear la base de datos solo si no existe
CREATE DATABASE IF NOT EXISTS mydb;
USE mydb;

-- Configuración inicial
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

-- ===========================
-- TABLAS PRINCIPALES
-- ===========================

CREATE TABLE IF NOT EXISTS `cliente` (
  `idCliente` VARCHAR(40) NOT NULL,
  `tipoDocumentoCliente` VARCHAR(45) NOT NULL,
  `nombreCliente` VARCHAR(45) NOT NULL,
  `apellidoCliente` VARCHAR(45) NOT NULL,
  `direccionCliente` VARCHAR(45) NOT NULL,
  `telefonoCliente` VARCHAR(45) NOT NULL,
  `emailCliente` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `proveedores` (
  `idProveedores` INT(11) NOT NULL,
  `tipoDocumentoProveedor` VARCHAR(45) NOT NULL,
  `nombreProveedor` VARCHAR(45) NOT NULL,
  `telefonoProveedor` VARCHAR(45) NOT NULL,
  `correoProveedor` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idProveedores`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `productos` (
  `idProductos` INT NOT NULL AUTO_INCREMENT,
  `nombreProducto` VARCHAR(45) NOT NULL,
  `entradaProducto` INT NOT NULL,
  `salidaProducto` INT NOT NULL,
  `categoriaProducto` VARCHAR(45) NOT NULL,
  `idProveedores` INT NOT NULL,
  `precioUnitario` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`idProductos`),
  KEY `idx_idProveedores` (`idProveedores`),
  CONSTRAINT `fk_productos_proveedores`
    FOREIGN KEY (`idProveedores`)
    REFERENCES `proveedores` (`idProveedores`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `entrada` (
  `idEntrada` INT(11) NOT NULL,
  `fechaEntrada` DATE NOT NULL,
  `reciboEntrada` FLOAT(10,2) NOT NULL,
  `idProveedores` INT(11) NOT NULL,
  PRIMARY KEY (`idEntrada`),
  KEY `idx_idProveedoresEntrada` (`idProveedores`),
  CONSTRAINT `fk_entrada_proveedores` FOREIGN KEY (`idProveedores`) REFERENCES `proveedores` (`idProveedores`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `pedidos` (
  `idPedidos` INT(11) NOT NULL,
  `fechaPedido` DATE NOT NULL,
  `horaPedido` TIME NOT NULL,
  `idCliente` VARCHAR(40) NOT NULL,
  `valorPedido` FLOAT(10,2) NOT NULL,
  `ivaPedido` VARCHAR(45) NOT NULL,
  `totalPedido` FLOAT(10,2) NOT NULL,
  PRIMARY KEY (`idPedidos`),
  KEY `idx_idCliente` (`idCliente`),
  CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`idCliente`) REFERENCES `cliente` (`idCliente`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `detalleproductos` (
  `idPedido` INT(5) NOT NULL,
  `idProductos` INT(11) NOT NULL,
  `cantidadDetalleProducto` INT(5) NOT NULL,
  `valorUnitarioDetalleProducto` FLOAT(10,2) NOT NULL,
  `totalPagarDetalleProducto` FLOAT(10,2) NOT NULL,
  `ivaDetalleProducto` FLOAT(10,2) NOT NULL,
  `totalDetalleProducto` FLOAT(10,2) NOT NULL,
  KEY `idx_idPedido` (`idPedido`),
  KEY `idx_idProductos` (`idProductos`),
  CONSTRAINT `fk_detalle_pedidos` FOREIGN KEY (`idPedido`) REFERENCES `pedidos` (`idPedidos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_detalle_productos` FOREIGN KEY (`idProductos`) REFERENCES `productos` (`idProductos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `detallesentradas` (
  `idEntradas` INT(11) NOT NULL,
  `idProductos` INT(11) NOT NULL,
  `cantidadEntradaDetalle` INT(11) NOT NULL,
  `precioUEntradaDetalle` FLOAT(10,2) NOT NULL,
  `precioTEntradaDetalle` FLOAT(10,2) NOT NULL,
  KEY `idx_idEntradas` (`idEntradas`),
  KEY `idx_idProductos` (`idProductos`),
  CONSTRAINT `fk_detallesEntradas_entrada` FOREIGN KEY (`idEntradas`) REFERENCES `entrada` (`idEntrada`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_detallesEntradas_productos` FOREIGN KEY (`idProductos`) REFERENCES `productos` (`idProductos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `idRoles` INT(5) NOT NULL,
  `nombreRoles` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idRoles`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `nombreUsuario` VARCHAR(40) NOT NULL,
  `passwordUsuario` VARCHAR(45) NOT NULL,
  `idRoles` INT(5) NOT NULL,
  PRIMARY KEY (`nombreUsuario`),
  KEY `idx_idRoles` (`idRoles`),
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`idRoles`) REFERENCES `roles` (`idRoles`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `venta` (
  `idVenta` INT(11) NOT NULL,
  `idPedidos` INT(11) NOT NULL,
  `fechaVenta` DATE NOT NULL,
  `horaVenta` TIME NOT NULL,
  `totalVenta` FLOAT(10,2) NOT NULL,
  `ivaVenta` FLOAT(10,2) NOT NULL,
  `pagaVenta` FLOAT(10,2) NOT NULL,
  PRIMARY KEY (`idVenta`),
  KEY `idx_idPedidos` (`idPedidos`),
  CONSTRAINT `fk_venta_pedidos` FOREIGN KEY (`idPedidos`) REFERENCES `pedidos` (`idPedidos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

COMMIT;

 /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



