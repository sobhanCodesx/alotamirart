<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="ROBOTS" content="noindex,nofollow">
    <title>پنل مدیریت | ویرایش پست</title>
    <?php include BASE_PATH . "/them/admin/layout/head.php"; ?>
    
    <style>
        /* ===== استایل طلایی-آبی ===== */
        body {
            background: #0a0e27 !important;
        }
        
        .content-wrapper {
            background: linear-gradient(135deg, #0a0e27 0%, #1a1a3e 50%, #0d1b2a 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        /* ===== کارت جستجو ===== */
        .search-card {
            background: rgba(10, 14, 39, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid #FFD700;
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.2), inset 0 0 50px rgba(0, 100, 255, 0.05);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .search-input {
            background: rgba(10, 14, 39, 0.8) !important;
            color: #FFD700 !important;
            border: 2px solid #FFD700 !important;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.1) !important;
        }
        
        .search-input::placeholder {
            color: #666 !important;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #FFD700, #FFA500) !important;
            color: #0a0e27 !important;
            border: none !important;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3) !important;
        }
        
        .search-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.5) !important;
        }
        
        /* ===== برگه‌ها ===== */
        .nav-tabs {
            border-bottom: 2px solid #FFD700 !important;
        }
        
        .nav-tabs .nav-link {
            border: none !important;
            border-radius: 10px 10px 0 0 !important;
            padding: 10px 20px !important;
            font-weight: 700 !important;
            transition: all 0.3s ease !important;
            color: #FFD700 !important;
        }
        
        .nav-tabs .nav-link:hover {
            background: rgba(255, 215, 0, 0.1) !important;
        }
        
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #FFD700, #FFA500) !important;
            color: #0a0e27 !important;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.2) !important;
        }
        
        .badge-gold {
            background: #0a0e27;
            color: #FFD700;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
        }
        
        .badge-red {
            background: #8B0000;
            color: #FFD700;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
        }
        
        .badge-blue {
            background: #4A90D9;
            color: #0a0e27;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
        }
        
        /* ===== جدول ===== */
        .table-gold {
            background: rgba(10, 14, 39, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid #FFD700;
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.2), inset 0 0 50px rgba(0, 100, 255, 0.05);
            border-radius: 15px;
            overflow: hidden;
            color: #E0E0E0 !important;
        }
        
        .table-gold thead {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(0, 100, 255, 0.15), rgba(255, 215, 0, 0.15));
            border-bottom: 3px solid #FFD700;
        }
        
        .table-gold thead th {
            color: #FFD700 !important;
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
            font-size: 16px !important;
            padding: 15px 10px !important;
            border-bottom: none !important;
        }
        
        .table-gold tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 215, 0, 0.1);
        }
        
        .table-gold tbody tr:hover {
            background: rgba(255, 215, 0, 0.05);
            transform: scale(1.01);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.1);
        }
        
        .table-gold tbody td {
            padding: 12px 10px !important;
            vertical-align: middle !important;
            color: #E0E0E0 !important;
        }
        
        .row-number {
            color: #FFD700 !important;
            font-weight: 900 !important;
            font-size: 18px !important;
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }
        
        .post-title {
            color: #E0E0E0 !important;
            font-weight: 500 !important;
        }
        
        .post-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 2px solid #FFD700;
            border-radius: 8px;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3), 0 0 60px rgba(0, 100, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .post-img:hover {
            transform: scale(1.8);
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.5), 0 0 100px rgba(0, 100, 255, 0.3);
            border: 3px solid #4A90D9;
            z-index: 999;
            position: relative;
        }
        
        .post-img-inactive {
            filter: grayscale(0.5);
        }
        
        .author-name {
            color: #4A90D9 !important;
            font-weight: 600 !important;
            text-shadow: 0 0 20px rgba(74, 144, 217, 0.3);
        }
        
        /* ===== دکمه‌ها ===== */
        .btn-status-active {
            background: linear-gradient(135deg, #8B0000, #440000) !important;
            color: #FFD700 !important;
            border: 1px solid #FFD700 !important;
            box-shadow: 0 0 20px rgba(139, 0, 0, 0.3) !important;
            padding: 5px 15px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
        }
        
        .btn-status-active:hover {
            transform: scale(1.05);
            box-shadow: 0 0 40px rgba(139, 0, 0, 0.5) !important;
        }
        
        .btn-status-inactive {
            background: linear-gradient(135deg, #FFD700, #FFA500) !important;
            color: #0a0e27 !important;
            border: 1px solid #FFD700 !important;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3) !important;
            padding: 5px 15px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
        }
        
        .btn-status-inactive:hover {
            transform: scale(1.05);
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.5) !important;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #1a1a3e, #0a0e27) !important;
            color: #FFD700 !important;
            border: 2px solid #FFD700 !important;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.1) !important;
            padding: 5px 15px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-edit:hover {
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.3), 0 0 100px rgba(0, 100, 255, 0.2) !important;
            transform: translateY(-2px) !important;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #440000, #1a0000) !important;
            color: #FF4444 !important;
            border: 2px solid #FF4444 !important;
            box-shadow: 0 0 30px rgba(255, 0, 0, 0.1) !important;
            padding: 5px 15px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-delete:hover {
            box-shadow: 0 0 50px rgba(255, 0, 0, 0.3) !important;
            transform: translateY(-2px) !important;
        }
        
        /* ===== پیجینیشن ===== */
        .pagination-wrapper {
            background: rgba(10, 14, 39, 0.8);
            backdrop-filter: blur(10px);
            padding: 15px 25px;
            border-radius: 15px;
            border: 2px solid #FFD700;
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.1), inset 0 0 50px rgba(0, 100, 255, 0.05);
            display: inline-block;
        }
        
        .pagination .page-item .page-link {
            background: rgba(10, 14, 39, 0.8) !important;
            border: 2px solid #FFD700 !important;
            color: #FFD700 !important;
            margin: 0 5px !important;
            border-radius: 10px !important;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.1) !important;
            transition: all 0.3s ease !important;
            padding: 8px 16px !important;
        }
        
        .pagination .page-item .page-link:hover {
            background: linear-gradient(135deg, #FFD700, #FFA500) !important;
            color: #0a0e27 !important;
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.3) !important;
            transform: translateY(-3px) scale(1.05) !important;
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #FFD700, #FFA500) !important;
            border-color: #FFD700 !important;
            color: #0a0e27 !important;
            font-weight: 900 !important;
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.3) !important;
            transform: scale(1.1) !important;
        }
        
        .pagination .page-item.disabled .page-link {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }
        
        /* ===== اسکرول ===== */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        ::-webkit-scrollbar-track {
            background: linear-gradient(180deg, #0a0e27, #1a1a3e);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #FFD700, #FFA500);
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #4A90D9, #FFD700);
            box-shadow: 0 0 50px rgba(74, 144, 217, 0.5);
        }
        
        /* ===== انیمیشن ===== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-animated {
            animation: fadeIn 0.5s ease;
        }
        
        /* ===== ریسپانسیو ===== */
        @media (max-width: 768px) {
            .table-gold {
                font-size: 12px !important;
            }
            .table-gold thead th,
            .table-gold tbody td {
                padding: 8px 5px !important;
            }
            .post-img {
                width: 40px;
                height: 40px;
            }
            .btn {
                font-size: 10px !important;
                padding: 3px 8px !important;
            }
            .nav-tabs .nav-link {
                font-size: 12px !important;
                padding: 5px 10px !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include BASE_PATH . '/them/admin/layout/nav.php'; ?>
    </div>
    
    <div class="content-wrapper">
        <div class="container-fluid">
            
            <!-- ===== جستجو ===== -->
            <div class="search-card card-animated">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text search-btn">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input type="text" 
                                   id="searchInput" 
                                   class="form-control search-input" 
                                   placeholder="🔍 جستجوی مقاله ... (مثال: آمل، تهران)"
                                   onkeyup="searchTable()">
                            <div class="input-group-append">
                                <button class="btn search-btn" onclick="searchTable()">
                                    <i class="fas fa-arrow-left"></i> جستجو
                                </button>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block" style="color: #4A90D9 !important;">
                            <i class="fas fa-info-circle"></i> با تایپ کلمه، مقالات مرتبط نمایش داده می‌شوند
                        </small>
                    </div>
                    <div class="col-md-4 text-left">
                        <span style="color: #FFD700; text-shadow: 0 0 20px rgba(255, 215, 0, 0.3); font-weight: 700; font-size: 16px;">
                            <i class="fas fa-file-alt"></i> تعداد مقالات: <span id="totalCount">0</span>
                        </span>
                        <br>
                        <button class="btn btn-sm mt-2" style="background: linear-gradient(135deg, #1a1a3e, #0a0e27); color: #4A90D9; border: 2px solid #4A90D9; box-shadow: 0 0 30px rgba(74, 144, 217, 0.1);" onclick="clearSearch()">
                            <i class="fas fa-times-circle"></i> پاک کردن
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- ===== تب‌ها ===== -->
            <ul class="nav nav-tabs mb-3" id="myTab">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-active" onclick="switchTab('active')">
                        <i class="fas fa-check-circle"></i> مقالات فعال
                        <span class="badge-gold" id="activeCount">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-inactive" onclick="switchTab('inactive')">
                        <i class="fas fa-times-circle"></i> مقالات غیرفعال
                        <span class="badge-red" id="inactiveCount">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-all" onclick="switchTab('all')">
                        <i class="fas fa-list"></i> همه
                        <span class="badge-blue" id="allCount">0</span>
                    </a>
                </li>
            </ul>
            
            <!-- ===== جدول ===== -->
            <div class="table-gold card-animated">
                <div class="table-responsive">
                    <table class="table mb-0" id="postTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">عنوان</th>
                                <th scope="col">عکس</th>
                                <th scope="col">نویسنده</th>
                                <th scope="col">وضعیت</th>
                                <th scope="col">عملیات</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php
                            $count = 1;
                            foreach ($post as $c) { 
                                $isActive = ($c['status'] == 1);
                            ?>
                                <tr class="post-row <?= $isActive ? 'active-post' : 'inactive-post' ?>" data-status="<?= $c['status'] ?>">
                                    <th scope="row" class="row-number"><?= $count++ ?></th>
                                    <td class="post-title">
                                        <?php if (!$isActive): ?>
                                            <i class="fas fa-lock" style="color: #8B0000; margin-left: 5px;"></i>
                                        <?php else: ?>
                                            <i class="fas fa-check-circle" style="color: #FFD700; margin-left: 5px;"></i>
                                        <?php endif; ?>
                                        <?= $c['title'] ?>
                                    </td>
                                    <td>
                                        <img src="<?= assets($c['img']) ?>" class="post-img <?= !$isActive ? 'post-img-inactive' : '' ?>">
                                    </td>
                                    <td class="author-name">
                                        <i class="fas fa-user-astronaut"></i> <?= $c['w'] ?>
                                    </td>
                                    <td>
                                        <a class="btn <?= $isActive ? 'btn-status-active' : 'btn-status-inactive' ?>" 
                                           href="<?= assets('change/status/'.$c['id']) ?>">
                                            <i class="fas <?= $isActive ? 'fa-times-circle' : 'fa-check-circle' ?>"></i>
                                            <?= $isActive ? 'غیرفعال کن' : 'فعال کن' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a class="btn btn-edit" href="<?= assets('admin/posts/update/'.$c['id']) ?>">
                                                <i class="fas fa-edit" style="color: #4A90D9;"></i> ویرایش
                                            </a>
                                            <a class="btn btn-delete" href="<?= assets('admin/posts/deleted/'.$c['id']) ?>">
                                                <i class="fas fa-trash"></i> حذف
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- ===== پیجینیشن ===== -->
            <div class="d-flex justify-content-center mt-4">
                <div class="pagination-wrapper">
                    <?php echo get_pag($page, 'admin/posts/index', $pages, ''); ?>
                </div>
            </div>
            
        </div>
    </div>

    <script>
        // ===== دیتاهای مقالات =====
        var allPosts = [];
        var rows = document.querySelectorAll('#tableBody .post-row');
        rows.forEach(function(row) {
            var status = parseInt(row.getAttribute('data-status'));
            var title = row.querySelector('.post-title').textContent.trim();
            var img = row.querySelector('.post-img').getAttribute('src');
            var author = row.querySelector('.author-name').textContent.trim();
            var id = row.querySelector('.btn-edit').getAttribute('href').split('/').pop();
            
            allPosts.push({
                id: id,
                title: title,
                img: img,
                author: author,
                status: status,
                element: row
            });
        });
        
        var currentTab = 'active';
        
        // ===== بروزرسانی تعداد =====
        function updateCounts() {
            var activeCount = allPosts.filter(function(p) { return p.status == 1; }).length;
            var inactiveCount = allPosts.filter(function(p) { return p.status == 0; }).length;
            
            document.getElementById('activeCount').textContent = activeCount;
            document.getElementById('inactiveCount').textContent = inactiveCount;
            document.getElementById('allCount').textContent = allPosts.length;
            document.getElementById('totalCount').textContent = allPosts.length;
        }
        
        // ===== تابع جستجو =====
        function searchTable() {
            var input = document.getElementById('searchInput');
            var filter = input.value.toLowerCase().trim();
            var visibleCount = 0;
            
            allPosts.forEach(function(post) {
                var show = true;
                
                // فیلتر بر اساس تب
                if (currentTab === 'active' && post.status != 1) show = false;
                if (currentTab === 'inactive' && post.status != 0) show = false;
                
                // فیلتر بر اساس جستجو
                if (show && filter !== '') {
                    if (!post.title.toLowerCase().includes(filter)) show = false;
                }
                
                if (show) {
                    post.element.style.display = '';
                    visibleCount++;
                } else {
                    post.element.style.display = 'none';
                }
            });
            
            document.getElementById('totalCount').textContent = visibleCount;
        }
        
        // ===== تابع تعویض تب =====
        function switchTab(tab) {
            currentTab = tab;
            
            var tabActive = document.getElementById('tab-active');
            var tabInactive = document.getElementById('tab-inactive');
            var tabAll = document.getElementById('tab-all');
            
            // ریست کردن همه
            [tabActive, tabInactive, tabAll].forEach(function(el) {
                el.classList.remove('active');
                el.style.background = 'transparent';
                el.style.color = '#FFD700';
            });
            
            // فعال کردن تب انتخاب شده
            if (tab === 'active') {
                tabActive.classList.add('active');
                tabActive.style.background = 'linear-gradient(135deg, #FFD700, #FFA500)';
                tabActive.style.color = '#0a0e27';
            } else if (tab === 'inactive') {
                tabInactive.classList.add('active');
                tabInactive.style.background = 'linear-gradient(135deg, #FFD700, #FFA500)';
                tabInactive.style.color = '#0a0e27';
            } else if (tab === 'all') {
                tabAll.classList.add('active');
                tabAll.style.background = 'linear-gradient(135deg, #4A90D9, #2E6BB0)';
                tabAll.style.color = '#0a0e27';
            }
            
            searchTable();
        }
        
        // ===== پاک کردن جستجو =====
        function clearSearch() {
            document.getElementById('searchInput').value = '';
            searchTable();
        }
        
        // ===== اجرا در ابتدا =====
        window.onload = function() {
            updateCounts();
            switchTab('active');
            
            // جستجو با Enter
            document.getElementById('searchInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchTable();
                }
            });
        };
    </script>

</body>
<?php require_once BASE_PATH . '/them/admin/layout/js.php'; ?>

</html>