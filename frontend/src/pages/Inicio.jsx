import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Users, Tags, Package, ShoppingCart, Receipt, Truck, Shield } from 'lucide-react';

const Inicio = () => {
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const userData = localStorage.getItem('user');
    if (userData) {
      setUser(JSON.parse(userData));
    }
  }, []);

  const features = [
    { title: 'Usuarios', desc: 'Gestiona los clientes y administradores.', icon: Users, path: '/usuarios', color: '#3b82f6' },
    { title: 'Roles', desc: 'Niveles de acceso del sistema.', icon: Shield, path: '/roles', color: '#8b5cf6' },
    { title: 'Categorías', desc: 'Organiza los productos por tipo.', icon: Tags, path: '/categorias', color: '#10b981' },
    { title: 'Productos', desc: 'Catálogo e inventario comercial.', icon: Package, path: '/productos', color: '#f59e0b' },
    { title: 'Proveedores', desc: 'Empresas que surten los productos.', icon: Truck, path: '/proveedores', color: '#64748b' },
    { title: 'Pedidos', desc: 'Órdenes de compra de los clientes.', icon: ShoppingCart, path: '/pedidos', color: '#ef4444' },
    { title: 'Facturas', desc: 'Registro contable de las ventas.', icon: Receipt, path: '/facturas', color: '#06b6d4' }
  ];

  return (
    <>
      <header className="main-header">
        <div>
          <h1 style={{ marginBottom: '0.25rem' }}>Panel de Inicio</h1>
          <p style={{ color: '#64748b', margin: 0, fontSize: '1.05rem' }}>Bienvenido de nuevo, <strong>{user ? user.nombre : 'Administrador'}</strong></p>
        </div>
      </header>

      <div style={{ padding: '2rem', display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))', gap: '1.5rem' }}>
        {features.map((feat, idx) => {
          const Icon = feat.icon;
          return (
            <div 
              key={idx} 
              className="modal-box" 
              style={{ position: 'relative', transform: 'none', margin: 0, cursor: 'pointer', transition: 'transform 0.2s', borderTop: `4px solid ${feat.color}` }}
              onClick={() => navigate(feat.path)}
              onMouseEnter={(e) => e.currentTarget.style.transform = 'translateY(-5px)'}
              onMouseLeave={(e) => e.currentTarget.style.transform = 'none'}
            >
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '1rem' }}>
                <div style={{ backgroundColor: `${feat.color}20`, padding: '0.75rem', borderRadius: '8px' }}>
                  <Icon size={28} color={feat.color} />
                </div>
                <h3 style={{ margin: 0, fontSize: '1.25rem' }}>{feat.title}</h3>
              </div>
              <p style={{ color: '#64748b', fontSize: '0.9rem', margin: 0 }}>{feat.desc}</p>
            </div>
          );
        })}
      </div>
    </>
  );
};

export default Inicio;
