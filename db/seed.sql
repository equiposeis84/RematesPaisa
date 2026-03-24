USE sistema_comercial;

-- =====================================================
-- 1. TABLA: ROLES (Refactorizado)
-- =====================================================
INSERT INTO roles (
    id_rol, 
    nombre, 
    descripcion
) 
    VALUES
(1, 'Administrador', 'Control total: Gestión de productos, reportes y permisos'),
(2, 'Cliente', 'Usuario registrado: Puede ver productos y realizar compras'),
(3, 'Usuario', 'Visitante: Solo puede visualizar el catálogo de remate');

-- =====================================================
-- 2. TABLA: USUARIOS
-- =====================================================
INSERT INTO usuarios (
    rol_id, 
    nombre, 
    email, 
    password, 
    numero_documento
) 
VALUES
(1, 'Sebastian Administrador', 'admin@remate.com', 'admin_secure_hash', '10102020'),
(2, 'Juan Cliente', 'juan.perez@email.com', 'cliente_hash_123', '80809090'),
(2, 'Maria Compra', 'maria.luz@email.com', 'cliente_hash_456', '70706060');

-- =====================================================
-- 3. TABLA: CATEGORIAS 
-- =====================================================
INSERT INTO categorias (
    id_categoria, 
    nombre
    )
    VALUES
(1, 'Cocina'), 
(2, 'Decoración'), 
(3, 'Baño'), 
(4, 'Infantil'),
(5, 'Lavandería'), 
(6, 'Hogar'), 
(7, 'Mascotas'), 
(8, 'Electrónica'),
(9, 'Organización'), 
(10, 'Muebles'), 
(11, 'Jardín');

-- =====================================================
-- 3.5 TABLA: PROVEEDORES
-- =====================================================
INSERT INTO proveedores (
    id_proveedor,
    nit,
    nombre,
    telefono,
    correo,
    direccion,
    activo
)
VALUES
(1, '900123456-1', 'Mega Plásticos S.A.', '3101234567', 'ventas@megaplasticos.com', 'Calle 10 # 20-30', 1),
(2, '800987654-2', 'Importaciones El Sol', '3209876543', 'contacto@importsol.com', 'Carrera 45 # 10-15', 1),
(3, '901234567-3', 'ElectroHogar Colombia', '3005556677', 'ventas@electrohogar.com', 'Avenida 68 # 50-22', 1),
(4, '890903938-8', 'Postobon S.A.', '3001112233', 'postobon@gmail.com', 'Calle 52 # 47-28', 1),
(5, '890900050-1', 'Grupo Nutresa', '3102223344', 'nutresa@gmail.com', 'Carrera 42 # 8-00', 1),
(6, '860000045-3', 'Alpina Productos Alimenticios', '3203334455', 'alpinaalimentos@gmail.com', 'Km 2 Vía Briceño', 1),
(7, '830112233-4', 'Juan Valdez Cafe', '3154445566', 'juanvaldez@gmail.com', 'Calle 73 # 8-13', 1),
(8, '860005224-5', 'Bavaria S.A.', '3125556677', 'bavaria@gmail.com', 'Carrera 53 # 127-60', 1),
(9, '890300114-6', 'Colombina S.A.', '3146667788', 'colombinaoficial@gmail.com', 'Calle 52 # 1-84', 1),
(10, '860002133-7', 'Ramo S.A.', '3007778899', 'ramoempresa@gmail.com', 'Av. Boyacá # 68-80', 1),
(11, '900888777-8', 'Roya Prestige S.A', '3118889900', 'royalprestige@gmail.com', 'Carrera 15 # 93-60', 1),
(12, '890111222-9', 'Yupi', '3209990011', 'yupi@gmail.com', 'Zona Franca del Cauca', 1),
(13, '860222333-0', 'Norma', '3100001122', 'norma@gmail.com', 'Calle 13 # 68-40', 1),
(14, '901333444-1', 'Peersal', '3121112233', 'peersal@gmail.com', 'Autopista Sur # 50-20', 1),
(15, '860444555-2', 'Offi-esco', '3002223344', 'offiesco@gmail.com', 'Carrera 7 # 156-68', 1);


-- =====================================================
-- 4. TABLA: PRODUCTOS
-- =====================================================
-- Precios en COP. Compra estimada al 60% del PVP.
INSERT INTO productos (
    id_producto, 
    categoria_id, 
    proveedor_id,
    nombre, 
    stock_actual, 
    stock_minimo, 
    precio_compra, 
    precio_venta
    ) 
    VALUES
(1, 1, 1, 'Juego de utensilios de teflon', 50, 5, 3300, 5500),
(2, 1, 1, 'Colador plastico #21', 50, 5, 600, 1000),
(3, 1, 1, 'Torre pocillo x4 grande', 50, 5, 13800, 23000),
(4, 6, 2, 'Masajeador recargable', 50, 5, 5400, 9000),
(5, 8, 3, 'Parlante robot', 50, 5, 13200, 22000),
(6, 1, 1, 'Termo FUA', 50, 5, 12000, 20000),
(7, 1, 2, 'Vaso pitillo en vidrio transportable', 50, 5, 4800, 8000),
(8, 1, 1, 'Juego de tarro chef x5', 50, 5, 20400, 34000),
(9, 10, 2, 'Butaca ratán', 50, 5, 4680, 7800),
(10, 11, 2, 'Matera roca cuadrado pequeña', 50, 5, 2100, 3500);

-- =====================================================
-- 5. TABLA: PEDIDOS (Realizados por Clientes)
-- =====================================================
INSERT INTO pedidos (usuario_id, subtotal, total, estado) VALUES
(2, 28500, 28500, 'PAGADO'), -- Compra de Juan
(3, 9000, 9000, 'ENTREGADO'); -- Compra de Maria

-- =====================================================
-- 6. TABLA: DETALLE_PEDIDO
-- =====================================================
INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(1, 14, 1, 23000, 23000), -- Torre pocillo
(1, 1, 1, 5500, 5500),    -- Utensilios
(2, 25, 1, 9000, 9000);   -- Masajeador

-- =====================================================
-- 7. TABLA: FACTURAS
-- =====================================================
INSERT INTO facturas (pedido_id, numero_factura, total, estado) VALUES
(1, 'REM-0001', 28500, 'PAGADA'),
(2, 'REM-0002', 9000, 'PAGADA');

-- =====================================================
-- 8. TABLA: DETALLE_FACTURA
-- =====================================================
INSERT INTO detalle_factura (factura_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(1, 14, 1, 23000, 23000),
(1, 1, 1, 5500, 5500),
(2, 25, 1, 9000, 9000);