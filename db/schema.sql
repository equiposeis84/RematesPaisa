-- DROP DATABASE IF EXISTS sistema_comercial ;
-- =====================================================
-- CREAR BASE DE DATOS
-- =====================================================
CREATE DATABASE IF NOT EXISTS sistema_comercial
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sistema_comercial;

-- =====================================================
-- TABLA ROLES
-- =====================================================
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

-- =====================================================
-- TABLA USUARIOS
-- =====================================================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    rol_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tipo_documento VARCHAR(10),
    numero_documento VARCHAR(30),
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id_rol)
);

CREATE INDEX idx_usuario_documento ON usuarios(numero_documento);

-- =====================================================
-- TABLA PROVEEDORES
-- =====================================================
CREATE TABLE proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nit VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(30),
    correo VARCHAR(120),
    direccion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE
);

-- =====================================================
-- TABLA CATEGORIAS
-- =====================================================
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

-- =====================================================
-- TABLA PRODUCTOS
-- =====================================================
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    proveedor_id INT,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    precio_compra DECIMAL(12,2) NOT NULL,
    precio_venta DECIMAL(12,2) NOT NULL,
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id_categoria),
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id_proveedor)
);

-- =====================================================
-- TABLA MOVIMIENTOS INVENTARIO
-- =====================================================
CREATE TABLE movimientos_inventario (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_movimiento ENUM('ENTRADA', 'SALIDA', 'AJUSTE', 'VENTA', 'COMPRA') NOT NULL,
    referencia VARCHAR(100),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario)
);

-- =====================================================
-- DETALLE MOVIMIENTOS INVENTARIO
-- =====================================================
CREATE TABLE detalle_movimiento (
    id_detalle_mov INT AUTO_INCREMENT PRIMARY KEY,
    movimiento_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    costo_unitario DECIMAL(12,2),
    FOREIGN KEY (movimiento_id) REFERENCES movimientos_inventario(id_movimiento) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id_producto)
);

-- =====================================================
-- TABLA COMPRAS
-- =====================================================
CREATE TABLE compras (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    proveedor_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    subtotal DECIMAL(12,2),
    impuesto DECIMAL(12,2),
    total DECIMAL(12,2),
    estado ENUM('PENDIENTE','PAGADA','ANULADA') DEFAULT 'PENDIENTE',
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id_proveedor),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario)
);

-- =====================================================
-- DETALLE COMPRAS
-- =====================================================
CREATE TABLE detalle_compra (
    id_detalle_compra INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id_compra) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id_producto)
);

-- =====================================================
-- TABLA PEDIDOS (VENTAS)
-- =====================================================
CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(12,2),
    impuesto DECIMAL(12,2),
    total DECIMAL(12,2),
    estado ENUM('PENDIENTE', 'PAGADO', 'ENTREGADO', 'CANCELADO') DEFAULT 'PENDIENTE',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario)
);

-- =====================================================
-- DETALLE PEDIDOS
-- =====================================================
CREATE TABLE detalle_pedido (
    id_detalle_pedido INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id_producto)
);

-- =====================================================
-- TABLA FACTURAS
-- =====================================================
CREATE TABLE facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    numero_factura VARCHAR(50) UNIQUE,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(12,2),
    impuesto DECIMAL(12,2),
    total DECIMAL(12,2),
    estado ENUM('EMITIDA','ANULADA','PAGADA') DEFAULT 'EMITIDA',
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id_pedido)
);

-- =====================================================
-- DETALLE FACTURAS
-- =====================================================
CREATE TABLE detalle_factura (
    id_detalle_factura INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (factura_id) REFERENCES facturas(id_factura) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id_producto)
);