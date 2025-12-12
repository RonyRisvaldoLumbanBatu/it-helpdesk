<?php
// views/partials/reports.php
require_once __DIR__ . '/../../src/Database.php';

$pdo = Database::getInstance();

// 1. Get Stats by Status
$stmtStatus = $pdo->query("SELECT status, COUNT(*) as count FROM tickets GROUP BY status");
$statusData = $stmtStatus->fetchAll(PDO::FETCH_KEY_PAIR); // ['pending' => 5, 'resolved' => 10]

// Normalize data ensure all keys exist
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

<div style="padding-bottom: 50px;">
    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 1.5rem; color: var(--text-main); font-weight: 700;">Laporan & Statistik</h2>
        <p style="color: var(--text-muted);">Analisis performa layanan IT Helpdesk dalam 7 hari terakhir.</p>
    </div>

    <!-- Charts Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">

        <!-- Status Chart -->
        <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: var(--text-main);">Distribusi
                Status Tiket</h3>
            <div style="height: 300px; display: flex; justify-content: center;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Trend Chart -->
        <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: var(--text-main);">Tren Tiket
                Masuk (7 Hari)</h3>
            <div style="height: 300px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Summary Table (Mini) -->
    <div
        style="margin-top: 24px; background: white; border: 1px solid var(--border); border-radius: 12px; padding: 24px;">
        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 15px; color: var(--text-main);">Ringkasan Singkat
        </h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
                    <th style="padding: 12px;">Metrik</th>
                    <th style="padding: 12px;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px; color: var(--text-muted);">Total Tiket (Semua Waktu)</td>
                    <td style="padding: 12px; font-weight: bold; font-size: 1.1rem;"><?php echo array_sum($counts); ?>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px; color: var(--text-muted);">Tingkat Penyelesaian (Resolved / Total)</td>
                    <td style="padding: 12px; font-weight: bold; color: #15803d;">
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

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Data from PHP
    const statusLabels = <?php echo json_encode(array_map('ucfirst', array_map(function ($s) {
        return str_replace('_', ' ', $s); }, $statuses))); ?>;
    const statusData = <?php echo json_encode($counts); ?>;

    const dateLabels = <?php echo json_encode($dates); ?>;
    const trendData = <?php echo json_encode($dailyCounts); ?>;

    // 1. Status Chart (Doughnut)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: [
                    '#fbbf24', // Pending (Orange/Yellow)
                    '#3b82f6', // In Progress (Blue)
                    '#22c55e', // Resolved (Green)
                    '#ef4444'  // Rejected (Red)
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // 2. Trend Chart (Line)
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
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>