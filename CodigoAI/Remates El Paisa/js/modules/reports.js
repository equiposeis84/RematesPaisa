/*
Archivo: js/modules/reports.js
Descripción: Generación de reportes y estadísticas (ventas, entregas, inventario).
Explicación: Contiene funciones para calcular métricas y preparar datos para gráficos o exportación.
Importante: Evitar cálculos costosos en el hilo principal; paginar o resumir datos grandes.
*/

// --- FUNCIONES DE REPORTES ---
import { getCurrentUserRole, mockOrders, mockCustomers, mockUsers } from './constants.js';
import { formatCurrency } from './utils.js';

export function renderReportsPage() {
    const container = document.getElementById('page-reports');
    if (!container) return;
    const role = getCurrentUserRole();
    if (role !== 'admin') {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full">
                <h2 class="text-3xl font-bold mb-4">Acceso Restringido</h2>
                <p class="mb-4">Solo los administradores pueden ver los reportes.</p>
                <button onclick="event.preventDefault(); window.navigateTo && window.navigateTo('login')" class="bg-blue-500 text-white px-6 py-2 rounded">Ir a Iniciar Sesión</button>
            </div>
        `;
        return;
    }
    // compute KPIs
    const totalSales = mockOrders.reduce((s, o) => s + (Number(o.total) || 0), 0);
    const totalOrders = mockOrders.length;
    const avgOrder = totalOrders ? Math.round(totalSales / totalOrders) : 0;
    // simple profit approximation: assume 30% margin
    const estimatedProfit = Math.round(totalSales * 0.3);

    const uniqueCustomers = new Set(mockOrders.map(o => o.customerEmail)).size || mockCustomers.length;
    const totalUsers = Object.keys(mockUsers).length + mockCustomers.length;

    container.innerHTML = `
        <div class="page active animate-fade-in">
            <h2 class="text-2xl font-bold mb-4">Reportes</h2>
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="bg-white p-4 rounded-xl shadow flex-1">
                    <div class="text-sm text-slate-500">Ventas Totales</div>
                    <div class="text-2xl font-bold mt-2 text-custom-blue">${formatCurrency(totalSales)}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow w-40">
                    <div class="text-sm text-slate-500">Pedidos</div>
                    <div class="text-2xl font-bold mt-2 text-indigo-600">${totalOrders}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow w-48">
                    <div class="text-sm text-slate-500">Promedio</div>
                    <div class="text-2xl font-bold mt-2 text-emerald-600">${formatCurrency(avgOrder)}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow w-48">
                    <div class="text-sm text-slate-500">Ganancia Estimada</div>
                    <div class="text-2xl font-bold mt-2 text-purple-600">${formatCurrency(estimatedProfit)}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow p-4 min-h-[260px]">
                    <div class="font-semibold mb-2">Ventas por Fecha</div>
                    <div class="h-52"><canvas id="sales-chart"></canvas></div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 min-h-[260px]">
                    <div class="font-semibold mb-2">Estado de Pedidos</div>
                    <div class="h-52"><canvas id="orders-status-chart"></canvas></div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 min-h-[260px]">
                    <div class="font-semibold mb-2">Crecimiento de Usuarios</div>
                    <div class="h-52"><canvas id="users-growth-chart"></canvas></div>
                </div>
                <div class="bg-white rounded-xl shadow p-4 min-h-[260px]">
                    <div class="font-semibold mb-2">Productos Top</div>
                    <div class="h-52"><canvas id="top-products-chart"></canvas></div>
                </div>
            </div>
        </div>
    `;
    renderSalesChart();
    renderOrdersStatusChart();
    renderUsersGrowthChart();
    renderTopProductsChart();
}

export function renderCharts() {
    renderSalesChart();
}

function renderSalesChart() {
    const ctx = document.getElementById('sales-chart');
    if (!ctx) return;
    if (window.salesChart) {
        try { window.salesChart.destroy(); } catch (e) { /* ignore */ }
    }
    // prepare timeseries from mockOrders grouped by date
    const byDate = {};
    mockOrders.forEach(o => {
        const d = o.date || (new Date()).toISOString().split('T')[0];
        byDate[d] = (byDate[d] || 0) + (Number(o.total) || 0);
    });
    const labels = Object.keys(byDate).sort();
    const data = labels.map(l => byDate[l]);
    window.salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Ventas (por fecha)',
                data,
                backgroundColor: 'rgba(59, 130, 246, 0.12)',
                borderColor: 'rgba(59, 130, 246, 1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 8, right: 8, left: 8, bottom: 8 } },
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
}

function renderOrdersStatusChart() {
    const ctx = document.getElementById('orders-status-chart');
    if (!ctx) return;
    if (window.statusChart) { try { window.statusChart.destroy(); } catch (e) {} }
    const statusCounts = {};
    mockOrders.forEach(o => { statusCounts[o.status] = (statusCounts[o.status] || 0) + 1; });
    const labels = Object.keys(statusCounts);
    const data = labels.map(l => statusCounts[l]);
    window.statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: ['#34D399', '#FBBF24', '#60A5FA', '#F87171', '#A78BFA'] }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8 } } } }
    });
}

function renderUsersGrowthChart() {
    const ctx = document.getElementById('users-growth-chart');
    if (!ctx) return;
    if (window.usersChart) { try { window.usersChart.destroy(); } catch (e) {} }
    const dateSet = new Set();
    mockOrders.forEach(o => dateSet.add(o.date));
    const labels = Array.from(dateSet).sort();
    const cumulative = [];
    labels.forEach((d) => {
        const seen = new Set(mockOrders.filter(o => o.date <= d).map(o => o.customerEmail));
        cumulative.push(seen.size + Object.keys(mockUsers).length);
    });
    window.usersChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Usuarios (cumulativo)', data: cumulative, backgroundColor: 'rgba(167,139,250,0.9)' }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { maxRotation: 45, minRotation: 0 } }, y: { beginAtZero: true } } }
    });
}

function renderTopProductsChart() {
    const ctx = document.getElementById('top-products-chart');
    if (!ctx) return;
    if (window.topProductsChart) { try { window.topProductsChart.destroy(); } catch (e) {} }
    const counts = {};
    mockOrders.forEach(o => {
        (o.items || []).forEach(it => { counts[it.name] = (counts[it.name] || 0) + (it.qty || 1); });
    });
    const labels = Object.keys(counts).sort((a,b) => counts[b]-counts[a]).slice(0,6);
    const data = labels.map(l => counts[l]);
    window.topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Unidades vendidas', data, backgroundColor: ['#60A5FA','#34D399','#FBBF24','#F87171','#A78BFA','#FB7185'] }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
}