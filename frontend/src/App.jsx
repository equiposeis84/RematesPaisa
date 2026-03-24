import { useState } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import Sidebar from './components/Sidebar';
import Usuarios from './pages/Usuarios';
import Roles from './pages/Roles';
import Categorias from './pages/Categorias';
import Productos from './pages/Productos';
import Pedidos from './pages/Pedidos';
import Facturas from './pages/Facturas';
import Proveedores from './pages/Proveedores';
import Perfil from './pages/Perfil';
import Inicio from './pages/Inicio';
import Login from './pages/Login';
import Register from './pages/Register';
import { Menu } from 'lucide-react';
import './App.css';
import './services/authService'; // Activa los interceptores de Axios globalmente

function App() {
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);
  const [isAuthenticated, setIsAuthenticated] = useState(!!localStorage.getItem('token'));

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={isAuthenticated ? <Navigate to="/inicio" replace /> : <Login onLogin={() => setIsAuthenticated(true)} />} />
        <Route path="/register" element={isAuthenticated ? <Navigate to="/inicio" replace /> : <Register />} />

        <Route path="/*" element={
          isAuthenticated ? (
            <div className="app-wrapper">
              <Sidebar
                isOpen={isSidebarOpen}
                setIsOpen={setIsSidebarOpen}
                onLogout={() => setIsAuthenticated(false)}
              />

              <div className={`main-content ${isSidebarOpen ? 'sidebar-open' : ''}`}>
                <div className="mobile-header">
                  <button className="menu-btn" onClick={() => setIsSidebarOpen(true)}>
                    <Menu size={28} />
                  </button>
                  <h2 className="mobile-title">AdminPanel</h2>
                </div>

                <div className="admin-container">
                  <Routes>
                    <Route path="/" element={<Navigate to="/inicio" replace />} />
                    <Route path="/inicio" element={<Inicio />} />
                    <Route path="/usuarios" element={<Usuarios />} />
                    <Route path="/roles" element={<Roles />} />
                    <Route path="/categorias" element={<Categorias />} />
                    <Route path="/productos" element={<Productos />} />
                    <Route path="/pedidos" element={<Pedidos />} />
                    <Route path="/facturas" element={<Facturas />} />
                    <Route path="/proveedores" element={<Proveedores />} />
                    <Route path="/perfil" element={<Perfil />} />
                  </Routes>
                </div>
              </div>
            </div>
          ) : (
            <Navigate to="/login" replace />
          )
        } />
      </Routes>
    </BrowserRouter>
  );
}

export default App;