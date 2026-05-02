@extends('layouts.app')

@section('content')
<!-- Top Navbar -->
<div class="top-navbar">
    <h2 class="greeting-title">Welcome {{ Auth::user()->name }} !</h2>
    <div class="top-controls">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search">
        </div>
        <div class="top-icons">
            <div class="icon-btn" title="Theme Toggle"><i class="fas fa-moon"></i></div>
            <div class="icon-btn" title="Notifications"><i class="fas fa-bell"></i></div>
        </div>
    </div>
</div>

<div class="container-fluid p-0 mt-2">
    @if(auth()->user()->hasRole('admin'))
        <!-- Overview Section -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="font-weight-bold mb-0" style="color: var(--text-dark);">Over View</h6>
        </div>
        
        <div class="row mb-4">
            <!-- Total Products -->
            <div class="col-md-3">
                <div class="custom-card py-2" style="border: 1px solid #4CC8A3; background: #fff;">
                    <div class="card-body d-flex align-items-center py-2">
                        <div class="me-3 p-3 rounded" style="background-color: #e6f7f3;">
                            <i class="fas fa-box text-teal" style="color: #2C98A0; font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold" style="color: #2c3e50;">{{ $stats['total_items'] }}</h4>
                            <span class="text-muted" style="font-size: 0.8rem;">Total Products</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Orders/Loans -->
            <div class="col-md-3">
                <div class="custom-card py-2" style="border: 1px solid #67DBA5; background: #fff;">
                    <div class="card-body d-flex align-items-center py-2">
                        <div class="me-3 p-3 rounded" style="background-color: #eaf8f1;">
                            <i class="fas fa-layer-group text-success" style="color: #3BB2A3; font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold" style="color: #2c3e50;">{{ $stats['total_loans'] ?? $stats['active_loans'] }}</h4>
                            <span class="text-muted" style="font-size: 0.8rem;">Orders</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Stock / Available -->
            <div class="col-md-3">
                <div class="custom-card py-2" style="border: 1px solid #89E8AC; background: #fff;">
                    <div class="card-body d-flex align-items-center py-2">
                        <div class="me-3 p-3 rounded" style="background-color: #eefaf2;">
                            <i class="fas fa-chart-line text-info" style="color: #4CC8A3; font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold" style="color: #2c3e50;">{{ $stats['available_items'] }}</h4>
                            <span class="text-muted" style="font-size: 0.8rem;">Total Stock</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Out of Stock -->
            <div class="col-md-3">
                <div class="custom-card py-2" style="border: 1px solid #eab8b8; background: #fdf5f5; position: relative;">
                    <div class="card-body d-flex align-items-center py-2">
                        <div class="me-3 p-3 rounded" style="background-color: #fae8e8;">
                            <i class="fas fa-box-open" style="color: #d9534f; font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold" style="color: #2c3e50;">{{ $stats['total_items'] - $stats['available_items'] }}</h4>
                            <span class="text-muted" style="font-size: 0.8rem;">Out of Stock</span>
                        </div>
                        <i class="fas fa-info-circle text-muted" style="position: absolute; bottom: 8px; right: 10px; cursor: pointer; font-size: 0.9rem;" title="Includes loaned, missing, or maintenance items"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- No of users -->
            <div class="col-md-3 d-flex">
                <div class="custom-card w-100 p-4 d-flex flex-column justify-content-center align-items-start">
                    <div class="d-flex w-100 justify-content-between mb-4">
                        <h6 class="card-title mb-0">No of users</h6>
                        <i class="fas fa-ellipsis-v text-muted" style="cursor: pointer;"></i>
                    </div>
                    <div class="p-3 rounded mb-4" style="background-color: #d6eef0;">
                         <i class="fas fa-users" style="color: #2C98A0; font-size: 1.8rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #2c3e50;">{{ $stats['total_users'] }} K</h3>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Total Customers</p>
                </div>
            </div>

            <!-- Inventory Values -->
            <div class="col-md-4 d-flex">
                <div class="custom-card w-100 p-4">
                    <h6 class="card-title">Inventory Values</h6>
                    <div class="d-flex align-items-center justify-content-around mt-4">
                        <div style="width: 140px; height: 140px;">
                            <canvas id="inventoryValuesChart"></canvas>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center">
                                <span style="width: 16px; height: 16px; background-color: #d6eef0; display: inline-block; margin-right: 12px; border-radius: 4px;"></span>
                                <span class="text-muted" style="font-size: 0.85rem;">Sold units</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span style="width: 16px; height: 16px; background-color: #4b848c; display: inline-block; margin-right: 12px; border-radius: 4px;"></span>
                                <span class="text-muted" style="font-size: 0.85rem;">Total units</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 10 Stores by sales (Top Borrowed Items) -->
            <div class="col-md-5 d-flex">
                <div class="custom-card w-100 p-4">
                    <h6 class="card-title">Top 10 Stores by sales</h6>
                    <div class="mt-3">
                        @foreach($topItems->take(8) as $item)
                        <div class="mb-2 d-flex align-items-center">
                            <span class="text-muted text-truncate me-2" style="font-size: 0.75rem; width: 35%;">{{ $item->nama_barang }}</span>
                            <div class="progress flex-grow-1" style="height: 10px; border-radius: 5px; background-color: transparent;">
                                @php 
                                    $maxLoans = $topItems->first()->loans_count ?? 1;
                                    $percentage = ($item->loans_count / max($maxLoans, 1)) * 100;
                                @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%; background-color: #5c848e; border-radius: 5px;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="text-muted ms-2" style="font-size: 0.75rem; width: 35px; text-align: right;">{{ $item->loans_count }}k</span>
                        </div>
                        @endforeach
                        @if($topItems->isEmpty())
                            <p class="text-muted text-center" style="font-size: 0.85rem;">No data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Expense vs Profit (Loans Over Time) -->
            <div class="col-md-12">
                <div class="custom-card w-100 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="card-title mb-0">Expense vs Profit</h6>
                        <span class="text-muted" style="font-size: 0.85rem;">Last 6 months</span>
                    </div>
                    <div style="height: 250px; width: 100%;">
                        <canvas id="loansOverTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- User Dashboard (Retaining original user view for now, styling it lightly) -->
        <h5 class="mb-3 font-weight-bold" style="color: var(--text-dark);">Peminjaman Saya</h5>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="custom-card">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold" style="color: #2C98A0;">{{ $stats['active_loans'] }}</h2>
                        <p class="text-muted mb-0">Sedang Dipinjam</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="custom-card">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold" style="color: #4CC8A3;">{{ $stats['total_loans'] }}</h2>
                        <p class="text-muted mb-0">Total Peminjaman</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="custom-card">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold text-danger">{{ $stats['overdue_loans'] ?? 0 }}</h2>
                        <p class="text-muted mb-0">Terlambat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Active Loans Table -->
        <div class="row">
            <div class="col-12">
                <div class="custom-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="card-title mb-0">Peminjaman Aktif Saya</h6>
                        <a href="{{ route('items.index') }}" class="btn btn-sm text-white" style="background-color: var(--primary-color);">
                            <i class="fas fa-plus"></i> Pinjam Barang
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover">
                            <thead class="text-muted" style="border-bottom: 2px solid #EAF4F6;">
                                <tr>
                                    <th>Kode Peminjaman</th>
                                    <th>Barang</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Batas Kembali</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeLoans as $loan)
                                <tr>
                                    <td><strong>{{ $loan->kode_peminjaman }}</strong></td>
                                    <td>{{ $loan->item->nama_barang }}</td>
                                    <td>{{ $loan->tanggal_pinjam->format('d/m/Y') }}</td>
                                    <td>
                                        @if($loan->isReturnSoon())
                                            <span class="badge bg-danger rounded-pill">{{ $loan->daysUntilReturn() }} hari</span>
                                        @else
                                            <span class="text-muted">{{ $loan->tanggal_kembali_rencana->format('d/m/Y') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-light text-primary border rounded-pill px-3">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Anda tidak memiliki peminjaman aktif</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(auth()->user()->hasRole('admin'))
        
        // 1. Inventory Values Pie Chart
        const ctxPie = document.getElementById('inventoryValuesChart').getContext('2d');
        const availableItems = {{ $stats['available_items'] }};
        const totalItems = {{ $stats['total_items'] }};
        const remainingItems = totalItems - availableItems;
        const availablePercent = totalItems > 0 ? Math.round((availableItems / totalItems) * 100) : 0;
        const remainingPercent = 100 - availablePercent;

        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Sold units', 'Total units'],
                datasets: [{
                    data: [remainingPercent, availablePercent],
                    backgroundColor: ['#d6eef0', '#4b848c'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + '%';
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'textCenter',
                beforeDraw: function(chart) {
                    var width = chart.width,
                        height = chart.height,
                        ctx = chart.ctx;

                    ctx.restore();
                    var fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em Inter";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#fff";

                    var text = availablePercent + "%",
                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                        textY = height / 2;

                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }]
        });

        // 2. Loans Over Time Area Chart
        const ctxLine = document.getElementById('loansOverTimeChart').getContext('2d');
        const monthlyDataRaw = {!! json_encode($monthlyStats) !!};
        
        // Map to display Last 6 Months like mock
        let labels = monthlyDataRaw.map(item => new Date(item.date).toLocaleString('default', { month: 'short' }));
        let dataValues1 = monthlyDataRaw.map(item => item.total * 3000); // multiplied for mock scale
        let dataValues2 = monthlyDataRaw.map(item => (item.total + 2) * 2000);

        if(labels.length === 0) {
            labels = ['Dec', 'Jan', 'Feb', 'Mar', 'April', 'May', 'Jun'];
            dataValues1 = [25000, 18000, 22000, 31000, 24000, 39000, 41000];
            dataValues2 = [15000, 24000, 20000, 26000, 30000, 24000, 28000];
        }

        // Gradients
        let gradient1 = ctxLine.createLinearGradient(0, 0, 0, 400);
        gradient1.addColorStop(0, 'rgba(76, 200, 163, 0.2)');
        gradient1.addColorStop(1, 'rgba(76, 200, 163, 0.0)');

        let gradient2 = ctxLine.createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, 'rgba(234, 184, 184, 0.2)');
        gradient2.addColorStop(1, 'rgba(234, 184, 184, 0.0)');

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Highest Profit',
                        data: dataValues1,
                        borderColor: '#4CC8A3',
                        backgroundColor: gradient1,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4CC8A3',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 3
                    },
                    {
                        label: 'Highest Expense',
                        data: dataValues2,
                        borderColor: '#eab8b8',
                        backgroundColor: gradient2,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#eab8b8',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        borderDash: [5, 5]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: { size: 11 }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f4f4f4',
                            drawBorder: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#6c757d',
                            callback: function(value) {
                                return value/1000 + 'k';
                            },
                            font: { size: 11 }
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    @endif
});
</script>
@endpush
