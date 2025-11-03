-- ========================================
-- TABLA: Productos
-- ========================================
INSERT INTO Productos (
  idProductos
  nombreProducto
  entradaProducto
  salidaProducto
  categoriaProducto
  idProveedores
) VALUES
1,'Juego de utensilios de teflon',50,0,'Cocina',0

-- ========================================
-- TABLA: Cliente
-- ========================================
INSERT INTO Cliente (
  idCliente,
  tipoDocumentoCliente,
  nombreCliente,
  apellidoCliente,
  direccionCliente,
  telefonoCliente,
  emailCliente
) VALUES
('1031809518', 'CC', 'Samuel', 'Arevalo', 'Calle 132 #103-22', '3507054141', 'samuel.arevalo@example.com'),
('1013602451', 'CC', 'Thomas', 'Barrero', 'Transversal 16A #40-31 Sur', '3001234567', 'thomas.barrero@example.com'),
('1030531015', 'CC', 'Nicolas', 'Aguirre', 'Calle 3B #41A-37', '3507285394', 'nicolas.aguirre@example.com'),
('1030402020', 'CC', 'Laura', 'Martinez', 'Carrera 10 #25-15', '3201112233', 'laura.martinez@example.com'),
('1030203030', 'CC', 'Carlos', 'Gomez', 'Calle 45 #12-30', '3105556677', 'carlos.gomez@example.com'),
('1030504040', 'CC', 'Daniela', 'Ruiz', 'Avenida 5 #8-22', '3004445566', 'daniela.ruiz@example.com'),
('1030605050', 'CC', 'Julian', 'Torres', 'Transversal 8 #45-50', '3017778899', 'julian.torres@example.com'),
('1030706060', 'CC', 'Paula', 'Hernandez', 'Calle 90 #30-15', '3028889900', 'paula.hernandez@example.com'),
('1030807070', 'CC', 'Miguel', 'Sanchez', 'Carrera 22 #14-60', '3036667788', 'miguel.sanchez@example.com'),
('1030908080', 'CC', 'Sara', 'Jimenez', 'Avenida Central #123', '3049998877', 'sara.jimenez@example.com');

-- ========================================
-- TABLA: Pedidos (10 registros)
-- ========================================
INSERT INTO Pedidos (
  idPedidos,
  fechaPedido,
  horaPedido,
  idCliente,
  valorPedido,
  ivaPedido,
  totalPedido
) VALUES
(1, '2025-10-10', '10:15:00', '1031809518', 45000.00, '19%', 53550.00),
(2, '2025-10-10', '11:30:00', '1013602451', 85000.00, '19%', 101150.00),
(3, '2025-10-11', '09:45:00', '1030531015', 120000.00, '19%', 142800.00),
(4, '2025-10-11', '14:05:00', '1030402020', 67000.00, '19%', 79730.00),
(5, '2025-10-12', '13:25:00', '1030203030', 30000.00, '19%', 35700.00),
(6, '2025-10-12', '16:00:00', '1030504040', 98000.00, '19%', 116620.00),
(7, '2025-10-13', '08:30:00', '1030605050', 64000.00, '19%', 76160.00),
(8, '2025-10-13', '15:10:00', '1030706060', 56000.00, '19%', 66640.00),
(9, '2025-10-14', '17:50:00', '1030807070', 132000.00, '19%', 157080.00),
(10, '2025-10-14', '19:15:00', '1030908080', 42000.00, '19%', 49980.00);

-- ========================================
-- TABLA: Venta
-- ========================================

INSERT INTO Venta (
    idVenta
    idPedidos
    fechaVenta
    horaVenta
    totalVenta
    ivaVenta
    pagaVenta
) VALUES

('01','1','2025-10-10', '10:15:00', 53550.00, '19%', '60000.00'),
('02','2','2025-10-10', '11:30:00', 101150.00, '19%', '110000.00'),
('03','3','2025-10-11', '09:45:00', 142800.00, '19%', '150000.00'),
('04','4','2025-10-11', '14:05:00', 79730.00, '19%', '80000.00'),
('05','5','2025-10-12', '13:25:00', 35700.00, '19%', '40000.00'),
('06','6','2025-10-12', '16:00:00', 116620.00, '19%', '120000.00'),
('07','7','2025-10-13', '08:30:00', 76160.00, '19%', '80000.00'),
('08','8','2025-10-13', '15:10:00', 66640.00, '19%', '70000.00'),
('09','9','2025-10-14', '17:50:00', 157080.00, '19%', '160000.00'),
('10','10','2025-10-14', '19:15:00', 49980.00, '19%', '50000.00');

