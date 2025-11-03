/*
Archivo: js/validation.js
Descripción: Utilidades de validación de formularios y campos.
Explicación: Contiene funciones para comprobar formatos (email, teléfono, campos requeridos) y retornar mensajes de error.
Importante: validar tanto en cliente como en servidor (si existe) por seguridad.
*/

// Ejemplo de función de validación de email
function validarEmail(email) {
    // Expresión regular para validar el formato del email
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

// Ejemplo de función de validación de teléfono
function validarTelefono(telefono) {
    // Expresión regular para validar el formato del teléfono (ejemplo: 123-456-7890)
    var re = /^\d{3}-\d{3}-\d{4}$/;
    return re.test(String(telefono));
}

// Ejemplo de función para comprobar campos requeridos
function campoRequerido(campo) {
    return campo !== null && campo !== undefined && campo.trim() !== '';
}

// Ejemplo de función para retornar mensajes de error
function obtenerMensajeError(campo, tipo) {
    var mensaje = '';
    if (tipo === 'requerido' && !campoRequerido(campo)) {
        mensaje = 'Este campo es requerido.';
    } else if (tipo === 'email' && !validarEmail(campo)) {
        mensaje = 'Ingrese un email válido.';
    } else if (tipo === 'telefono' && !validarTelefono(campo)) {
        mensaje = 'Ingrese un teléfono válido.';
    }
    return mensaje;
}

// ...resto del código existente...