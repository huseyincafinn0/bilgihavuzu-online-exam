<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-header">
        <i class="fa-solid fa-paw fa-2x text-teal" style="color: #14b8a6;"></i>
        <h3>Yönetim Paneli</h3>
    </div>

    <div class="profile-widget">
        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['full_name'] ?? 'Admin') ?>&background=14b8a6&color=fff" alt="Profile">
        <div class="profile-info">
            <span>Hoşgeldin</span>
            <h4><?= htmlspecialchars($_SESSION['full_name'] ?? 'yönetici') ?></h4>
        </div>
    </div>

    <ul class="list-unstyled components">
        <p>Genel</p>
        
        <li class="active">
            <a href="index.php"><i class="fa-solid fa-house"></i> Anasayfa (Dashboard)</a>
        </li>
        <li>
            <a href="exam_types.php"><i class="fa-solid fa-layer-group"></i> Sınav Türleri</a>
        </li>
        <li>
            <a href="exam_sections.php"><i class="fa-solid fa-puzzle-piece"></i> Alt Sınavlar</a>
        </li>
        <li>
            <a href="lessons.php"><i class="fa-solid fa-book"></i> Dersler</a>
        </li>
        <li>
            <a href="topics.php"><i class="fa-solid fa-bookmark"></i> Konular</a>
        </li>
        
        <!-- Collapsible Menu -->
        <li>
            <a href="#sorularSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fa-solid fa-circle-question"></i> Sorular
            </a>
            <ul class="collapse list-unstyled" id="sorularSubmenu">
                <li>
                    <a href="questions.php"><i class="fa-solid fa-list-ul"></i> Soru Listesi</a>
                </li>
                <li>
                    <a href="question_add.php"><i class="fa-solid fa-plus"></i> Soru Ekle</a>
                </li>
                <li>
                    <a href="question_import.php"><i class="fa-solid fa-file-csv"></i> Toplu Yükle (CSV)</a>
                </li>
            </ul>
        </li>

        <li>
            <a href="users.php"><i class="fa-solid fa-users"></i> Kullanıcılar</a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="#"><i class="fa-solid fa-gear"></i></a>
        <a href="#"><i class="fa-solid fa-expand"></i></a>
        <a href="../logout.php"><i class="fa-solid fa-power-off text-danger"></i></a>
    </div>
</nav>

<!-- Page Content -->
<div id="content">
    
    <!-- Topbar -->
    <div class="topbar">
        <div class="nav-left">
            <button type="button" id="sidebarCollapse" class="btn-menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="search-bar d-none d-md-block">
                <input type="text" placeholder="Ara...">
            </div>
        </div>
        <div class="nav-right">
            <a href="#" class="notification-bell">
                <i class="fa-regular fa-bell"></i>
                <span class="badge">6</span>
            </a>
            
            <div class="dropdown">
                <a href="#" class="topbar-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['full_name'] ?? 'Admin') ?>&background=14b8a6&color=fff" alt="User">
                    <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Sistem Yöneticisi') ?> <i class="fa-solid fa-angle-down ms-1"></i></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="../dashboard.php"><i class="fa-solid fa-arrow-left me-2"></i> Siteye Dön</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fa-solid fa-sign-out-alt me-2"></i> Çıkış Yap</a></li>
                </ul>
            </div>
        </div>
    </div>
