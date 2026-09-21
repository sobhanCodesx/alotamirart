<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحه مورد نظر پیدا نشد - 404</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Vazirmatn', 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #0a1628, #1a2a4a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: rtl;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            border-radius: 30px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
            border: 2px solid #facc15;
            position: relative;
            overflow: hidden;
        }
        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 70% 30%, rgba(250, 204, 21, 0.03), transparent 60%);
            pointer-events: none;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 10px;
            letter-spacing: -5px;
        }
        .error-code span {
            color: #facc15;
        }
        .error-icon {
            font-size: 5rem;
            display: block;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }
        p {
            font-size: 1.1rem;
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .btn {
            padding: 12px 32px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #ffffff;
            border: 2px solid #0f172a;
        }
        .btn-primary:hover {
            background: #facc15;
            color: #0f172a;
            border-color: #facc15;
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(250, 204, 21, 0.3);
        }
        .btn-secondary {
            background: transparent;
            color: #0f172a;
            border: 2px solid #e9edf2;
        }
        .btn-secondary:hover {
            border-color: #facc15;
            background: #fef9e7;
            transform: translateY(-3px);
        }
        .search-box {
            max-width: 400px;
            margin: 20px auto 0;
            display: flex;
            background: #f8fafc;
            border-radius: 60px;
            overflow: hidden;
            border: 2px solid #e9edf2;
            transition: all 0.3s ease;
        }
        .search-box:focus-within {
            border-color: #facc15;
            box-shadow: 0 4px 30px rgba(250, 204, 21, 0.1);
        }
        .search-box input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            outline: none;
            font-size: 1rem;
            background: transparent;
            color: #1e293b;
            font-family: inherit;
        }
        .search-box button {
            padding: 12px 24px;
            background: #0f172a;
            border: none;
            color: #ffffff;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
        }
        .search-box button:hover {
            background: #facc15;
            color: #0f172a;
        }
        
        @media (max-width: 480px) {
            .container { padding: 30px 20px; }
            .error-code { font-size: 5rem; }
            .error-icon { font-size: 3.5rem; }
            h1 { font-size: 1.5rem; }
            .btn-group { flex-direction: column; }
            .btn-group .btn { justify-content: center; }
        }
    </style>
</head>
<body>

<div class="container">
    <span class="error-icon">😕</span>
    <div class="error-code">4<span>0</span>4</div>
    <h1>⛔ صفحه مورد نظر پیدا نشد</h1>
    <p>
        متأسفیم، صفحه‌ای که به دنبال آن هستید وجود ندارد یا آدرس آن تغییر کرده است.
        <br>
        لطفاً آدرس را بررسی کنید یا به صفحه اصلی بازگردید.
    </p>

    <div class="search-box">
        <form action="/search/1" method="post">
            <input type="text" name="search" placeholder="جستجو در سایت...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="btn-group">
        <a href="/" class="btn btn-primary">
            <i class="fas fa-home"></i> بازگشت به صفحه اصلی
        </a>
        <a href="/cities" class="btn btn-secondary">
            <i class="fas fa-map-marker-alt"></i> مشاهده شهرها
        </a>
    </div>
</div>

<!-- ===== Font Awesome ===== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</body>
</html>