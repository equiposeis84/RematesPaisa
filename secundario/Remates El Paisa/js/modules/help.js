/*
Archivo: js/modules/help.js
Descripción: Contenido de ayuda y FAQs para usuarios.
Explicación: Renderiza guías, preguntas frecuentes y enlaces de soporte.
Importante: Mantener el contenido accesible y actualizado.
*/

// --- FUNCIONES DE AYUDA ---
export function renderHelpPage() {
    const container = document.getElementById('page-help');
    if (!container) return;
    container.innerHTML = `
        <div class="page active animate-fade-in">
            <h2 class="text-2xl font-bold mb-4">Ayuda y Contacto</h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg p-6 shadow">
                    <h3 class="font-semibold mb-3">Preguntas Frecuentes</h3>
                    <div class="space-y-4 text-slate-700">
                        <div>
                            <p class="font-semibold">¿Puedo cancelar o modificar mi pedido?</p>
                            <p class="text-sm">Puedes cancelar tu pedido siempre que no haya sido enviado. Para modificaciones, contáctanos lo antes posible.</p>
                        </div>
                        <div>
                            <p class="font-semibold">¿Qué hago si tengo un problema con mi pedido?</p>
                            <p class="text-sm">Contáctanos inmediatamente a través de los canales de soporte y con gusto resolveremos tu inconveniente.</p>
                        </div>
                        <div>
                            <p class="font-semibold">¿Cuánto tarda la entrega?</p>
                            <p class="text-sm">Las entregas en la ciudad suelen tardar entre 24 y 48 horas. En zonas fuera de la ciudad puede demorar más.</p>
                        </div>
                        <div>
                            <p class="font-semibold">¿Puedo devolver un producto?</p>
                            <p class="text-sm">Aceptamos devoluciones en casos de producto dañado o error en el envío. Revisa las políticas y contacta soporte.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow">
                    <h3 class="font-semibold mb-3">Contáctanos</h3>
                    <div class="grid grid-cols-1 gap-3 text-slate-700">
                        <div>
                            <p class="text-sm text-slate-500">Información de contacto</p>
                            <p class="mt-2"><strong>Teléfono:</strong> +57 1 123 4567</p>
                            <p><strong>Email:</strong> <a href="mailto:contacto@remateselpaisa.com" class="text-blue-500">contacto@remateselpaisa.com</a></p>
                            <p class="truncate"><strong>Dirección:</strong> Calle 123 #45-67, Bogotá</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Horario de Atención</p>
                            <p class="mt-2">Lunes a Sábado, 8:00 AM - 6:00 PM</p>
                            <p>Domingos y festivos: Cerrado</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Soporte en línea</p>
                            <p class="mt-2">Chat en vivo: Disponible en horario de atención. También puedes abrir un ticket desde tu perfil.</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Redes Sociales</p>
                            <p class="mt-2">Síguenos en <strong>Facebook</strong> y <strong>Instagram</strong> para novedades y promociones.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}
