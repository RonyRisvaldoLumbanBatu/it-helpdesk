<?php
// views/partials/reports.php
require_once __DIR__ . '/../../src/Database.php';

$pdo = Database::getInstance();

// 1. Get Stats by Status
$stmtStatus = $pdo->query("SELECT status, COUNT(*) as count FROM tickets GROUP BY status");
$statusData = $stmtStatus->fetchAll(PDO::FETCH_KEY_PAIR); // ['pending' => 5, 'resolved' => 10]

// Normalize data: ensure all status keys exist with 0 if missing
$statuses = ['pending', 'in_progress', 'resolved', 'rejected'];
$counts = [];
foreach ($statuses as $s) {
    $counts[] = $statusData[$s] ?? 0;
}

// 2. Get Stats by Last 7 Days
$dates = [];
$dailyCounts = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $displayDate = date('d M', strtotime("-$i days"));

    $stmtDate = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE DATE(created_at) = :d");
    $stmtDate->execute(['d' => $date]);

    $dates[] = $displayDate;
    $dailyCounts[] = $stmtDate->fetchColumn();
}
?>

<div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; margin-bottom: 30px;">
    <!-- Main Card Header -->
    <div style="padding: 20px 24px; background: var(--primary); border-bottom: 1px solid #e2e8f0;">
        <h2 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: white;">Laporan & Statistik</h2>
        <p style="margin: 4px 0 0 0; color: rgba(255,255,255,0.8); font-size: 0.9rem;">Analisis performa layanan IT Helpdesk dalam 7 hari terakhir.</p>
    </div>

    <!-- Main Card Body -->
    <div style="padding: 24px;">

        <!-- Charts Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; margin-bottom: 30px;">
            <!-- Status Chart Box -->
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: var(--primary);">
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: white;">Distribusi Status Tiket</h3>
                </div>
                <div style="padding: 20px; display: flex; justify-content: center; height: 350px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Trend Chart Box -->
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: var(--primary);">
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: white;">Tren Tiket Masuk (7 Hari)</h3>
                </div>
                <div style="padding: 20px; height: 350px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Summary Table Box -->
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: var(--primary);">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: white;">Ringkasan Singkat</h3>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; font-weight: 600; font-size: 0.85rem; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Metrik</th>
                        <th style="padding: 12px 20px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#f8fafc'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';"
                        onmouseout="this.style.background='white'; this.style.transform='none'; this.style.boxShadow='none';">
                        <td style="padding: 16px 20px; color: #334155; font-size: 0.95rem;">Total Tiket (Semua Waktu)</td>
                        <td style="padding: 16px 20px; font-weight: 700; font-size: 1.1rem; color: #0f172a; text-align: right;">
                            <?php echo array_sum($counts); ?>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#f8fafc'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';"
                        onmouseout="this.style.background='white'; this.style.transform='none'; this.style.boxShadow='none';">
                        <td style="padding: 16px 20px; color: #334155; font-size: 0.95rem;">Tingkat Penyelesaian (Resolved / Total)</td>
                        <td style="padding: 16px 20px; font-weight: 700; color: #15803d; text-align: right; font-size: 1rem;">
                            <?php
                            $total = array_sum($counts);
                            $resolved = $statusData['resolved'] ?? 0;
                            echo $total > 0 ? round(($resolved / $total) * 100, 1) . '%' : '0%';
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Chart.js Library (Using v3.9.1 for stability) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- 1. Prepare Data ---
        const statusLabels = <?php echo json_encode(array_map('ucfirst', array_map(function ($s) {
            return str_replace('_', ' ', $s);
        }, $statuses))); ?>;
        const statusData = <?php echo json_encode($counts); ?>;

        const dateLabels = <?php echo json_encode($dates); ?>;
        const trendData = <?php echo json_encode($dailyCounts); ?>;

        // --- 2. Utils ---
        function destroyChart(id) {
            const chartElement = document.getElementById(id);
            if (chartElement) {
                const chartInstance = Chart.getChart(chartElement);
                if (chartInstance) {
                    chartInstance.destroy();
                }
            }
        }

        // --- 3. Status Chart (Doughnut) ---
        destroyChart('statusChart');
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: [
                        '#fbbf24', // Pending
                        '#3b82f6', // In Progress
                        '#22c55e', // Resolved
                        '#ef4444'  // Rejected
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20 }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 2000,
                    easing: 'easeOutQuart'
                },
                cutout: '60%',
            }
        });

        // --- 4. Trend Chart (Line) ---
        destroyChart('trendChart');
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: dateLabels,
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: trendData,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { display: true, color: '#e2e8f0' }
                    },
                    x: {
                        grid: { display: true, color: '#e2e8f0' }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 13 },
                        bodyFont: { size: 13 },
                        displayColors: false
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });
    });
</script>