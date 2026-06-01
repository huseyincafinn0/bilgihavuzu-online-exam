<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Illustrative Data
$stats = [
    ['title' => 'Toplam Sorular', 'value' => '2500', 'trend' => '4%', 'trend_up' => true, 'icon' => 'fa-clipboard-question', 'color' => 'teal'],
    ['title' => 'Ortalama Çözüm Süresi', 'value' => '123.50 sn', 'trend' => '3%', 'trend_up' => true, 'icon' => 'fa-clock', 'color' => 'blue'],
    ['title' => 'Toplam Sınav', 'value' => '2,500', 'trend' => '34%', 'trend_up' => true, 'icon' => 'fa-file-lines', 'color' => 'teal'],
    ['title' => 'Sınav Başarı Oranı', 'value' => '%72.4', 'trend' => '12%', 'trend_up' => false, 'icon' => 'fa-chart-line', 'color' => 'purple'],
    ['title' => 'Toplam Çözülen Soru', 'value' => '2,315', 'trend' => '34%', 'trend_up' => true, 'icon' => 'fa-check-double', 'color' => 'teal'],
    ['title' => 'Yeni Kullanıcı', 'value' => '7,325', 'trend' => '34%', 'trend_up' => true, 'icon' => 'fa-users', 'color' => 'blue']
];
?>

<div class="main-content">
    
    <!-- Stat Cards -->
    <div class="row mb-4">
        <?php foreach($stats as $s): ?>
        <div class="col-md-4 col-sm-6 col-12">
            <div class="admin-card stat-card">
                <div>
                    <div class="stat-label"><i class="fa-solid <?= $s['icon'] ?> me-2"></i> <?= $s['title'] ?></div>
                    <div class="stat-value"><?= $s['value'] ?></div>
                    <div class="stat-trend <?= $s['trend_up'] ? 'trend-up' : 'trend-down' ?>">
                        <i class="fa-solid <?= $s['trend_up'] ? 'fa-caret-up' : 'fa-caret-down' ?>"></i> <?= $s['trend'] ?> Geçen Haftaya Göre
                    </div>
                </div>
                <div class="stat-icon icon-<?= $s['color'] ?>">
                    <i class="fa-solid <?= $s['icon'] ?>"></i>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Main Charts -->
    <div class="row">
        <!-- Area Chart -->
        <div class="col-md-8">
            <div class="admin-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title m-0 border-0">Platform Aktiviteleri <span class="text-muted fs-6 fw-normal">Soru & Sınav Yoğunluğu</span></h5>
                    <select class="form-select form-select-sm" style="width: 200px;">
                        <option>14 Nisan 2026 - 13 Mayıs 2026</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="mainAreaChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Progress Bars -->
        <div class="col-md-4">
            <div class="admin-card">
                <h5 class="card-title">En İyi Sınav Performansları</h5>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium text-white">TYT Deneme Sınavları</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium text-white">AYT Deneme Sınavları</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-purple" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium text-white">KPSS Lisans</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium text-white">LGS Denemeleri</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-purple" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row Cards -->
    <div class="row">
        <!-- Lessons Distribution -->
        <div class="col-md-4">
            <div class="admin-card">
                <h5 class="card-title">Soru Dağılımı (Dersler)</h5>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="w-25 text-muted">Matematik</div>
                    <div class="w-50 mx-2">
                        <div class="progress" style="height: 15px;">
                            <div class="progress-bar" style="width: 85%;"></div>
                        </div>
                    </div>
                    <div class="w-25 text-end fw-bold text-white">123k</div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="w-25 text-muted">Fizik</div>
                    <div class="w-50 mx-2">
                        <div class="progress" style="height: 15px;">
                            <div class="progress-bar bg-purple" style="width: 65%;"></div>
                        </div>
                    </div>
                    <div class="w-25 text-end fw-bold text-white">53k</div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="w-25 text-muted">Türkçe</div>
                    <div class="w-50 mx-2">
                        <div class="progress" style="height: 15px;">
                            <div class="progress-bar" style="width: 45%;"></div>
                        </div>
                    </div>
                    <div class="w-25 text-end fw-bold text-white">23k</div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-md-4">
            <div class="admin-card">
                <h5 class="card-title">Sınav Kategorileri</h5>
                <div class="chart-container" style="height: 200px;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Settings -->
        <div class="col-md-4">
            <div class="admin-card">
                <h5 class="card-title">Hızlı Ayarlar</h5>
                <ul class="list-unstyled mb-4">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted"><i class="fa-solid fa-gear me-2"></i> Sistem Ayarları</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted"><i class="fa-solid fa-users me-2"></i> Üyelik Yönetimi</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted"><i class="fa-solid fa-trophy me-2"></i> Başarılar</a></li>
                    <li><a href="../logout.php" class="text-decoration-none text-danger"><i class="fa-solid fa-power-off me-2"></i> Çıkış Yap</a></li>
                </ul>
                <div class="text-center mt-3 p-3 rounded" style="background: rgba(20,184,166,0.1);">
                    <h6 class="fw-bold" style="color: var(--teal);">Sistem Sağlığı</h6>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Sorular</span>
                        <span class="fw-bold">100%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Area Chart Setup
    const ctxArea = document.getElementById('mainAreaChart').getContext('2d');
    
    // Create Gradients
    let gradientTeal = ctxArea.createLinearGradient(0, 0, 0, 400);
    gradientTeal.addColorStop(0, 'rgba(0, 242, 254, 0.4)');
    gradientTeal.addColorStop(1, 'rgba(0, 242, 254, 0.0)');
    
    let gradientPurple = ctxArea.createLinearGradient(0, 0, 0, 400);
    gradientPurple.addColorStop(0, 'rgba(243, 85, 218, 0.4)');
    gradientPurple.addColorStop(1, 'rgba(243, 85, 218, 0.0)');

    new Chart(ctxArea, {
        type: 'line',
        data: {
            labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
            datasets: [{
                label: 'Soru Çözümleri',
                data: [20, 50, 30, 80, 40, 90],
                backgroundColor: gradientTeal,
                borderColor: '#00f2fe',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#00f2fe',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }, {
                label: 'Sınav Geri Dönüşleri',
                data: [10, 30, 60, 40, 70, 50],
                backgroundColor: gradientPurple,
                borderColor: '#f355da',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#f355da',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { 
                        color: 'rgba(255, 255, 255, 0.05)',
                        borderDash: [5, 5] 
                    },
                    ticks: { color: '#a0aec0' }
                },
                x: { 
                    grid: { display: false },
                    ticks: { color: '#a0aec0' }
                }
            }
        }
    });

    // Pie Chart Setup
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['YKS', 'LGS', 'KPSS', 'DGS'],
            datasets: [{
                data: [40, 25, 20, 15],
                backgroundColor: ['#00f2fe', '#f355da', '#9d4edd', '#3b82f6'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { 
                    position: 'right',
                    labels: { color: '#ffffff' }
                }
            }
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
