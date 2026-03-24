import React from 'react';
import { NavLink } from 'react-router-dom';
import { Shield, Users, Tags, Package, ShoppingCart, Receipt, Truck, X, Layers, LogOut, UserCircle } from 'lucide-react';
import { logout } from '../services/authService';
import './Sidebar.css';

const Sidebar = ({ isOpen, setIsOpen, onLogout }) => {
  const userStr = localStorage.getItem('user');
  const user = userStr ? JSON.parse(userStr) : null;
  const userName = user ? user.nombre : "Usuario Remate";
  const userRole = user && user.rol_nombre ? user.rol_nombre : "Administrador";

  const menuItems = [
    { id: 'roles', path: '/roles', label: 'Roles', icon: Shield },
    { id: 'usuarios', path: '/usuarios', label: 'Usuarios', icon: Users },
    { id: 'categorias', path: '/categorias', label: 'Categorías', icon: Tags },
    { id: 'productos', path: '/productos', label: 'Productos', icon: Package },
    { id: 'pedidos', path: '/pedidos', label: 'Pedidos', icon: ShoppingCart },
    { id: 'facturas', path: '/facturas', label: 'Facturas', icon: Receipt },
    { id: 'proveedores', path: '/proveedores', label: 'Proveedores', icon: Truck }
  ];

  return (
    <>
      <div className={`sidebar-overlay ${isOpen ? 'open' : ''}`} onClick={() => setIsOpen(false)}></div>
      <aside className={`sidebar ${isOpen ? 'open' : ''}`}>
        <div className="sidebar-header">
          <NavLink to="/inicio" className="sidebar-logo" style={{ textDecoration: 'none', color: 'inherit' }} onClickCapture={() => setIsOpen(false)}>
            <Layers size={28} className="logo-icon" />
            <span>AdminPanel</span>
          </NavLink>
          <button className="sidebar-close" onClick={() => setIsOpen(false)}>
            <X size={24} />
          </button>
        </div>
        
        <nav className="sidebar-nav">
          {menuItems.map(item => {
            const Icon = item.icon;
            return (
              <NavLink 
                key={item.id} 
                to={item.path}
                className={({ isActive }) => `nav-item ${isActive ? 'active' : ''}`}
                onClick={() => {
                  if (window.innerWidth <= 768) setIsOpen(false);
                }}
              >
                <Icon size={20} strokeWidth={2.5} />
                <span>{item.label}</span>
              </NavLink>
            );
          })}
        </nav>

        {/* User Profile and Logout section at the bottom */}
        <div className="sidebar-footer">
          <NavLink 
            to="/perfil" 
            className="sidebar-profile" 
            style={{ textDecoration: 'none', color: 'inherit', display: 'flex', alignItems: 'center' }}
            onClick={() => {
              if (window.innerWidth <= 768) setIsOpen(false);
            }}
          >
            <UserCircle size={32} className="profile-avatar" />
            <div className="profile-info" style={{ display: 'flex', flexDirection: 'column' }}>
              <span className="profile-role" style={{fontWeight: 'bold', fontSize: '0.9rem'}}>{userName}</span>
              <span className="profile-role" style={{fontSize: '0.75rem', opacity: 0.8}}>{userRole}</span>
            </div>
          </NavLink>
          <button className="btn-logout" onClick={() => {
            if (onLogout) onLogout();
            logout();
          }}>
            <LogOut size={20} strokeWidth={2.5} />
          </button>
        </div>
      </aside>
    </>
  );
};

export default Sidebar;
