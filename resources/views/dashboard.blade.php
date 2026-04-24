<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $ventasDelta = $metrics['ventas_delta'] ?? 0;
                $ordenesDelta = $metrics['ordenes_delta'] ?? 0;
                $clientesDelta = $metrics['clientes_delta'] ?? 0;
                $recentVentas = $recentVentas ?? collect();
            @endphp

            <!-- Tarjetas de Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">Ventas del Mes</p>
                        <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <p class="text-3xl font-semibold text-slate-900 tracking-tight">$ {{ number_format($metrics['ventas_mes'] ?? 0, 2) }}</p>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $ventasDelta >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $ventasDelta >= 0 ? '+' : '-' }}{{ number_format(abs($ventasDelta), 1) }}%
                        </span>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">Órdenes del Mes</p>
                        <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <p class="text-3xl font-semibold text-slate-900 tracking-tight">{{ number_format($metrics['ordenes_mes'] ?? 0) }}</p>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $ordenesDelta >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $ordenesDelta >= 0 ? '+' : '-' }}{{ number_format(abs($ordenesDelta), 1) }}%
                        </span>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">Clientes</p>
                        <div class="h-9 w-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M12 12a5 5 0 100-10 5 5 0 000 10z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <p class="text-3xl font-semibold text-slate-900 tracking-tight">{{ number_format($metrics['clientes_total'] ?? 0) }}</p>
                            <p class="text-xs text-slate-500 mt-1">Nuevos este mes: {{ number_format($metrics['clientes_mes'] ?? 0) }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $clientesDelta >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $clientesDelta >= 0 ? '+' : '-' }}{{ number_format(abs($clientesDelta), 1) }}%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Gráficas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Gráfica de Ventas Mensuales -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ventas Mensuales</h3>
                    <div class="relative h-64 sm:h-72 lg:h-80">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>

                <!-- Gráfica de Productos Top -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Productos Más Vendidos</h3>
                    <div class="relative h-64 sm:h-72 lg:h-80">
                        <canvas id="productosChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Tendencias y Tabla -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Gráfica de Tendencias -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-3">
                        <h3 class="text-lg font-semibold text-gray-800">Tendencia de Ventas</h3>
                        <div class="flex space-x-2">
                            <button data-trend-range="7" class="px-3 py-1 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">7D</button>
                            <button data-trend-range="30" class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">30D</button>
                            <button data-trend-range="90" class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">90D</button>
                        </div>
                    </div>
                    <div class="relative h-64 sm:h-72">
                        <canvas id="tendenciaChart"></canvas>
                    </div>
                </div>

                <!-- Últimas Transacciones -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Últimas Transacciones</h3>
                    <div class="space-y-4">
                        @forelse ($recentVentas as $venta)
                            <div class="flex items-center justify-between pb-3 border-b border-gray-200 last:border-b-0 last:pb-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Orden #{{ $venta->id }}</p>
                                    <p class="text-xs text-gray-500">{{ optional($venta->created_at)->diffForHumans() }}</p>
                                </div>
                                <span class="text-sm font-semibold text-green-600">+$ {{ number_format($venta->total, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No hay transacciones recientes.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para las gráficas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Configuración común
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        };

        // Gráfica de Ventas Mensuales
            const ventasLabels = @json($monthLabels ?? []);
            const ventasData = @json($monthlyTotals ?? []);
            const productosLabels = @json($topProductoLabels ?? []);
            const productosData = @json($topProductoData ?? []);
            const tendenciaSeries = @json($trendSeries ?? []);

        const ventasCtx = document.getElementById('ventasChart').getContext('2d');
        new Chart(ventasCtx, {
            type: 'bar',
            data: {
                labels: ventasLabels,
                datasets: [{
                    label: 'Ventas',
                    data: ventasData,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                }
            }
        });

        // Gráfica de Productos Top
        const productosCtx = document.getElementById('productosChart').getContext('2d');
        new Chart(productosCtx, {
            type: 'doughnut',
            data: {
                labels: productosLabels,
                datasets: [{
                    data: productosData,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(107, 114, 128, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // Gráfica de Tendencia
        const tendenciaCtx = document.getElementById('tendenciaChart').getContext('2d');
        const tendenciaChart = new Chart(tendenciaCtx, {
            type: 'line',
            data: {
                labels: (tendenciaSeries['7'] || {}).labels || [],
                datasets: [{
                    label: 'Ventas',
                    data: (tendenciaSeries['7'] || {}).data || [],
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                }
            }
        });

        const trendButtons = document.querySelectorAll('[data-trend-range]');
        const setActiveButton = (range) => {
            trendButtons.forEach((button) => {
                if (button.dataset.trendRange === range) {
                    button.classList.remove('bg-gray-200', 'text-gray-700');
                    button.classList.add('bg-blue-500', 'text-white');
                } else {
                    button.classList.remove('bg-blue-500', 'text-white');
                    button.classList.add('bg-gray-200', 'text-gray-700');
                }
            });
        };

        const updateTrend = (range) => {
            const series = tendenciaSeries[range] || tendenciaSeries['7'] || { labels: [], data: [] };
            tendenciaChart.data.labels = series.labels;
            tendenciaChart.data.datasets[0].data = series.data;
            tendenciaChart.update();
            setActiveButton(range);
        };

        trendButtons.forEach((button) => {
            button.addEventListener('click', () => updateTrend(button.dataset.trendRange));
        });

        updateTrend('7');
    </script>
</x-app-layout>