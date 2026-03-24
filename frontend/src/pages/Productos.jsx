import { useState, useEffect } from 'react';
import axios from 'axios';
import { Pencil, Trash2, Package } from 'lucide-react';

const URL_API = "http://localhost:3000/api/productos";
const URL_CATEGORIAS = "http://localhost:3000/api/categorias";
const URL_PROVEEDORES = "http://localhost:3000/api/proveedores";

const Productos = () => {
  const [productos, setProductos] = useState([]);
  const [categoriasList, setCategoriasList] = useState([]);
  const [proveedoresList, setProveedoresList] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [enEdicion, setEnEdicion] = useState(false);
  const [loading, setLoading] = useState(true);

  // Paginación y búsqueda
  const [searchTerm, setSearchTerm] = useState("");
  const [searchField, setSearchField] = useState("nombre");
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 5;
  
  // Campos de la BD
  const [idProducto, setIdProducto] = useState(null);
  const [categoriaId, setCategoriaId] = useState("");
  const [proveedorId, setProveedorId] = useState("");
  const [nombre, setNombre] = useState("");
  const [descripcion, setDescripcion] = useState("");
  const [precioCompra, setPrecioCompra] = useState(0);
  const [precioVenta, setPrecioVenta] = useState(0);
  const [stockActual, setStockActual] = useState(0);
  const [stockMinimo, setStockMinimo] = useState(0);
  const [activo, setActivo] = useState(1);

  const listar = () => {
    setLoading(true);
    axios.get(URL_API)
      .then(res => setProductos(res.data))
      .catch(err => console.error("Error al listar productos:", err))
      .finally(() => setLoading(false));
  };

  const listarDependencias = () => {
    axios.get(URL_CATEGORIAS)
      .then(res => setCategoriasList(res.data))
      .catch(err => console.error("Error al listar categorias:", err));
      
    axios.get(URL_PROVEEDORES)
      .then(res => setProveedoresList(res.data))
      .catch(err => console.error("Error al listar proveedores:", err));
  };

  useEffect(() => {
    listar();
    listarDependencias();
  }, []);

  const limpiarFormulario = () => {
    setCategoriaId(""); setProveedorId(""); setNombre(""); setDescripcion("");
    setPrecioCompra(0); setPrecioVenta(0); setStockActual(0); setStockMinimo(0);
    setActivo(1); setEnEdicion(false); setIdProducto(null);
    setShowModal(false);
  };

  const abrirRegistro = () => {
    limpiarFormulario();
    const nextId = productos.length > 0 ? Math.max(...productos.map(p => p.id_producto)) + 1 : 1;
    setIdProducto(nextId);
    setShowModal(true);
  };

  const seleccionarProducto = (p) => {
    setIdProducto(p.id_producto);
    setCategoriaId(p.categoria_id || "");
    setProveedorId(p.proveedor_id || "");
    setNombre(p.nombre);
    setDescripcion(p.descripcion || "");
    setPrecioCompra(p.precio_compra);
    setPrecioVenta(p.precio_venta);
    setStockActual(p.stock_actual);
    setStockMinimo(p.stock_minimo);
    setActivo(p.activo);
    setEnEdicion(true);
    setShowModal(true);
  };

  const guardar = () => {
    const datos = {
      categoria_id: categoriaId || null,
      proveedor_id: proveedorId || null,
      nombre,
      descripcion,
      precio_compra: parseFloat(precioCompra),
      precio_venta: parseFloat(precioVenta),
      stock_actual: parseInt(stockActual, 10),
      stock_minimo: parseInt(stockMinimo, 10),
      activo
    };

    if (!nombre || !categoriaId) {
      alert("El nombre y la categoría son obligatorios");
      return;
    }

    if (enEdicion) {
      axios.put(`${URL_API}/${idProducto}`, datos)
        .then(() => {
          limpiarFormulario();
          listar();
          alert("Producto actualizado correctamente.");
        })
        .catch(err => {
          console.error("Error interno:", err);
          alert("Error al actualizar: " + (err.response?.data?.message || err.message));
        });
    } else {
      axios.post(URL_API, datos)
        .then(() => {
          limpiarFormulario();
          listar();
          alert("Producto creado con éxito.");
        })
        .catch(err => {
          console.error("Error interno:", err);
          alert("Error al crear: " + (err.response?.data?.message || err.message));
        });
    }
  };

  const eliminar = (id) => {
    if (window.confirm("¿Confirmar eliminación de este registro?")) {
      axios.delete(`${URL_API}/${id}`)
        .then(() => listar())
        .catch(err => {
          console.error("Error al eliminar:", err);
          alert("No se puede eliminar el producto. Es posible que tenga existencias en inventario o ventas relacionadas.\nDetalle: " + (err.response?.data?.error || err.message));
        });
    }
  };

  const filteredProductos = productos.filter(p => {
    if (!searchTerm) return true;
    const value = p[searchField];
    if (value === null || value === undefined) return false;
    return String(value).toLowerCase().includes(searchTerm.toLowerCase());
  });

  const totalPages = Math.ceil(filteredProductos.length / itemsPerPage);
  const currentItems = filteredProductos.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  return (
    <>
      <div className="top-action-bar">
        <button className="btn-add-record" onClick={abrirRegistro}>Añadir Producto</button>
        <div className="search-container">
          <input 
            type="text" 
            className="search-input" 
            placeholder="Search..." 
            value={searchTerm}
            onChange={(e) => { setSearchTerm(e.target.value); setCurrentPage(1); }}
          />
          <select 
            className="search-select" 
            value={searchField}
            onChange={(e) => { setSearchField(e.target.value); setCurrentPage(1); }}
          >
            <option value="id_producto">ID Producto</option>
            <option value="nombre">Nombre</option>
            <option value="categoria_nombre">Categoría</option>
            <option value="proveedor_nombre">Proveedor</option>
          </select>
          <button className="btn-search-ok" onClick={() => setCurrentPage(1)}>OK</button>
        </div>
      </div>

      <div className="table-wrapper">
        {loading ? (
          <div style={{ padding: '4rem', textAlign: 'center', color: '#94a3b8' }}>
            <div className="spinner"></div>
            <p style={{ marginTop: '1rem' }}>Cargando datos...</p>
          </div>
        ) : productos.length === 0 ? (
          <div style={{ padding: '5rem 2rem', textAlign: 'center', color: '#94a3b8' }}>
            <Package size={64} style={{ color: 'var(--primary)', opacity: 0.5, marginBottom: '1.5rem' }} />
            <h2>No hay productos registrados</h2>
            <p>Haz clic en "Registrar Producto" para comenzar.</p>
          </div>
        ) : (
          <table className="styled-table" style={{ fontSize: '0.9rem' }}>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Stock</th>
                <th>Precio Venta</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              {currentItems.map((p) => (
                <tr key={p.id_producto}>
                  <td>{p.id_producto}</td>
                  <td>{p.nombre}</td>
                  <td>{p.categoria_nombre}</td>
                  <td>{p.proveedor_nombre || <span style={{color: '#aaa', fontStyle: 'italic'}}>Ninguno</span>}</td>
                  <td>
                    <span style={{ 
                      color: p.stock_actual <= p.stock_minimo ? 'var(--danger)' : 'inherit',
                      fontWeight: p.stock_actual <= p.stock_minimo ? 'bold' : 'normal'
                    }}>
                      {p.stock_actual}
                    </span>
                  </td>
                  <td>${Number(p.precio_venta).toLocaleString()}</td>
                  <td>
                    <button className={`status-toggle ${p.activo ? 'is-active' : 'is-inactive'}`}>
                      {p.activo ? 'Activo' : 'Inactivo'}
                    </button>
                  </td>
                  <td className="actions-cell">
                    <button className="btn-icon" onClick={() => seleccionarProducto(p)}>
                      <Pencil size={18} color="var(--primary)" />
                    </button>
                    <button className="btn-icon" onClick={() => eliminar(p.id_producto)}>
                      <Trash2 size={18} color="var(--danger)" />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>

      {!loading && totalPages > 1 && (
        <div className="pagination-bar">
          <button 
            className="page-btn" 
            disabled={currentPage === 1} 
            onClick={() => setCurrentPage(prev => prev - 1)}
          >
            Previous
          </button>
          {Array.from({ length: totalPages }, (_, i) => i + 1).map(num => (
            <button 
              key={num}
              className={`page-btn ${currentPage === num ? 'active' : ''}`}
              onClick={() => setCurrentPage(num)}
            >
              {num}
            </button>
          ))}
          <button 
            className="page-btn" 
            disabled={currentPage === totalPages} 
            onClick={() => setCurrentPage(prev => prev + 1)}
          >
            Next
          </button>
        </div>
      )}

      {showModal && (
        <div className="modal-backdrop">
          <div className="modal-box" style={{ maxWidth: '600px' }}>
            <h2>{enEdicion ? "Actualizar Producto" : "Nuevo Producto"}</h2>
            <div className="form-grid">
              <div className="input-field" style={{ gridColumn: 'span 2' }}>
                <label>ID</label>
                <input type="text" value={idProducto || ''} disabled style={{ background: 'var(--border)', cursor: 'not-allowed' }}/>
              </div>
              <div className="input-field" style={{ gridColumn: 'span 2' }}>
                <label>Nombre del Producto *</label>
                <input value={nombre} onChange={(e) => setNombre(e.target.value)} />
              </div>
              
              <div className="input-field">
                <label>Categoría *</label>
                <select value={categoriaId} onChange={(e) => setCategoriaId(Number(e.target.value))}>
                  <option value="" disabled>Seleccione categoría...</option>
                  {categoriasList.map(c => (
                    <option key={c.id_categoria} value={c.id_categoria}>{c.nombre}</option>
                  ))}
                </select>
              </div>
              
              <div className="input-field">
                <label>Proveedor</label>
                <select value={proveedorId} onChange={(e) => setProveedorId(Number(e.target.value))}>
                  <option value="">Ninguno / Sin definir</option>
                  {proveedoresList.map(pr => (
                    <option key={pr.id_proveedor} value={pr.id_proveedor}>{pr.nombre}</option>
                  ))}
                </select>
              </div>

              <div className="input-field">
                <label>Precio Compra</label>
                <input type="number" step="0.01" value={precioCompra} onChange={(e) => setPrecioCompra(e.target.value)} />
              </div>
              
              <div className="input-field">
                <label>Precio Venta</label>
                <input type="number" step="0.01" value={precioVenta} onChange={(e) => setPrecioVenta(e.target.value)} />
              </div>
              
              <div className="input-field">
                <label>Stock Actual</label>
                <input type="number" value={stockActual} onChange={(e) => setStockActual(e.target.value)} />
              </div>
              
              <div className="input-field">
                <label>Stock Mínimo</label>
                <input type="number" value={stockMinimo} onChange={(e) => setStockMinimo(e.target.value)} />
              </div>

              <div className="input-field" style={{ gridColumn: 'span 2' }}>
                <label>Descripción</label>
                <textarea 
                  value={descripcion} 
                  onChange={(e) => setDescripcion(e.target.value)} 
                  rows={2}
                  style={{ width: '100%', padding: '0.5rem', borderRadius: '4px', border: '1px solid #ccc' }}
                />
              </div>

              <div className="input-field" style={{ gridColumn: 'span 2' }}>
                <label>Estado</label>
                <select value={activo} onChange={(e) => setActivo(Number(e.target.value))}>
                  <option value={1}>Activo</option>
                  <option value={0}>Inactivo</option>
                </select>
              </div>
            </div>
            <div className="modal-btns">
              <button className="btn-cancel" onClick={limpiarFormulario}>Cancelar</button>
              <button className="btn-save" onClick={guardar}>Guardar Cambios</button>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default Productos;
