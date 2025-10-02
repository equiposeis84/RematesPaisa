// ==============================
//   LÓGICA DEL LOGIN
// ==============================

// 1. Capturamos el formulario por su id
let form = document.getElementById("loginForm");

// 2. Escuchamos cuando el formulario se envía
form.addEventListener("submit", function(e) {
  e.preventDefault(); // evita que la página se recargue al enviar el form

  // 3. Obtenemos los valores de los campos
  let email = document.getElementById("email").value;
  let password = document.getElementById("password").value;

  // 4. Validamos credenciales (SIMULADO)
  if (email === "admin@a.com" && password === "123") {
    // Si es administrador
    alert("✅ Bienvenido Administrador");

    // Guardamos al usuario en LocalStorage
    localStorage.setItem("usuario", JSON.stringify({
      rol: "admin",
      email: email
    }));

    // Redirigimos
    window.location.href = "/RematesElPaisa Views/Administrador/html/Catalogo.html";
  } 
  else if (email === "cliente@correo.com" && password === "cliente123") {
    // Si es cliente
    alert("✅ Bienvenido Cliente");

    // Guardamos en LocalStorage
    localStorage.setItem("usuario", JSON.stringify({
      rol: "cliente",
      email: email
    }));

    // Redirigimos
    window.location.href = "/RematesElPaisa Views/Cliente/html/Catalogo.html";
  } 
  else if (email === "repartidor@correo.com" && password === "123") {
    // Si es repartidor
    alert("✅ Bienvenido Repartidor");

    // Guardamos en LocalStorage
    localStorage.setItem("usuario", JSON.stringify({
      rol: "repartidor",
      email: email
    }));

    // Redirigimos
    window.location.href = "/RematesElPaisa Views/Repartidor/html/Catalogo.html";
  }
  else {
    // Credenciales incorrectas
    alert("❌ Correo o contraseña incorrectos");
  }
});