-- ========================================
-- TABLA: Proveedores
-- ========================================

INSERT INTO Proveedores
(
  idProveedores,
  tipoDocumentoProveedor
  nombreProveedor
  telefonoProveedor
  correoProveedor
) VALUES
(1, 'CC', 'Proveedor A', '3001234567', 'proveedor.a@example.com'),
(2, 'CC', 'Proveedor B', '3012345678', 'proveedor.b@example.com'),
(3, 'CC', 'Proveedor C', '3023456789', 'proveedor.c@example.com'),
(4, 'CC', 'Proveedor D', '3034567890', 'proveedor.d@example.com'),
(5, 'CC', 'Proveedor E', '3045678901', 'proveedor.e@example.com'),
(6, 'CC', 'Proveedor F', '3056789012', 'proveedor.f@example.com'),
(7, 'CC', 'Proveedor G', '3067890123', 'proveedor.g@example.com'),
(8, 'CC', 'Proveedor H', '3078901234', 'proveedor.h@example.com'),
(9, 'CC', 'Proveedor I', '3089012345', 'proveedor.i@example.com'),
(10, 'CC', 'Proveedor J', '3090123456', 'proveedor.j@example.com');

-- ========================================
-- TABLA: detalleProductos
-- ========================================

INSERT INTO detalleProductos
(
  idPedido
  idProductos
  cantidadDetalleProducto
  valorUnitarioDetalleProducto
  totalPagarDetalleProducto
  totalPagarDetalleProducto
  totalPagarDetalleProducto
) VALUES
(1, 1, 2, 15000.00, 30000.00, 45000.00),
(2, 2, 1, 85000.00, 85000.00, 85000.00),
(3, 3, 3, 40000.00, 120000.00, 120000.00),
(4, 4, 1, 67000.00, 67000.00, 67000.00),
(5, 5, 1, 30000.00, 30000.00, 30000.00),
(6, 6, 2, 49000.00, 98000.00, 98000.00),
(7, 7, 4, 16000.00, 64000.00, 64000.00),
(8, 8, 2, 28000.00, 56000.00, 56000.00),
(9, 9, 3, 44000.00, 132000.00, 132000.00),
(10, 10, 1, 42000.00, 42000.00, 42000.00);

-- ========================================
-- TABLA: Entrada
-- ========================================

INSERT INTO Entrada
(
  idEntrada
  fechaEntrada
  reciboEntrada
  idProveedores
) VALUES
(1, '2025-10-01', 'REC-001', 1),
(2, '2025-10-02', 'REC-002', 2),
(3, '2025-10-03', 'REC-003', 3),
(4, '2025-10-04', 'REC-004', 4),
(5, '2025-10-05', 'REC-005', 5),
(6, '2025-10-06', 'REC-006', 6),
(7, '2025-10-07', 'REC-007', 7),
(8, '2025-10-08', 'REC-008', 8),
(9, '2025-10-09', 'REC-009', 9),
(10, '2025-10-10', 'REC-010', 10);

-- ========================================
-- TABLA: detallesEntradas
-- ========================================

INSERT INTO detallesEntradas
(
  idEntrada
  idProductos
  cantidadEntradaDetalle
  precioUEntradaDetalle
  precioTEntradaDetalle
) VALUES
(1, 1, 100, 15000.00, 1500000.00),
(2, 2, 50, 85000.00, 4250000.00),
(3, 3, 200, 40000.00, 8000000.00),
(4, 4, 80, 67000.00, 5360000.00),
(5, 5, 150, 30000.00, 4500000.00),
(6, 6, 60, 49000.00, 2940000.00),
(7, 7, 120, 16000.00, 1920000.00),
(8, 8, 90, 28000.00, 2520000.00),
(9, 9, 70, 44000.00, 3080000.00),
(10, 10, 110, 42000.00, 4620000.00);

-- ========================================
-- TABLA: Roles
-- ========================================

INSERT INTO Roles
(
  idRoles
  nombreRoles
) VALUES
(1, 'Administrador'),
(2, 'Repartidor'),
(3, 'Cliente'),
(4, 'Usuario');

-- ========================================
-- TABLA: Usuarios
-- ========================================

INSERTO INTO Usuarios
(
  nombreUsuario
  passwordUsuario
  idRoles
) VALUES
('admin', 'admin123', 1),
('repartidor1', 'repartidor123', 2),
('cliente1', 'cliente123', 3),
('usuario1', 'usuario123', 4);
