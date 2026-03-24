import axios from 'axios';

const TOKEN_KEY = 'token';
const USER_KEY = 'user';

// ─── Helpers de sesión ────────────────────────────────────────
export const getToken = () => localStorage.getItem(TOKEN_KEY);
export const getUser  = () => JSON.parse(localStorage.getItem(USER_KEY) || 'null');

export const saveSession = (token, user) => {
    localStorage.setItem(TOKEN_KEY, token);
    localStorage.setItem(USER_KEY, JSON.stringify(user));
};

export const logout = () => {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    window.location.href = '/login';
};

// ─── Interceptor de Axios ─────────────────────────────────────
// Añade automáticamente el header Authorization en cada petición
axios.interceptors.request.use((config) => {
    const token = getToken();
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

// Si el backend responde 401 (token expirado o inválido), cierra la sesión
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401 && window.location.pathname !== '/register') {
            console.warn('ADVERTENCIA: Token expirado o invalido. Cerrando sesion...');
            logout();
        }
        return Promise.reject(error);
    }
);
