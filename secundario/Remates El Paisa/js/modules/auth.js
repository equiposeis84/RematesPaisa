/*
Archivo: js/modules/auth.js
Descripción: Maneja autenticación y sesión de usuario en cliente.
Explicación: Contiene funciones para login/logout y gestión de token/estado en localStorage.
Importante: Esta autenticación es sólo a nivel cliente; no confíes en ella para seguridad en producción.
*/

import { mockUsers, mockCustomers, getCurrentUserRole, setCurrentUserRole, getNextPage, setNextPage, setCurrentUserEmail, saveCustomersToStorage } from './constants.js';

// --- FUNCIONES DE AUTENTICACIÓN ---
export function renderLoginPage() {
    const container = document.getElementById('page-login');
    if (!container) return;
    container.innerHTML = `
        <div class="flex justify-center items-start mt-12">
            <div class="w-full max-w-3xl grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-center text-slate-800 mb-1">Iniciar Sesión</h2>
                    <p class="text-center text-slate-500 mb-6">Accede a tu cuenta para continuar</p>
                    <form id="login-form" class="space-y-4">
                        <div>
                            <label class="block text-sm text-slate-600 mb-1">Correo Electrónico</label>
                            <input id="login-email" type="email" placeholder="tu@correo.com" class="w-full px-3 py-2 border border-slate-200 rounded-lg" required />
                        </div>
                        <div>
                            <label class="block text-sm text-slate-600 mb-1">Contraseña</label>
                            <input id="login-password" type="password" placeholder="********" class="w-full px-3 py-2 border border-slate-200 rounded-lg" required />
                        </div>
                        <button type="submit" class="mt-2 w-full py-2.5 rounded-lg text-white" style="background: linear-gradient(90deg,#3b82f6,#2563eb); font-weight:700;">Ingresar</button>
                        <div class="flex justify-between text-sm mt-2">
                            <a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('password-recovery')" class="text-custom-blue">¿Olvidaste tu contraseña?</a>
                            <a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('register')" class="text-custom-blue">Regístrate aquí</a>
                        </div>
                    </form>
                    <div class="mt-4 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <button id="btn-google-signin" class="google-btn" aria-label="Iniciar sesión con Google"> <img src="img/google-logo.png" alt="Google" style="width:20px;height:20px"> <span>Iniciar con Google</span></button>
                        </div>
                    </div>
                </div>
                <div class="login-credentials">
                    <div class="cred-box">
                        <h3 class="cred-title">Correos de prueba</h3>
                        <p class="login-small mb-2">Usa cualquiera de estas cuentas temporales para probar:</p>
                        <div class="text-sm text-slate-700 space-y-1">
                            <div><strong>admin@remates.com</strong> — Contraseña: <strong>123</strong> <span class="text-slate-500">(Administrador)</span></div>
                            <div><strong>repartidor@remates.com</strong> — Contraseña: <strong>123</strong> <span class="text-slate-500">(Repartidor)</span></div>
                            <div><strong>cliente@remates.com</strong> — Contraseña: <strong>123</strong> <span class="text-slate-500">(Cliente)</span></div>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <button id="btn-google-signin" class="google-btn"> <img src="img/google-logo.png" alt="G" style="width:20px;height:20px"> Iniciar con Google</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    const form = document.getElementById('login-form');
    if (form) form.onsubmit = handleLogin;

    const gbtn = document.getElementById('btn-google-signin');
    if (gbtn) gbtn.addEventListener('click', async (e) => {
        e.preventDefault();
        if (!window.googleSignIn) {
            window.showAlert && window.showAlert('Google Sign-In no está configurado en este entorno.', 'info');
            return;
        }
        const profile = await window.googleSignIn();
        if (!profile || !profile.email) return;
        const email = profile.email;
        // create user if not exist
        if (!mockUsers[email]) {
            mockUsers[email] = { role: 'customer', name: profile.name || email.split('@')[0], password: null };
        }
        // create customer entry if missing
        if (!mockCustomers.find(c => c.email === email)) {
            const newCustomerId = 'C' + (mockCustomers.length + 1).toString().padStart(3, '0');
            mockCustomers.push({ id: newCustomerId, name: mockUsers[email].name, email, phone: '', address: '', registered: new Date().toISOString().split('T')[0], orders: 0 });
            try { saveCustomersToStorage(); } catch (err) { /* noop */ }
        }
        // set session
    setCurrentUserRole(mockUsers[email].role || 'customer');
    setCurrentUserEmail(email);
        window.showAlert && window.showAlert('Inicio de sesión con Google correcto', 'success');
        if (window.renderNav) window.renderNav();
        window.navigateTo && window.navigateTo('home');
    });
}

export function handleLogin(event) {
    event.preventDefault();
    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value.trim();
    const user = mockUsers[email];
    if (user && user.password === password) {
        setCurrentUserRole(user.role);
        // almacenar email del usuario conectado (persistente)
        setCurrentUserEmail(email);
        window.showAlert && window.showAlert('¡Bienvenido, ' + user.name + '!', 'success');
            // Si hay una página objetivo, navegar a ella tras el login
            const next = getNextPage && getNextPage();
            if (next) {
                setNextPage && setNextPage(null);
                window.navigateTo && window.navigateTo(next);
            } else {
                window.navigateTo && window.navigateTo('home');
            }
            // refresh UI sections that depend on role
            try {
                if (window.renderNav) window.renderNav();
                if (window.renderAdminOrdersPage) window.renderAdminOrdersPage();
                if (window.renderUsersPage) window.renderUsersPage();
                if (window.renderReportsPage) window.renderReportsPage();
            } catch (e) {
                // noop
            }
    } else {
        window.showAlert && window.showAlert('Correo o contraseña incorrectos', 'error');
    }
}

export function logout() {
    setCurrentUserRole('guest');
    setCurrentUserEmail(null);
    window.showAlert && window.showAlert('Sesión cerrada', 'info');
    window.navigateTo && window.navigateTo('login');
}

export function renderRegisterPage() {
    const container = document.getElementById('page-register');
    if (!container) return;
    container.innerHTML = `
        <div class="flex justify-center items-center min-h-[60vh]">
            <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
                <h2 class="text-2xl font-bold text-center text-slate-800 mb-1">Crear Cuenta</h2>
                <p class="text-center text-slate-500 mb-6">Regístrate para empezar a comprar</p>
                <form id="register-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="reg-first-name" class="block text-sm font-medium text-slate-600 mb-1">Nombres</label>
                            <input type="text" id="reg-first-name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label for="reg-last-name" class="block text-sm font-medium text-slate-600 mb-1">Apellidos</label>
                            <input type="text" id="reg-last-name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="reg-email" class="block text-sm font-medium text-slate-600 mb-1">Correo Electrónico</label>
                        <input type="email" id="reg-email" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="reg-password" class="block text-sm font-medium text-slate-600 mb-1">Contraseña</label>
                            <input type="password" id="reg-password" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label for="reg-confirm-password" class="block text-sm font-medium text-slate-600 mb-1">Confirmar Contraseña</label>
                            <input type="password" id="reg-confirm-password" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="reg-document" class="block text-sm font-medium text-slate-600 mb-1">Documento de Identidad</label>
                        <input type="text" id="reg-document" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    </div>
                    
                    <div class="mb-4">
                        <label for="reg-phone" class="block text-sm font-medium text-slate-600 mb-1">Teléfono</label>
                        <input type="tel" id="reg-phone" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    </div>
                    
                    <div class="mb-6">
                        <label for="reg-address" class="block text-sm font-medium text-slate-600 mb-1">Dirección</label>
                        <textarea id="reg-address" rows="2" required class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
                    </div>
                    
                    <p id="register-error" class="text-red-500 text-sm text-center mb-4 hidden"></p>
                    <button type="submit" class="w-full bg-custom-blue text-white font-semibold py-2.5 px-4 rounded-lg hover:bg-custom-blue-dark transition-colors">Registrarse</button>
                </form>
                <div class="text-center mt-4">
                    <p class="text-sm text-slate-600">¿Ya tienes una cuenta? <a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('login');" class="text-custom-blue hover:underline">Inicia sesión aquí</a></p>
                </div>
            </div>
        </div>
    `;

    const form = document.getElementById('register-form');
    if (form) form.onsubmit = handleRegister;
}

export function handleRegister(event) {
    event.preventDefault();
    const email = document.getElementById('reg-email').value;
    const password = document.getElementById('reg-password').value;
    const confirmPassword = document.getElementById('reg-confirm-password').value;
    const errorElement = document.getElementById('register-error');
    
    if (password !== confirmPassword) {
        errorElement.textContent = 'Las contraseñas no coinciden';
        errorElement.classList.remove('hidden');
        return;
    }
    
    if (mockUsers[email]) {
        errorElement.textContent = 'Este correo ya está registrado';
        errorElement.classList.remove('hidden');
        return;
    }
    
    // Crear nuevo usuario
    mockUsers[email] = {
        role: 'customer',
        name: `${document.getElementById('reg-first-name').value} ${document.getElementById('reg-last-name').value}`,
        password: password
    };
    
    // Crear nuevo cliente
    const newCustomerId = 'C' + (mockCustomers.length + 1).toString().padStart(3, '0');
    mockCustomers.push({
        id: newCustomerId,
        name: `${document.getElementById('reg-first-name').value} ${document.getElementById('reg-last-name').value}`,
        email: email,
        phone: document.getElementById('reg-phone').value,
        address: document.getElementById('reg-address').value,
        document: document.getElementById('reg-document').value,
        registered: new Date().toISOString().split('T')[0],
        orders: 0,
        status: 'Activo'
    });
    try { saveCustomersToStorage(); } catch (err) { /* noop */ }
    
    window.showAlert && window.showAlert('¡Registro exitoso! Por favor inicia sesión', 'success');
    window.navigateTo && window.navigateTo('login');
}

export function renderPasswordRecovery() {
    const container = document.getElementById('page-login');
    if (!container) return;
    container.innerHTML = `
        <div class="flex justify-center items-center min-h-[60vh]">
            <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
                <h2 class="text-2xl font-bold text-center text-slate-800 mb-1">Recuperar Contraseña</h2>
                <form id="recovery-form">
                    <div class="mb-4">
                        <label for="recovery-email" class="block text-sm font-medium text-slate-600 mb-1">Correo Electrónico</label>
                        <input type="email" id="recovery-email" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <button type="submit" class="w-full bg-custom-blue text-white font-semibold py-2.5 px-4 rounded-lg hover:bg-custom-blue-dark transition-colors">
                        Enviar Código
                    </button>
                </form>
                <div class="text-center mt-4">
                    <a href="#" onclick="event.preventDefault(); window.navigateTo && window.navigateTo('login')" class="text-custom-blue hover:underline">Volver a Iniciar Sesión</a>
                </div>
            </div>
        </div>
    `;

    const form = document.getElementById('recovery-form');
    if (form) form.onsubmit = handlePasswordRecovery;
}

export function handlePasswordRecovery(event) {
    event.preventDefault();
    const email = document.getElementById('recovery-email').value;
    window.showAlert && window.showAlert(`Se ha enviado un código de recuperación a ${email}`, 'success');
    setTimeout(() => window.navigateTo && window.navigateTo('login'), 3000);
}
