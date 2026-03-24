-- =====================================================
-- 1. CONSULTA DE FACTURACIÓN   
-- =====================================================

SELECT 
    f.numero_factura AS 'Factura_No',
    f.fecha AS 'Fecha_Venta',
    u.nombre AS 'Nombre_Cliente',
    u.numero_documento AS 'Documento_Cliente',
    cat.nombre AS 'Categoria',
    p.nombre AS 'Producto',
    df.cantidad AS 'Cant',
    df.precio_unitario AS 'Precio_Unit_COP',
    df.subtotal AS 'Subtotal_Item',
    f.total AS 'Total_Factura',
    f.estado AS 'Estado_Pago'
FROM facturas f
INNER JOIN pedidos ped ON f.pedido_id = ped.id_pedido
INNER JOIN usuarios u ON ped.usuario_id = u.id_usuario
INNER JOIN detalle_factura df ON f.id_factura = df.factura_id
INNER JOIN productos p ON df.producto_id = p.id_producto
INNER JOIN categorias cat ON p.categoria_id = cat.id_categoria
ORDER BY f.fecha DESC;

-- =====================================================
-- 2. Consulta de Inventario y Abastecimiento
-- =====================================================

SELECT 
    p.id_producto AS 'ID',
    p.nombre AS 'Producto',
    c.nombre AS 'Categoria',
    prov.nombre AS 'Proveedor_Principal',
    p.stock_actual AS 'Stock',
    p.stock_minimo AS 'Minimo',
    p.precio_compra AS 'Costo_COP',
    p.precio_venta AS 'Venta_COP',
    (p.precio_venta - p.precio_compra) AS 'Ganancia_Estimada'
FROM productos p
INNER JOIN categorias c ON p.categoria_id = c.id_categoria
LEFT JOIN proveedores prov ON p.proveedor_id = prov.id_proveedor
ORDER BY p.nombre ASC;

-- =====================================================
-- 3. Consulta de Seguridad y Acceso (Login/Roles)
-- =====================================================

SELECT 
    u.id_usuario,
    u.nombre AS 'Nombre_Completo',
    u.email AS 'Correo_Electronico',
    r.nombre AS 'Rol_Asignado',
    r.descripcion AS 'Permisos_Del_Rol',
    u.activo AS 'Cuenta_Activa'
FROM usuarios u
INNER JOIN roles r ON u.rol_id = r.id_rol;